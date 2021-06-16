<?php

namespace App\Plugins\ServiceDesk\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilter;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilterMeta;
use Lang;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Filesystem\Filesystem;
use App\Plugins\ServiceDesk\Request\Report\CreateUpdateReportRequest;
use App\Plugins\ServiceDesk\Controllers\Assets\AssetListController;
use Cache;
use AUth;
use App\Plugins\ServiceDesk\Request\Report\ExportReportRequest;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * ReportController
 * This controller is used to CRUD service desk reports
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ReportController extends Controller
{
    public function __construct()
    {   
        $this->middleware(['auth', 'role.agent']);
    }

    /**
     * method to add service desk reports content in report/get page
     * @return view
     */
    public function reports()
    {
        return view('service::reports.reports');
    }

    /**
     * method for servicedesk report list blade page
     * @param string $reportType
     * @return view
     */
    public function reportIndex($reportType)
    {
        return view('service::reports.report-index', compact('reportType'));
    }

    /**
     * method for creating service desk report blade page
     * @param string $reportType
     * @return view
     */
    public function createReport($reportType)
    {
        return view('service::reports.create-report', compact('reportType'));
    }

    /**
     * method for editing service desk report blade page
     * @param string $reportType
     * @param $reportFilterId
     * @return view
     */
    public function editReport($reportType, $reportFilterId)
    {
        return view('service::reports.edit-report', compact('reportType','reportFilterId'));
    }
    
    /**
     * method to view service desk report blade page
     * @param string $reportType
     * @param int $reportFitlerId
     * @return view
     */
    public function viewReport($reportType, $reportFilterId)
    {
        return view('service::reports.view-report', compact('reportType','reportFilterId'));
    }

    /** 
     * method to create update report filter
     * @param CreateUpdateReportRequest $request
     * @return $response
     */
    public function createUpdateReportFilter(CreateUpdateReportRequest $request)
    {
        $filterArray = $request->toArray();
        $filterArray['creator_id'] = Auth::user()->id;
        $reportFilter = SdReportFilter::updateOrCreate(['id' => $request->id], $filterArray);
        $reportFilter->filterMeta()->delete();
        if ($request->has('fields')) {
        foreach ($request->fields as $field) {
                SdReportFilterMeta::updateOrCreate(
                    [
                        'report_filter_id' => $reportFilter->id,
                        'key' => $field['key']
                    ],
                    [
                        'report_filter_id' => $reportFilter->id,
                        'key'   => $field['key'],
                        'value' => $field['value'],
                        'value_meta' => $field['value_meta'],
                ]);
            }
        }
        Cache::forever('report_column_keys'.$reportFilter->id, $request->report_column_keys);

        return successResponse(Lang::get('ServiceDesk::lang.report_saved_successfully'));
    }

    /**
     * method to get report filter list
     * @param Request $request
     * @return $reponse
     */
    public function getReportFilterList(Request $request)
    {
        $searchString = $request->input('search-query') ?: '';
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortOrder = $request->input('sort-order') ?: 'desc'; 

        $reportFilters = SdReportFilter::where('name', 'LIKE', "%$searchString%")
          ->orWhere('description', 'LIKE', "%$searchString%")
          ->orderBy($sortField, $sortOrder)
          ->paginate($limit);

        return successResponse('', ['reports' => $reportFilters]);
    }

    /**
     * method to get report filter
     * @param $reportFilterId
     * @return $response
     */
    public function getReportFilter($reportFilterId)
    {
        $reportFilter = SdReportFilter::where('id', $reportFilterId)->with('filterMeta')->first();

        if (is_null($reportFilter)) {
            return errorResponse(Lang::get('ServiceDesk::lang.report_filter_not_found'));
        }

        return successResponse('', ['report_filter' => $reportFilter]);
    }

    /**
     * method to delete report filter
     * @param $reportFilterId
     * @return $response
     */
    public function deleteReportFilter($reportFilterId)
    {
        $reportFilter = SdReportFilter::where('id', $reportFilterId)->first();

        if (is_null($reportFilter)) {
            return errorResponse(Lang::get('ServiceDesk::lang.report_filter_not_found'));
        }

        $reportFilter->delete();

        return successResponse(Lang::get('ServiceDesk::lang.report_deleted_successfully'));
    }

    /**
     * method to export report to excel
     * @param ExportReportRequest $request
     * @return $response
     */
    public function exportReportToExcel(ExportReportRequest $request)
    {
      $reportDataWithColumns = $this->getReportData($request->filter_id);
      $filePath = $request->file_path ?? "export/sdreport$request->filter_id.xlsx";
      $spreadSheet = $this->setSpreadSheetParameters($request->filter_id, $reportDataWithColumns);
      $this->makeDirectory();
      // for excel
      $writer = new Xlsx($spreadSheet);
      $writer->save($filePath);
      $fileParams = $this->setFileParameters('xlsx', "sdreport$request->filter_id.xlsx", base_path('public/export') . "/sdreport$request->filter_id.xlsx");

      return response()->download($fileParams['file_path'], $fileParams['file_name'], $fileParams['headers'])->deleteFileAfterSend();

    }

    /**
     * method to get formatted report data based on filter id
     * @param int $fitlerId
     * @return array $reportDataWithColumns
     */
    private function getReportData($filterId)
    {
        $parameters = ['report' => true, 'filter_id' => $filterId];
        $requestParams = new Request($parameters);
        $reportData = (new AssetListController)->getAssetList($requestParams);
        $reportDataWithColumns = json_decode($reportData->content(), true)['data'];
        $reportDataWithColumns = array_merge([$reportDataWithColumns['column_list']], $reportDataWithColumns['report_data']);

        return $reportDataWithColumns;
    }

    /**
     * method to set spread sheet parameters
     * @param int $fitlerId
     * @param array $reportDataWithColumns
     * @return Spreadsheet $spreadSheet
     */
    private function setSpreadSheetParameters($filterId, $reportDataWithColumns)
    {
        $reportFilter = SdReportFilter::where('id', $filterId)->with('reportFilterCreator')->first()->toArray();
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $spreadSheet->getActiveSheet()->setTitle('Asset Report Sheet 1');
        if ($reportFilter) {
            $spreadSheet->getProperties()
                ->setCreator($reportFilter['report_filter_creator']['full_name'])
                ->setLastModifiedBy($reportFilter['report_filter_creator']['full_name'])
                ->setTitle($reportFilter['name'])
                ->setSubject("Servicedesk Asset Report")
                ->setDescription($reportFilter['description'])
                ->setKeywords("Asset Report")
                ->setCategory("Servicedesk Asset Report");
        }
        $styleArrayFirstRow = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ];
        $spreadSheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadSheet->getDefaultStyle()->getFont()->setSize(12);
        $sheet->getStyle('A1:' . 'T1')->applyFromArray($styleArrayFirstRow);
        $sheet->fromArray($reportDataWithColumns);

        return $spreadSheet;
    }

    /**
     * method to export report to csv
     * @param ExportReportRequest $request
     * @return $response
     */
    public function exportReportToCsv(ExportReportRequest $request)
    {
        $reportDataWithColumns = $this->getReportData($request->filter_id);
        $filePath = $request->file_path ?? "export/sdreport$request->filter_id.csv";
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->fromArray($reportDataWithColumns);
        $this->makeDirectory();
        // for csv
        $writer = new Csv($spreadSheet);
        $writer->save($filePath);
        $fileParams = $this->setFileParameters('csv', "sdreport$request->filter_id.csv", base_path('public/export') . "/sdreport$request->filter_id.csv");

        return response()->download($fileParams['file_path'], $fileParams['file_name'], $fileParams['headers'])->deleteFileAfterSend();

    }

    /**
     * method to set file parameters
     * @param string $fileType
     * @param string $fileName
     * @param string $filePath
     * @return array $fileParams
     */
    private function setFileParameters($fileType, $fileName, $filePath)
    {
        $fileParams = [
                'headers' => [
                        'content_type' => $fileType,
                        'message' => Lang::get('ServiceDesk::lang.report_exported_successfully')
                    ],
                'file_name' => $fileName,
                'file_path' => $filePath
            ];

        return $fileParams;
    }

    /**
     * method to create directory public\export
     * @return $path
     */
    private function makeDirectory()
    {
        $path = base_path('public/export');
        $fileSystem = new Filesystem();
        if (!$fileSystem->isDirectory($path)) {
          $fileSystem->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

}
