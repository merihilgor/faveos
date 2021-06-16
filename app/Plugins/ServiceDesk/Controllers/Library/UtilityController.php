<?php

namespace App\Plugins\ServiceDesk\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use Exception;
use App\Plugins\ServiceDesk\Model\Common\AssetRelation;
use Auth;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use Lang;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\User;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\ServiceDesk\Model\Problem\Impact;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Model\Common\TicketRelation;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Cab\Cab;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;


class UtilityController extends Controller {

    protected $agentPermission;
    
    public function __construct() {
        $this->middleware('auth');
        $this->agentPermission = new AgentPermissionPolicy();
    }

    public static function assetSearch($query, $format = 'json') {
        $assets = new SdAssets();
        $asset = $assets->where('name', 'LIKE', '%' . $query . '%')->select('name as label', 'id as value');

        if ($format == 'json') {
            $asset = $asset->get()->toJson();
        }
        return $asset;
    }

    //Common method used to get assetlist based on asset-type
    public static function assetByTypeId($typeid) {
        $assets = new SdAssets();
        $asset = $assets->where('asset_type_id', $typeid);
        return $asset;
    }
    
    //Common method used to get assetlist based on asset-type which are not linked with contracts
    public static function assetByTypeIdFilteredByContract($typeid) {

        $linkedAssets = CommonAssetRelation::where('type', 'sd_contracts')->select('asset_id')->get()->toArray();
        $assets = new SdAssets();
        $asset = $assets->where('asset_type_id', $typeid)->whereNotIn('id', $linkedAssets);
        return $asset;
    }

    public static function getModelWithSelect($model, $select = []) {
        try {
            if (count($select) > 0) {
                $model = $model->select($select);
            }
            return $model;
        } catch (Exception $ex) {
            
        }
    }

    public static function saveTicketRelation($ticketid, $table, $id) {
        
        $relation = new \App\Plugins\ServiceDesk\Model\Common\TicketRelation();
        $relations = $relation->where('ticket_id', $ticketid)->where('owner','LIKE',$table."%")->get();
        if ($relations->count() > 0) {
            foreach ($relations as $del) {
                $del->delete();
            }
        }
        if (is_array($id)) {
            foreach ($id as $i) {
                $relation->create([
                    'ticket_id' => $ticketid,
                    'owner' => "$table:$i",
                ]);
            }
        } else {
            $owner = "$table:$id";
            $relation->create([
                'ticket_id' => $ticketid,
                'owner' => $owner,
            ]);
        }
    }

    public static function saveAssetRelation($assetid, $table, $id) {
        if (is_array($assetid)) {
            $assetid = implode(',', $assetid);
        }
        $owner = "$table:$id";
        $relation = new \App\Plugins\ServiceDesk\Model\Common\AssetRelation();
        $relations = $relation->where('owner', $owner)->get();
        if ($relations->count() > 0) {
            foreach ($relations as $del) {
                $del->delete();
            }
        }
        $relation->create([
            'asset_ids' => $assetid,
            'owner' => $owner,
        ]);
    }

    public static function getAssetByTicketid($ticketid) {
        $relation = new \App\Plugins\ServiceDesk\Model\Common\AssetRelation();
        $model = $relation->where('owner', "tickets:$ticketid")->first();
        $asset = false;
        if ($model) {
            if ($model->asset_id) {
                $assets = new SdAssets();
                $asset = $assets->where('id', $model->asset_id)->first();
            }
        }
        return $asset;
    }

    // public static function getRelationOfTicketByTable($ticketid,$table) {
    //     $realtions  = new \App\Plugins\ServiceDesk\Model\Common\TicketRelation();
    //     $realtion = $realtions->where('ticket_id',$ticketid)->where('owner','LIKE',$table."%")->first();
    //     if($realtion){
    //         return $realtion;
    //     }
    // }

