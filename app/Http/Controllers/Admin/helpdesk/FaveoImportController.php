<?php


namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportFileRequest;
use App\Http\Requests\ImportMappingRequest;
use App\ImportProcessor;
use App\Jobs\ImportJob;
use App\Jobs\Notifications as NotifyQueue;
use App\Jobs\SendImportNotificationJob;
use App\Model\helpdesk\Import\Import;
use App\Traits\UserImport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Storage;

/*
 * Handles Excel Import
 */
class FaveoImportController extends Controller
{
    /**
     * Displays Form for uploading and importing
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function importForm()
    {
        return view('themes.default1.admin.helpdesk.import.upload');
    }

    /**
     * @param ImportFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function processImportFile(ImportFileRequest $request)
    {
        $file = $request->file('import_file');

        /*
         * The following code block is used instead of $request->file('file_name')->store('directory')
         * because laravel stores csv file as .txt extension which makes hard for csv file reader to properly read
         */
        $filenameStore = uniqid().'.'.$file->getClientOriginalExtension();
        Storage::disk('local')->putFileAs('/imports/', $file, $filenameStore); //storage/app/attachments/imports/

        $fileName = $file->getRealPath();

        try {
            $reader = $this->getSpreadSheetReader($fileName);
        } catch (\Exception $e) {
            return errorResponse(trans('lang.importer_reader_error'));
        }

        $filterSubset = new SpreadSheetReadFilterController(0, 1);

        $reader->setReadFilter($filterSubset);

        $spreadSheet = $reader->load($fileName);

        $spreadSheetData = $spreadSheet->getActiveSheet()->toArray();

        $created = Import::create(
            ['path' => $filenameStore, 'columns' => json_encode(array_filter(array_pop($spreadSheetData)))]
        );

        return ($created)
            ? successResponse(trans('lang.importer_file_uploaded_success'),['import_id' => $created->id])
            : errorResponse(trans('lang.importer_file_upload_fail'));
    }

    /**
     * returns faveo and third party attributes for mapping columns from spreadsheet
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetailsForProcessing(Request $request)
    {
        $returnArray = [];
        
        $import = Import::find($request->import_id);

        if ($import) {

            $columns = json_decode($import->columns, true);

            $returnArray['faveo_attributes'] = (new ImportProcessor)->getFaveoAttributes();

            $thirdPartyAtributes = (new Collection($columns))->map(function ($element) {
                $attributeObject = (object)[];
                $attributeObject->id = $element;
                $attributeObject->name = $element;
                $attributeObject->is_loginable = true;
                return $attributeObject;
            });

            $thirdPartyAtributes->push((object) ['id' => 'Do not Import', 'name' => 'Do not Import', 'is_loginable' => true]);

            $returnArray['third_party_attributes'] = $thirdPartyAtributes;

            return successResponse('', $returnArray);
        }
        return errorResponse(trans('lang.importer_import_error'));
    }

    /**
     * @param $filePathName String full path name of file to read
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function getSpreadSheetReader($filePathName)
    {
        $inputFileType = IOFactory::identify($filePathName);

        $reader = IOFactory::createReader($inputFileType);

        $reader->setReadDataOnly(true);

        return $reader;
    }

    public function postProcessingAttributes(ImportMappingRequest $request)
    {
        $mappings = $request->faveo_attributes;

        $import = Import::find($request->import_id);

        if ($import) {
            $fileName = $import->path;

            $filePath = storage_path().DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."attachments".DIRECTORY_SEPARATOR."imports".DIRECTORY_SEPARATOR.$fileName;

            try {
                $reader = $this->getSpreadSheetReader($filePath);
            } catch (\Exception $e) {
                return errorResponse(trans('lang.importer_reader_error'));
            }

            (new PhpMailController)->setQueue();

            ImportJob::withChain([
                new SendImportNotificationJob([
                   'message' => trans('lang.importer_job_success'),
                   'to'      => \Auth::user()->id,
                   'by'      => "system",
                   'table'   => null,
                   'row_id'  => null,
                   'url'     => url('user'),
                ])
            ])->dispatch(new ImportProcessor($reader, $filePath, $mappings, json_decode($import->columns, true)));


            return successResponse(trans('lang.importer_job_queued'));
        }
        return errorResponse(trans('lang.importer_processing_fail'));
    }
}
