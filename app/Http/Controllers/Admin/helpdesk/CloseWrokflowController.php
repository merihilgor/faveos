<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\WorkflowCloseRequest;
use App\Model\helpdesk\Workflow\WorkflowClose;
use App\Model\helpdesk\Ticket\Ticket_Status;
use Lang;

/**
 * |=================================================
 * |=================================================
 * In this controller the functionalities fo close ticket workflow defined.
 */
class CloseWrokflowController extends Controller
{
    private $security;

    public function __construct(WorkflowClose $security)
    {
        $this->security = $security;
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * get the workflow settings page.
     *
     * @param \App\Model\helpdesk\Workflow\WorkflowClose $securitys
     *
     * @return type view
     */
    public function index(WorkflowClose $securitys, Ticket_Status $statuses)
    {
        try {
            $security = $securitys->whereId('1')->first();
            $ticketStatus = $security->ticketStatus()->pluck('id')->toArray();
            return view('themes.default1.admin.helpdesk.settings.close-workflow.index', compact('security', 'ticketStatus', 'statuses'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * updating the workflow settings for closing ticket.
     *
     * @param type                                             $id
     * @param \App\Http\Requests\helpdesk\WorkflowCloseRequest $request
     *
     * @return type redirect
     */
    public function update($id, WorkflowCloseRequest $request, Ticket_Status $statuses)
    {
        try {
            $security = new WorkflowClose();
            $securitys = $security->whereId($id)->first();
            $securitys->days = $request->input('days');
            $securitys->send_email = $request->input('send_email');
            $securitys->status = $request->input('status');
            $statuses->whereNotIn('id', $request->input('ticket_status'))->update(['auto_close' => null]);
            $statuses->whereIn('id', $request->input('ticket_status'))->update(['auto_close' => 1]);
            $securitys->save();

            return \Redirect::back()->with('success', Lang::get('lang.saved_your_settings_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
}