    public static function getRelationOfTicketByTable($ticketId, $typeId, $type) {
     $relation = CommonTicketRelation::where('ticket_id', $ticketId)->where('type_id', $typeId)->where('type', $type);
     if($relation){
      return $relation;
  }
}

public static function getTicketByThreadId($id) {
    $tickets = new \App\Plugins\ServiceDesk\Model\Common\Ticket();
    $ticket = $tickets->where('id', $id)->first();
    return $ticket;
}

public static function getUserByAssetId($assetid) {
    $assets = new SdAssets();
    $asset = $assets->find($assetid);
    $userid = $asset->used_by;
    $users = new \App\User();
    $user = $users->find($userid);
    return $user;
}

public static function getManagedByAssetId($assetid) {
    $assets = new SdAssets();
    $asset = $assets->find($assetid);
    $userid = $asset->managed_by;
    $users = new \App\User();
    $user = $users->find($userid);
    return $user;
}

public static function getRelationOfTicket($id) {
    $relations = new \App\Plugins\ServiceDesk\Model\Common\TicketRelation();
    $relation = $relations->where('ticket_id', $id)->get();
    return $relation;
}

public static function getRelationOfAsset($table, $id) {
    $relations = new \App\Plugins\ServiceDesk\Model\Common\AssetRelation();
    $owner = "$table:$id";
    $relation = $relations->where('owner', $owner)->first();
    return $relation;
}

public static function getSubjectByThreadId($thread) {
    $title = "";
    if($thread){
     $ticketid = $thread->ticket_id;
     $title = title($ticketid);
 }
 return $title;
}

public static function getBodyByThreadId($threadid) {
    $threads = new \App\Model\helpdesk\Ticket\Ticket_Thread();
    $thread = $threads->find($threadid);
    $ticketid = $thread->ticket_id;
    $thread_first = $threads->where('ticket_id', $ticketid)->first();
    return $thread_first->body;
}

public static function getBodyByThreadMaxId($thread) {
    return $thread->body;
}

public static function attachment($id, $table, $attachments, $saved = 2) {
        //dd($id);
    $owner = "$table:$id";
    $value = "";
    $type = "";
    $size = "";

    if ($attachments) {
        foreach ($attachments as $attachment) {
            if ($attachment) {
                $name = $attachment->getClientOriginalName();
                $destinationPath = public_path('uploads/service-desk/attachments');
                $value = rand(0000, 9999) . '.' . $name;
                $type = $attachment->getClientOriginalExtension();
                $size = $attachment->getSize();
                if ($saved == 2) {
                    $attachment->move($destinationPath, $value);
                } else {
                    $value = file_get_contents($attachment->getRealPath());
                }
                self::storeAttachment($saved, $owner, $value, $type, $size);
            }
        }
    }
}

public static function storeAttachment($saved, $owner, $value, $type, $size) {
    $attachments = new Attachments();
    $attachments->create([
        'saved' => $saved,
        'owner' => $owner,
        'value' => $value,
        'type' => $type,
        'size' => $size,
    ]);
}

public static function deleteAttachments($id, $table) {
    $owner = "$table:$id";
    $attachments = new Attachments();
    $attachment = $attachments->where('owner', $owner)->first();

    if ($attachment) {
        self::removeAttachment($attachment);
    }
}

public static function removeAttachment($attachment) {
    $saved = $attachment->saved;
    if ($saved == 2) {
        $file = $attachment->value;
        $path = public_path('uploads' . DIRECTORY_SEPARATOR . 'service-desk' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $file);
        unlink($path);
    }
    $attachment->delete();
}

public static function downloadAttachment($attachment) {
    $saved = $attachment->saved;
    if ($saved == 2) {
        $file = $attachment->value;
        $attach = public_path('uploads' . DIRECTORY_SEPARATOR . 'service-desk' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $file);
    } else {
        $attach = $attachment->value;
    }
    return $attach;
}

public static function storeAssetRelation($table, $id, $asset_ids = [], $update = false) {

       //dd($table,$id, $asset_ids, $update);
    $relations = new AssetRelation();
    $owner = "$table:$id";
    $relationses = $relations->where('owner', $owner)->get();
    if ($relationses->count() > 0) {
        foreach ($relationses as $relationse) {
            $relationse->delete();
        }
    }
    if ($asset_ids) {
        if (is_array($asset_ids)) {
            $asset_ids = implode(',', $asset_ids);
        }

        $relations->asset_ids = $asset_ids;
        $relations->owner = $owner;
        $relations->save();
    }
}

public static function deleteAssetRelation($id) {
    $relations = new AssetRelation();
    $relation = $relations->where('asset_ids', '!=', '')->get();
    $asset_ids = "";
        //dd($relation->asset_ids);
    foreach ($relation as $del) {
        $array = $del->asset_ids;
        $array = array_diff($array, [$id]);
        if (count($array) > 0) {
            $asset_ids = implode(',', $array);
            $del->asset_ids = $asset_ids;
            $del->save();
        } else {
            $del->delete();
        }
    }
}

public static function xmlToArray($xml, $options = array()) {
    $defaults = array(
            'namespaceSeparator' => ':', //you may want this to be something other than a colon
            'attributePrefix' => '', //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(), //array of xml tag names which should always become arrays
            'autoArray' => true, //only create arrays for tags which appear more than once
            'textContent' => 'option-name', //key used for the text content of elements
            'autoText' => true, //skip textContent key if node has no attributes or child nodes
            'keySearch' => false, //optional search and replace on tag and attribute names
            'keyReplace' => false       //replace values for above search values (as passed to str_replace())
        );
    $options = array_merge($defaults, $options);
    $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace
        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch'])
                    $attributeName = str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                . $attributeName;
                $attributesArray[$attributeKey] = (string) $attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = self::xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);

