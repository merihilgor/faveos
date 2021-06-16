<?php

namespace App\Plugins\ServiceDesk\Controllers\Assets;

use File;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use App\Plugins\ServiceDesk\Model\Assets\BarcodeTemplate;
use App\Plugins\ServiceDesk\Requests\BarcodeTemplateRequest;
use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;

class BarcodeTemplateController extends BaseServiceDeskController
{
    /**
     * Return views for creating template
     * @param void
     * @param void
     */
    public function create()
    {
        return view('service::barcode.settings');
    }

    /**
     * Persist template information in database
     * @param App\Plugins\ServiceDesk\Requests\BarcodeTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BarcodeTemplateRequest $request)
    {
        $createdTemplate = BarcodeTemplate::create($request->except('logo_image'));
        if($request->hasFile('logo_image')) 
            UtilityController::attachment($createdTemplate->id, 'sd_barcode_templates',[$request->file('logo_image')]);
        return ($createdTemplate) 
        ? successResponse(trans('ServiceDesk::lang.template_created')) 
        : errorResponse(trans('ServiceDesk::lang.template_create_error'));


    }

    /**
     * Return Template
     * @param void
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $template = BarcodeTemplate::first();
        if($template) {
            $template = $template->toArray();
            $template['logo_image'] = Attachments::where('owner', 'sd_barcode_templates:' . $template['id'])->first();
        }

        return successResponse('',$template);
    }


   /**
    * Updates template information
    * @param mixed $id
    * @param App\Plugins\ServiceDesk\Requests\BarcodeTemplateRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function update(BarcodeTemplateRequest $request,BarcodeTemplate $template)         
    {
        $updated = $template->update($request->except('logo_image'));
        if($updated) {
            
            if($request->image_exists == 'false') $this->unlinkAttachments($template->id);

            if($request->hasFile('logo_image')) {
                $this->unlinkAttachments($template->id);
                UtilityController::attachment($template->id, 'sd_barcode_templates',[$request->file('logo_image')]);
            }
        }

        return ($updated) 
        ? successResponse(trans('ServiceDesk::lang.template_created')) 
        : errorResponse(trans('ServiceDesk::lang.template_create_error'));
    }

    /**
     * Deletes the attachment 
     * @param id
     */
    private function unlinkAttachments($id)     
    {
        $attachment = Attachments::where('owner', 'sd_barcode_templates:' . $id)->first();
        if ($attachment) {
            $file = $attachment->value;
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            Attachments::where('owner', 'sd_barcode_templates:' . $id)->delete();
        }
    }

    /**
     * Deletes the template
     * @param id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTemplate(BarcodeTemplate $template)
    {
        $deletedRows = $template->delete();
        $this->unlinkAttachments($template->id);
        return ($deletedRows)
        ? successResponse(trans('ServiceDesk::lang.barcode_template_deleted'))
        : errorResponse(trans('ServiceDesk::lang.barcode_template_delete_error'));
    }
}