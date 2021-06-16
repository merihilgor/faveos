<?php
namespace App\Plugins\ServiceDesk\Controllers\Problem;
use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Location\Models\Location;
use App\Model\helpdesk\Ticket\Ticket_Priority as Priority;
use App\Plugins\ServiceDesk\Requests\CreateProblemRequest;
use Illuminate\Http\Request;
use App\User;
use App\Model\helpdesk\Agent\Permission as Group;
use App\Model\helpdesk\Ticket\Ticket_Status as TicketType;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Common\TicketAssetProblem;
use App\Plugins\ServiceDesk\Requests\CreateChangesRequest;
use Lang;
use File;
use DB;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Model\Problem\ProblemChangeRelation;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;

class ProblemController extends BaseServiceDeskController {

    protected $agentPermission;
    
    public function __construct() {
        $this->middleware('auth');
        $this->agentPermission = new AgentPermissionPolicy();
    }
  
    /**
     * 
     * @param type $id
     * @return type
     */
    public function delete($id) {
        try {
            $sdProblems = SdProblem::findOrFail($id);
            $ticketRelation = CommonTicketRelation::where('type', 'sd_problem')->where('type_id', $id)->delete();
            $assetRelation = CommonAssetRelation::where('type', 'sd_problem')->where('type_id', $id)->delete();
            $sdProblems->delete();
            return \Redirect::route('service-desk.problem.index')->with('success', Lang::get('ServiceDesk::lang.problem_deleted_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function attachNewProblemToTicket(CreateProblemRequest $request) {
        try {
            // $request->validate([
            //     'subject' => 'max:50',
            // ]);
            $ticketid = $request->input('ticketid');
            $store = $this->store($request, $ticketid);
            
            if ($store) {
               UtilityController::commonTicketAttachRelation($ticketid, $store->id, 'sd_problem');
               if (is_array($store->assets()) && ($store->assets() != [])) {
                 
                    foreach ($store->assets() as $value) {
                        UtilityController::commonTicketAttachRelation($ticketid, $value, 'sd_assets'); 
                    }
                }
        return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.created_new_problem_and_attached_to_this_ticket'));
        } 
        }catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    
}
    /**
     * 
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function attachExistingProblemToTicket(Request $request) {
        try {
            $ticketid = $request->input('ticketid');
            $problemid = $request->input('problemid');
            if(!$problemid){
                 return redirect()->back()->with('fails', Lang::get('ServiceDesk::lang.please_select_problem'));
            }
            UtilityController::commonTicketAttachRelation($ticketid, $problemid,'sd_problem');
            return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.problem_attached_to_this_ticket'));
        } catch (Exception $ex) {
            // dd($ex);
        }
    }

    /**
     * 
     * @return type
     */
    public function getAttachableProblem() {
        $model = new SdProblem();
        $select = ['id', 'subject', 'status_type_id'];
        $problems = UtilityController::getModelWithSelect($model, $select)->get();
        return \DataTables::of($problems)
                        ->addColumn('id', function($model) {
                            return '<input name="problemid" type="radio" value="' . $model->id . '">';
                        })
                        ->addColumn('subject', function($model) {
                            $subject = str_limit($model->subject, 20, '...');
                            return "<b>#PRB-".$model->id."</b> &nbsp;&nbsp;<p title='$model->subject'>$subject<p>";
                        })
                        ->addColumn('status', function($model) {
                            $status = "";
                            $statusid = $model->status_type_id;
                            $ticket_statuses = new \App\Model\helpdesk\Ticket\Ticket_Status();
                            $ticket_status = $ticket_statuses->find($statusid);
                            if ($ticket_status) {
                                $status = $ticket_status->name;
                            }
                            return $status;
                        })
                        ->rawColumns(['id','subject','status'])
                        ->make(true);
    }
    /**
     * 
     * @param type $problem
     * @param type $ticketid
     */
    public function timelineMarble($problem, $ticketid) {
        if ($problem) {
            echo $this->marble($problem, $ticketid);
        }
        echo "";
    }
    /**
     * 
     * @param type $problem
     * @param type $ticketid
     * @return type
     */
    public function marble($problem, $ticketid) {
        $subject = $problem->subject;
        $content = $problem->description;
        $problemid = $problem->id;
        return $this->marbleHtml($ticketid, $problemid, $subject, $content);
    }
    
    public static function problemPopUp($id, $url, $title = "Delete", $class = "", $btn_name = "", $button_check = true) {
        
        $class = "btn btn-primary";
        $btn_name = "<i class='fa fa-trash'>&nbsp;&nbsp;".Lang::get('ServiceDesk::lang.detach')."</i>";
        $button = "";
        if ($button_check == true) {
            $button = '<a href="#delete1" class="' . $class . '" data-toggle="modal" data-target="#delete1' . $id . '">' . $btn_name . '</a>&nbsp;';
        }
        return $button . '<div class="modal fade" id="delete1' . $id . '">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" style="color:black">' .Lang::get('ServiceDesk::lang.detach'). '</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <div class="col-md-12">
                                <p  style="color:black;">'.Lang::get('ServiceDesk::lang.are_you_sure'). '</p>
                                </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="close-popup" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.close').'</button>
                                <a href="' . $url . '" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.detach').'</a>
                            </div>
                        </div>
                    </div>
                </div>';
    }
    /**
     * 
     * @param type $ticketid
     * @param type $problemid
     * @param type $subject
     * @param type $content
     * @return type
     */
    public function marbleHtml($ticketid, $problemid, $subject, $content) {
        //dd($problemid);
        $subject_trim = str_limit($subject, 20);
        $content_trim = str_limit($content, 20);
        $url = url('service-desk/problem/detach/' . $ticketid.'/'.$problemid);
        $detach_popup = \App\Plugins\ServiceDesk\Controllers\Problem\ProblemController::problemPopUp($ticketid, $url, "Delete", " ", "<button class='btn btn-primary btn'> <i class='fa fa-trash' style='color:white;'> &nbsp;Delete</button>", true);
       return "<div class='box box-primary'>"
                . "<div class='box-header'>"
                . "<h3 class='box-title'>".Lang::get('ServiceDesk::lang.associated_problems')."</h3>"
                . "</div>"
                . "<div class='box-body row'>"
                . "<div class='col-md-12'>"
                . "<table class='table'>"
                . "<tr>"
                . "<td title='".$subject."'>" . ucfirst($subject_trim) . "</td>"
                // . "<td>" . ucfirst($content_trim) . "</td>"
                . "<th>" . $detach_popup . "&nbsp;&nbsp;<a href=" . url('service-desk/problem/' . $problemid . '/show') . " class='btn btn-primary btn'><i class='fa fa-eye' style='color:white;'> &nbsp;".Lang::get('ServiceDesk::lang.view')."</a>" . "</th>"
                . "</tr>"
                . "</table>"
                . "</div>"
                . "</div>"
                . "</div>";
    }
    /**
     * 
     * @param type $ticketid
     * @return type
     */
    public function detach($ticketid ,$problemId) {
        $relation = UtilityController::getRelationOfTicketByTable($ticketid, $problemId, 'sd_problem');
        if ($relation) {
            $relation->delete();
        }
        return redirect()->back()->with('success',Lang::get('ServiceDesk::lang.detached_successfully'));
    }
    /**
     * 
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function show($id) {
        try {
            if (!$this->agentPermission->problemsView()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $problems = new SdProblem();
            $problem = $problems->find($id);
            $sdPolicy = $this->agentPermission;
            if ($problem) {
                return view('service::problem.show', compact('problem','sdPolicy'));
            } else {
                throw new \Exception('Sorry we can not find your request');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function close($id) {
        try {
            $problems = new SdProblem();
            $problem = $problems->find($id);
            if ($problem) {
                $problem->status_type_id = 3;
                $problem->save();
                return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.updated'));
            } else {
                throw new \Exception('Sorry we can not find your request');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * @return type
     */
    public function getChanges() {
        $change = new \App\Plugins\ServiceDesk\Model\Changes\SdChanges();
        $changes = $change->select('id', 'subject')->get();
        return \DataTables::Collection($changes)
                        ->addColumn('id', function($model) {
                            return "<input type='radio' name='change' value='" . $model->id . "'>";
                        })
                        ->addColumn('subject', function($model) {
                            return "<b style='font-size:12px'>#CHN-".$model->id."</b> &nbsp;&nbsp;<span title='".$model->subject."'>". str_limit($model->subject, 20)."</span>";
                        })
                        ->rawColumns(['id','subject'])
                        ->make(true);
    }
    /**
     * 
     * @param type $id
     * @param \App\Plugins\ServiceDesk\Requests\CreateChangesRequest $request
     * @return type
     */
    public function attachNewChange($id, CreateChangesRequest $request) {
        //dd($request->all());
        try {
            $change_controller = new \App\Plugins\ServiceDesk\Controllers\Changes\ChangesController();
            $change = $change_controller->changeshandleCreate($request, true);
            $this->changeAttach($id, $change->id);
            if ($change) {
                return redirect()->back()->with('success',trans('ServiceDesk::lang.changes_attached_successfully'));
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * @param type $id
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function attachExistingChange($id, Request $request) {
        // dd($request->all());
        try {
            $changeid = $request->input('change');
            $store = $this->changeAttach($id, $changeid);
            if ($store) {
                return redirect()->back()->with('success', trans('ServiceDesk::lang.changes_attached_successfully'));
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * @param type $problemid
     * @param type $changeid
     * @return type
     */
    public function changeAttach($problemid, $changeid) {
        $relation = new \App\Plugins\ServiceDesk\Model\Problem\ProblemChangeRelation();
                    ProblemChangeRelation::where('problem_id', $problemid)->delete();
        return $relation->create([
                    'problem_id' => $problemid,
                    'change_id' => $changeid,
        ]);
    }
    /**
     * 
     * @param type $problemid
     * @return type
     */
    public function detachChange($problemid) {
        try {
            $relations = new \App\Plugins\ServiceDesk\Model\Problem\ProblemChangeRelation();
            $relation = $relations->where('problem_id', $problemid)->first();
            if ($relation) {
                $relation->delete();
            }
            return redirect()->back()->with('success',Lang::get('ServiceDesk::lang.changes_detached_successfully'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * @param type $id
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function deleteUploadfile($id, Request $request) {
        try {
            $file = $request->filename;
            $attachment = DB::table('sd_attachments')->where('owner', '=', 'sd_problem:' . $id)->delete();
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            return Lang::get('lang.your_status_updated_successfully');
            // return redirect()->back()->with('success', 'Updated');
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

     /**
     * 
     * @param Request $request
     * @return type
     */
    public function attachAssetToProblem(Request $request) {
        try{
            $request->validate([
                'asset' => 'required',
                'problemid' => 'required'
            ]);
                $data = CommonAssetRelation::whereIn('asset_id', request('asset'))->where('type_id', request('problemid'))
                        ->where('type', 'sd_problem')->get()->toArray();
                $problemId = $request->input('problemid');
            if($request->has('asset')){
                $assetIds = $request->input('asset');
                        UtilityController::commonCreateAssetAttach($assetIds, $problemId, 'sd_problem');
            } 
                return redirect()->back()->with('success',trans('ServiceDesk::lang.asset_attached_successfully'));
            }catch (Exception $ex) {
                return redirect()->back()->with('fails', $ex->getMessage());
            }
    }

    //This function will retrive attached ticket details in yajra data table format based on problemId
    
    /**
     * @param $problemId
     * @return type
     */
    public function getAssociatedTicketsBasedProblem($problemId) {

        $problem = SdProblem::findOrFail($problemId);
        $tickets = ($problem->tickets() != null)? $problem->tickets():[];
        
        return \DataTables::of($tickets)
                    ->addColumn('ticket_number', function($model) {
                        return "<a href=" . url('thread/' . $model['id']) . " title=".$model['ticket_number'].">".$model['ticket_number']."</a>";
                    })
                    ->addColumn('subject', function($model) {
                        return "<span title='".$model['title']."'>". str_limit($model['title'], 20)."</span>";
                    })
                    ->addColumn('action', function($model) use ($problemId) {
                          $url = url('service-desk/problem/detach/'.$model['id'].'/'.$problemId);
                          $detach = UtilityController::detachPopUp($model['id'], $url, "Detach");
                          return $detach;
                    })
                    ->rawColumns(['ticket_number','subject','action'])
                    ->make(true);
  }

}