                //replace characters in tag name
                if ($options['keySearch'])
                    $childTagName = str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix)
                    $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] = in_array($childTagName, $options['alwaysArray']) || !$options['autoArray'] ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName]) === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        //get text content of node
        $textContentArray = array();
        $plainText = trim((string) $xml);
        if ($plainText !== '')
            $textContentArray[$options['textContent']] = $plainText;

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '') ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }

    public static function arrayToXml($array, $key = '', $options = false) {
        $field = "";
        $value = "";
        //$options = false;
        if (is_integer($key)) {
            $field = "<field ";
            foreach ($array as $index => $item) {

                if (is_array($item)) {
                    $value = self::value($item);
                    $options = true;
                } elseif ($options == false) {
                    $it = '=' . '"' . $item . '" ';
                    $field .= $index . $it;
                }
            }

            $field .= ">$value</field>";
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $field .= self::arrayToXml($value, $key, $options);
            }
        }

        return $field;
    }

    public static function value($item) {
        $result = "";
        foreach ($item as $k => $v) {
            $result .= '<option value=' . '"' . $k . '"' . '>' . $v . '</option>';
        }
        return $result;
    }


    public static function deletePopUp($id, $url, $title = "", $class = "btn btn-primary btn-xs", $btn_name = "", $button_check = true) {

        $btn_name = "<i class='fa fa-trash'>&nbsp;&nbsp;</i>".Lang::get('ServiceDesk::lang.delete');
        $title = Lang::get('ServiceDesk::lang.delete');


        $button = "";
        if ($button_check == true) {
            $button = '<a href="#delete" class="' . $class . '" data-toggle="modal" data-target="#delete' . $id . '">' . $btn_name . '</a>&nbsp;';
        }
        $button .= '<div class="modal fade" id="delete' . $id . '">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style="color:black">' . $title . '</h4>
        </div>
        <div class="modal-body">
        <div class="row">
        <div class="col-md-12">
        <p  style="color:black;">'.Lang::get('ServiceDesk::lang.are_you_sure').'</p>
        </div>
        </div>
        </div>
        <div class="modal-footer">';

        $button .= '<button type="button" id="close-popup" class="btn btn-default';
        $button .= Lang::getLocale() == "ar" ? ' pull-right' : ' pull-left';
        $button .= '" data-dismiss="modal">';
        $button .= '<i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.close').'</button>
        <a href="' . $url . '" onclick="clickAndDisable(this);" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.delete').'</a>
        </div>
        </div>
        </div>
        </div>
        <script> 
        function clickAndDisable(link) {
     // disable subsequent clicks
           link.onclick = function(event) {
            event.preventDefault();
        }
    }   
    </script>';

    return $button;
}


