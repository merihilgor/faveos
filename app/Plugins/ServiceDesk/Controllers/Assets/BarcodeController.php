<?php

namespace App\Plugins\ServiceDesk\Controllers\Assets;

use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Assets\BarcodeTemplate;
use App\Plugins\ServiceDesk\Requests\AssetForBarcodeRequest;
use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\milon\barcode\src\Milon\Barcode\DNS2D;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BarcodeController extends BaseServiceDeskController
{
    public function generate(AssetForBarcodeRequest $request)
    {
        $payload = [];
        $assets = SdAssets::whereIn('id',$request->ids)->get()->all();
        $template = BarcodeTemplate::first();
        if(!(new AgentPermissionPolicy)->assetsView() || !$template || !$assets)
            return abort(404);
        $template = $template->toArray();
        $attachment = \DB::table('sd_attachments')->where('owner', '=','barcode_templates:'.$template['id'])->first();
        if($attachment){
            $attachPath = public_path('uploads' . DIRECTORY_SEPARATOR . 'service-desk' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $attachment->value);
            if(file_exists($attachPath)) {
                $template['logo_image'] = base64_encode(file_get_contents($attachPath));
            }
        }
        foreach($assets as $asset) {
            $img = DNS2D::getBarcodePNG($asset->identifier, "QRCODE,H");
            array_push(
                $payload,[
                    'barcode' => $img,
                    'asset_name' => $asset->name, 
                    'identifier' => $asset->identifier,
                ]
            );
        }
        return view('service::barcode.barcode',compact('payload','template'));
    }
}