public static function detachPopUp($id, $url, $title = "", $class = "btn btn-primary btn-xs", $btn_name = "", $button_check = true) {

    $btn_name = "<i class='fa fa-trash'>&nbsp;&nbsp;</i>".Lang::get('ServiceDesk::lang.detach');
    $title = Lang::get('ServiceDesk::lang.detach');


    $button = "";
    if ($button_check == true) {
        $button = '<a href="#detach" class="' . $class . '" data-toggle="modal" data-target="#detach' . $id . '">' . $btn_name . '</a>&nbsp;';
    }
    $button .= '<div class="modal fade" id="detach' . $id . '">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" style="color:black">' . $title . '</h4>
    </div>
    <div class="modal-body">
    <div class="row">
    <div class="col-md-12">
    <p  style="color:black;">'.Lang::get('ServiceDesk::lang.are_you_sure').'</p>
    </div>
    </div>
    </div>
    <div class="modal-footer">';

    $button .= '<button type="button" id="close-popup" class="btn btn-default';
    $button .= Lang::getLocale() == "ar" ? ' pull-right' : ' pull-left';
    $button .= '" data-dismiss="modal">';
    $button .= '<i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.close').'</button>
    <a href="' . $url . '" onclick="clickAndDisable(this);" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.detach').'</a>
    </div>
    </div>
    </div>
    </div>
    <script> 
    function clickAndDisable(link) {
     // disable subsequent clicks
       link.onclick = function(event) {
        event.preventDefault();
    }
}   
</script>';

return $button;
}


public static function checkCabUser($cabid) {
    $authid = Auth::user()->id;
    $result = false;
    $cabs = new \App\Plugins\ServiceDesk\Model\Cab\Cab();
    $cab = $cabs->find($cabid);
    if ($cab) {
        $members = $cab->approvers;
        if (is_array($members)) {
            if (in_array($authid, $members)) {
                $result = true;
            }
        } elseif (is_integer($members)) {
            if ($authid == $members) {
                $result = true;
            }
        } elseif ($cab->head) {
            if ($authid == $cab->head) {
                $result = true;
            }
        }
    }
    return $result;
}

public static function cabMessage($cabid, $activity, $url) {
    $cabs = new \App\Plugins\ServiceDesk\Model\Cab\Cab();
    $cab = $cabs->find($cabid);
    if ($cab) {
        $members = $cab->approvers;
        $head = $cab->head;
        if (is_array($members)) {
            if (count($members) > 0) {
                foreach ($members as $userid) {
                    self::sendCabMessage($userid, $head, $activity, $url);
                }
            }
        }
    }
}

public static function sendCabMessage($userid, $head, $activity, $url) {
    $users = new \App\User();
    $user = $users->find($userid);
    $leader = $users->find($head);
    $heads = "";
        //dd($url);
    if ($user) {
        $email = $user->email;
        $name = $user->first_name . " " . $user->last_name;
        if ($leader) {
            $heads = $leader->first_name . " " . $leader->last_name;
        }
            //dd([$email,$name,$heads,$url]);
        $php_mailer = new \App\Http\Controllers\Common\PhpMailController();
        $php_mailer->sendmail(
            $from = $php_mailer->mailfrom('1', '0'), $to = ['name' => $name, 'email' => $email], $message = [
                'subject' => 'Requesting For CAB Approval',
                'scenario' => 'sd-cab-vote',
            ], $template_variables = [
                'user' => $name,
                'system_link' => $url,
                '$system_from' => $heads,
            ]
        );
    }
}

public static function storeGeneralInfo($modelid, $table, $requests) {
    $owner = "$table:$modelid";
    $request = $requests->except('_token', 'attachment', 'identifier');
        // dd($request);
    $general = new GeneralInfo();

    if (count($request) > 0) {
        foreach ($request as $key => $value) {
            $generals = $general->where('owner', $owner)->where('key', $key)->first();
            if ($generals) {
                $generals->delete();
            }
            if ($value !== "") {
                $general->create([
                    'owner' => $owner,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }

    $attachments = $requests->file('attachment');
    $identifier = $requests->input('identifier');
    $attach_table = "$table:$identifier";
    self::attachment($modelid, $attach_table, $attachments);
    return "success";
}

public static function getAttachmentSize($size) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    $value = number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    return $value;
}


  //Common asset attach function for problem,change,release module [ only for create operation]

   /**
     * @param type $asset_ids
     * @param type $type_id
     * @param type $type
     */
   public static function commonCreateAssetAttach(Array $asset_ids, $type_id, $type)
   {
    $allowed_types = ['sd_problem', 'sd_changes', 'sd_releases', 'ticket', 'sd_contracts'];

    if (!in_array($type, $allowed_types)) {
        return false;
    }
    foreach ($asset_ids as $asset_id) {
        CommonAssetRelation::create(['asset_id' => $asset_id, 'type_id' => $type_id, 'type' => $type]) ;  
    }
    
}


//Common asset attach function for problem,change,release module [Update operation]

   /**
     * @param type $asset_ids
     * @param type $type_id
     * @param type $type
     */
   public static function commonAssetAttach(Array $asset_ids, $type_id, $type)
   {

    $allowed_types = ['sd_problem', 'sd_changes', 'sd_releases', 'tickets', 'sd_contracts'];

    if (!in_array($type, $allowed_types)) {
        return false;
    }
    CommonAssetRelation::where('type_id', $type_id)->where('type', $type)->delete();

    foreach ($asset_ids as $asset_id) {
        CommonAssetRelation::create(['asset_id' => $asset_id, 'type_id' => $type_id, 'type' => $type]) ;  
    }
    
}


//Common asset detach function for problem,change,release module [Detach operation]

   /**
     * @param type $assetId
     * @param type $typeId
     * @param type $type
     */
   public static function commonAssetDetach($assetId, $typeId, $type)
   {   
    try{
       $allowed_types = ['sd_problem', 'sd_changes', 'sd_releases', 'ticket', 'sd_contracts'];

       if (!in_array($type, $allowed_types)) {
        return false;
    }
    CommonAssetRelation::where('asset_id', $assetId)->where('type_id', $typeId)->where('type', $type)->delete();
    return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.asset_detached_successfully'));
}catch(Exception $ex){
    return redirect()->back()->with('fails', $ex->getMessage());
    
}
}


//Common Ticket asset/problem attach relation

   /**
     * @param type $ticketId
     * @param type $typeId
     * @param type $type
     */
   public static function commonTicketAttachRelation($ticketId, $typeId, $type)
   {

    $allowedTypes = ['sd_problem', 'sd_assets'];

    if (!in_array($type, $allowedTypes)) {
        return false;
    }
    CommonTicketRelation::where('type_id', $typeId)->where('type', $type)->where('ticket_id', $ticketId)->delete();
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $typeId, 'type' => $type]) ;  
}

//This function will retrive attached assets details based on ticketId

   /**
     * @param type $ticketId
     *
     */

   public static function getAssetsByTicketid($ticketId) {

    $relation = CommonTicketRelation::where('ticket_id', $ticketId)->where('type', 'sd_assets')->pluck('type_id')->toArray();
    if($relation){
        $attachedAssets = SdAssets::whereIn('id', $relation)->select('id','name','used_by','managed_by')->get()->toArray();

        foreach ($attachedAssets as $value) {
            
            $usedBy = User::where('id', $value['used_by'])->first();
            $displayUsedBy =(['id'=>$usedBy->id,'name'=>$usedBy->fullName, 'role'=>$usedBy->role]);
            
            $managedBy = User::where('id', $value['managed_by'])->first();
            $displayManagedBy = (['id'=>$managedBy->id,'name'=>$managedBy->fullName, 'role'=>$managedBy->role]);

            $result[] =(['assetId'=>$value['id'],'name'=> $value['name'],'used_by'=>$displayUsedBy ,'managed_by'=> $displayManagedBy]);
        }
        
        return $result;
    }

}

//This function will retrive attached problem details based on ticketId

    /**
     * @param type $ticketId
     */

    public static function getProblemsByTicketid($ticketId) {

        $problemId = CommonTicketRelation::where('ticket_id', $ticketId)->where('type', 'sd_problem')->value('type_id');
        
        if($problemId){
            $problemData = SdProblem::find($problemId);
            $subject = $problemData->subject;
            $from    = $problemData->from;
            $department = ($problemData->department != null) ? Department::where('id', $problemData->department)->select('id','name')->first()->toArray():[];
            $impact = ($problemData->impact_id != null) ? Impact::where('id', $problemData->impact_id)->select('id','name')->first()->toArray():[];
            $status_id = ($problemData->status_type_id != null) ? Ticket_Status::where('id', $problemData->status_type_id)->select('id','name')->first()->toArray():[];
            $priority_id = ($problemData->priority_id != null) ? Ticket_Priority::where('priority_id', $problemData->priority_id)->select('priority_id','priority')->first()->toArray():[];
            $response =([
              'id'            => $problemData->id,
              'subject'       => $subject,
              'from'          => $from,
              'department'    => $department,
              'impact_id'     => $impact,
              'status_type_id'=> $status_id, 
              'priority_id'   => $priority_id,
              'description'   => $problemData->description,
              'created_at'    => $problemData->created_at,
          ]);
            
            return $response;
        }
    }


    //This function will retrive attached asset details in yajra data table format for problem,change,release ,contract modules
    
    /**
     * @param $typeId, $type [Example type =sd_problem ,typeId =problemid]
     * @return type
     */
    public function getListOfAttachedAssets($typeId, $type) {

        $assetIds = CommonAssetRelation::where('type', $type)->where('type_id', $typeId)->pluck('asset_id')->toArray();
        $selectedassets = SdAssets::whereIn('id', $assetIds)->select('id','name','managed_by_id','used_by_id')->get()->toArray();
      
        return \DataTables::of($selectedassets)
        ->addColumn('name', function($model) {
            $name = $model['name'];
            if($name){
              return '<a href="'.url("service-desk/assets/".$model["id"]."/show"). '" title="'.$name.'">'.str_limit($name,30).'</a>';
          }
      })
        ->addColumn('managed_by', function($model) {
           $managed = \App\User::where('id',$model['managed_by_id'])->first();
           $managedBy = ($managed != null) ? $managed->fullName : null ;
           return "<a href=".url('user/'.$model['managed_by_id']). ">".$managedBy."</a>";
       })
        ->addColumn('used_by', function($model) {
            $used = \App\User::where('id',$model['used_by_id'])->first();
            $usedBy = ($used  != null) ?  $used->fullName : null ;
            return "<a href=".url('user/'.$model['used_by_id']). ">".$usedBy."</a>";
        })
        ->addColumn('action', function($model) use($typeId, $type) {
          $url = url('service-desk/detach/asset/'.$model['id'].'/'.$typeId.'/'.$type);
          $detach = ($this->agentPermission->assetDetach()) ? UtilityController::detachPopUp($model['id'], $url, "Detach"): '';
          return $detach;
      })
        ->rawColumns(['name','managed_by','used_by','action'])
        ->make(true);
    }

    
    //Common tab view for ticket timeline asset n problem informations
    public function timelineMarble($problem ,$asset, $ticketid) {

      if ($asset || $problem) {
          echo view("service::interface.agent.tab", ['ticketid'=> $ticketid, 'problem'=> $problem]);
      }
      echo "";
  }
  
    //Get assets based organization id
  public static function getAssetsByOrganizationId($orgId){
     
     $assetList = SdAssets::where('organization', $orgId)->select('id', 'name', 'used_by', 'managed_by', 'asset_type_id')->get()->toArray();
     return $assetList;
 }

    //Get assets based user id
 public static function getAssetsByUserId($userId){

    $assetList = SdAssets::where('used_by', $userId)->orWhere('managed_by', $userId)->select('id','name','managed_by','used_by', 'asset_type_id')->get();
    return $assetList;
}

   //Copying existing values of sd_ticket_relation to sd_common_ticket_relation ( for existing users (old relation to new relation))
public function TicketRelationToCommonTicketRelation(){
    try{
        $oldRelation = TicketRelation::select('ticket_id','owner')->get()->toArray();
        if($oldRelation){
            foreach ($oldRelation as $key => $value) {
             $ticketId = $value['ticket_id'];
             $data     = explode(":", $value['owner']);
             $type     = $data[0];
             $typeId   = $data[1];
             $this->commonTicketAttachRelation($ticketId, $typeId, $type);
         }
     }
 }catch(Exception $ex){
    return errorResponse($ex->getMessage());
}
}

    //Copying existing values of sd_asset_relations to sd_common_asset_relation ( for existing users (old relation to new relation))
public function AssetRelationToCommonAssetRelation(){
    try{
        $oldRelation = AssetRelation::select('asset_ids','owner')->get()->toArray();

        if($oldRelation){
            foreach ($oldRelation as $key => $value) {
             $assetId = $value['asset_ids'];
             $data     = explode(":", $value['owner']);
             $type     = $data[0];
             $typeId   = $data[1];
             $this->commonAssetAttach($assetId, $typeId, $type); 
         }
     }
 }catch(Exception $ex){
    return errorResponse($ex->getMessage());
}
}

}