<?php

namespace App\Http\Controllers\Agent\helpdesk\TicketsWrite;

use App\Http\Requests\helpdesk\Ticket\TicketEditRequest;
use App\Http\Requests\helpdesk\Ticket\TicketForkRequest;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Model\helpdesk\Ticket\Ticket_Status as Status;
use App\Http\Controllers\Admin\helpdesk\Label\LabelController;
use App\Http\Controllers\Agent\helpdesk\Filter\TagController;
use App\User;
use Lang;
use Auth;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Admin\helpdesk\SettingsController;
use Illuminate\Http\Request;
use Config;
use App\Model\Common\Template;
use App\Http\Controllers\Common\PhpMailController;
use Exception;
use Logger;
use App\Http\Requests\helpdesk\Ticket\TicketForwardRequest;
use App\Model\helpdesk\Ticket\Ticket_attachments as Attachment;
use App\Model\helpdesk\Ticket\Ticket_Collaborator as Collaborator;
use App\Http\Requests\helpdesk\Ticket\TagsCreateFromInboxRequest;


/**
 * Contains actions that results in change in ticket state
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketActionController extends Controller
{

  public function __construct()
  {
    $this->middleware(['auth', "role.agent"]);
  }

  /**
   * Ticket on which action is getting performed
   * @var Ticket
   */
  private $ticket;

  /**
   * Edits a ticket
   * @return Response
   */
  public function editTicket($ticketId, TicketEditRequest $request)
  {
    try {
        $this->ticket = Ticket::where('id',  $ticketId)->first();

        // update custom fields before ticket gets saved (so that listener can be enforced which happens before ticket saves)
        $this->saveCustomFields($request);

        $this->saveTicketData($request);

        if($request->has('subject')){
          $this->updateSubject($request->input('subject'));
        }

        return successResponse(Lang::get('lang.updated_successfully'));

    } catch (\Exception $e) {
        return errorResponse($e->getMessage());
    }
  }

    /**
     * Changes owner of a ticket
     * @param $ticketId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeOwner($ticketId, Request $request)
    {
        $ownerId = $request->owner_id;

        $this->ticket = Ticket::whereId($ticketId)->select("id", "user_id")->first();

        if($this->ticket->user_id == $ownerId){
            return errorResponse(Lang::get("lang.please_select_a_different_owner"));
        }

        $user = User::whereId($ownerId)->where("active", 1)->first();

        if(!$user){
            return errorResponse(Lang::get("lang.user_not_found"));
        }

        $this->ticket->user_id = $ownerId;

        $this->ticket->save();

        return successResponse(Lang::get("lang.successfully_changed"));
    }

  /**
   * Saves custom fields
   * @param  Request $request
   * @return null
   */
  private function saveCustomFields($request)
  {
    // in fork, custom fields can only be saved once ticket is generated
    // in edit ticket, it has to be saved before saving the ticket so that
    // listener enforcements happens at the end
    CustomFormValue::updateOrCreateCustomFields($request->all(), $this->ticket);
  }

  /**
   * Saves all ticket data in ticket table + custom fields
   * @param  Request $request
   * @return null
   */
  private function saveTicketData($request)
  {
    $this->updateTicketObjectWithRequestKey($request, 'assigned_id', 'assigned_to');

    $this->updateTicketObjectWithRequestKey($request, 'help_topic_id', 'help_topic_id');

    $this->updateTicketObjectWithRequestKey($request, 'department_id', 'dept_id');

    $this->updateTicketObjectWithRequestKey($request, 'source_id', 'source');

    $this->updateTicketObjectWithRequestKey($request, 'priority_id', 'priority_id');

    $this->updateTicketObjectWithRequestKey($request, 'type_id', 'type');

    $this->updateTicketObjectWithRequestKey($request, 'location_id', 'location_id');

    $this->updateTicketObjectWithRequestKey($request, 'requester', 'user_id');

    $this->updateTicketObjectWithRequestKey($request, 'status_id', 'status');

    $this->ticket->save();
  }

  /**
   * Updates ticket object with request data
   * @param  Request $request
   * @param  string $keyInTicket
   * @param  string $keyInRequest
   * @return bool
   */
  private function updateTicketObjectWithRequestKey($request, $keyInRequest, $keyInTicket)
  {
      if($request->has($keyInRequest)){
        return $this->ticket->$keyInTicket = $request->$keyInRequest;
      }
  }

  /**
   * Saves subject to the given thread
   * @param Request &$request
   * @param string $subject
   * @return null
   */
  private function updateSubject(string $subject = null)
  {
    $thread = $this->ticket->thread()->orderBy('id','asc')->first();
    // if subject is empty
    // removing trail spaces
    if(!trim($subject)) {
      // ticket helptopic as subject
      $thread->title = $this->ticket->helptopic->topic;
    } else {
      $thread->title = $subject;
    }
    $thread->save();
  }

  /**
   * Creates fork of the given ticket
   * @param int $ticketId
   * @return Response
   */
  public function createFork($ticketId, TicketForkRequest $request)
  {
    $existingTicket = Ticket::where('id',  $ticketId)->first();
    //get all the labels for the existing ticket
    $labelsForTicket = array_flatten($existingTicket->labels()->select('value')->get()->toArray());
    //get all the tags for the existing ticket
    $tagsForTicket = array_flatten($existingTicket->tags()->select('value')->get()->toArray());
    // now create a thread which says ticket has been forked
    $this->ticket = $existingTicket->replicate();
    // $this->ticket = $this->storeLabels($this->ticket->id,$labelsForTicket);
    // making duedate as null so that fresh SLA can be calculated
    $this->ticket->duedate = null;
    $this->ticket->reopened = 0;
    $this->ticket->reopened_at = null;
    $this->ticket->is_manual_duedate = 0;
    $this->saveTicketData($request);

    $newTicketId = $this->ticket->id;

    $this->storeLabels($newTicketId,$labelsForTicket);
    
    $this->storeTags($newTicketId,$tagsForTicket);
    // custom fields can only be saved once ticket is created.
    $this->saveCustomFields($request);

    // copy all the threads
    $this->copyThreadsToNewTicket($existingTicket);

    if($request->has('subject')){
      $this->updateSubject($request->input('subject'));
    }

  // creating thread in existing ticket which says that this ticket has been forked to new ticket
    $this->saveExistingTicketForkMessage($existingTicket);

    // close the old ticket
    $this->closeTicket($existingTicket->id);

    // copies collaborators of old ticket to new ticket
    $this->copyCollaboratorsToNewTicket($existingTicket);

    $responseMessage =  trans('lang.ticket-id-from-to', ['from_id' => "#".$existingTicket->ticket_number,
      'to_id' => "#".$this->ticket->ticket_number]);

    return successResponse($responseMessage);
  }

/**
 * Store the labels of the existing ticket to the newly forked ticket
 *
 * @date   2019-07-23T14:09:20+0530
 *
 * @param  string $ticketid
 * @param  array $labelsForTicket
 *
 */
  public function storeLabels($ticketid, array $labelsForTicket)
  {
    $cont = new LabelController();
    $parameters = [
            'labels' => $labelsForTicket,
            'ticket_id' => $ticketid,
        ];
    $cont->attachTicket(new \Illuminate\Http\Request($parameters));
  }

  /**
 * Store the tags of the existing ticket to the newly forked ticket
 *
 * @date   2019-07-23T14:09:20+0530
 *
 * @param  string $ticketid
 * @param  array $tagsForTicket
 *
 */
  public function storeTags($ticketid, array $tagsForTicket)
  {
    $cont = new TagController();
    $parameters = [
            'tags' => $tagsForTicket,
            'ticket_id' => $ticketid,
        ];
    $cont->addToFilter(new TagsCreateFromInboxRequest($parameters));
  }

  /**
   * Copies collaborators from old ticket to new ticket
   * @param  Ticket $existingTicket
   * @return null
   */
  private function copyCollaboratorsToNewTicket(Ticket $existingTicket)
  {
    $oldTicketCCs = Collaborator::where('ticket_id', $existingTicket->id)->get();

    foreach ($oldTicketCCs as $oldTicketCC) {

      Collaborator::create([
          'ticket_id' => $this->ticket->id,
          'user_id' => $oldTicketCC->user_id,
          'isactive' => $oldTicketCC->isactive,
          'role' => $oldTicketCC->role
      ]);
    }
  }

  /**
   * Creates thread in existing ticket which says that this ticket has been forked to a new ticket
   * @param  Ticket $existingTicket
   * @return null
   */
  private function saveExistingTicketForkMessage(Ticket $existingTicket)
  {
    $ticketUrl = "<a target=_blank href=".faveoUrl('thread/' . $this->ticket->id).">".$this->ticket->ticket_number."</a>";

    // create thread that ticket has been forked
    // update existing ticket status
    $existingTicket->thread()->create([
      'user_id'     => Auth::user()->id,
      'is_internal' => 1,
      'body'        => trans('lang.ticket-forked-to', ['url' => $ticketUrl]),
    ]);
  }

  /**
   * Create forks of threads of existing ticket under new ticket
   * @param Ticket $existingTicket
   * @return bool
   */
  private function copyThreadsToNewTicket(Ticket $existingTicket)
  {
    $threads = $existingTicket->thread()->orderBy('id','asc')->get();

    foreach ($threads as $thread) {

      $oldThreadAsArray = array_except($thread->toArray(), ['id', 'ticket_id', 'created_at', 'updated_at']);

      $newThread  = $this->ticket->thread()->create($oldThreadAsArray);

      $this->addAttachmentsToForkedTicket($oldThreadAsArray, $newThread);

      $thread->emailThread()->update(['ticket_id' => $newThread->ticket_id,'thread_id' => $newThread->id]);
    }

    // saving new ticket form message
    $ticketUrl = "<a target=_blank href=".faveoUrl('thread/' . $existingTicket->id).">$existingTicket->ticket_number</a>";

    // a new thread which says that this ticket is a fork of the old ticket
    // create thread that ticket has been forked
    // update existing ticket status
    $this->ticket->thread()->create([
      'is_internal' => 1,
      'body'        => trans('lang.ticket_forked_from', ['url' => $ticketUrl])
    ]);

  }

  /**
   * create copy of the attachments of old ticket
   * @param array $thread_array
   * @param model $thread_data
   * @return void
   */
  public function addAttachmentsToForkedTicket($thread_array, $thread_data)
  {
    $thread_attach = checkArray('attach', $thread_array);

    if ($thread_attach) {

      foreach ($thread_attach as $attach) {

        $attach_array = array_except($attach, ['id', 'ticket_id', 'created_at', 'updated_at']);

        $thread_data->attach()->create($attach_array);
      }
    }
  }

  /**
   * change the status of the old ticket
   * @param integer $ticketId
   * @return null
   */
  public function closeTicket($ticketId)
  {
    $statusId = Status::whereHas('type', function($q) {
                $q->where('name', 'Closed');
            })->where('default', 1)->value('id');

    (new TicketController)->changeStatus($ticketId, $statusId);
  }


  /**
   * Forwards ticket to a given email
   * @param  Request $request
   * @return
   */
  public function forwardTicket(TicketForwardRequest $request)
  {
    try {
      $userEmails = $request->emails;
      $sendAttachments = $request->send_attachments;

      foreach ($userEmails as $userEmail) {
        $ticketId = $request->ticket_id;

        $this->ticket = Ticket::where('id', $ticketId)->select('id','dept_id','ticket_number')->first();

        $this->sendTicketForwardMail($this->ticket, $userEmail, $sendAttachments);
      }

      $this->logTicketForwardThread($userEmails, Auth::user());

      return successResponse(Lang::get('lang.ticket-forwarded-successfully'));

    } catch(Exception $e) {
      Logger::exception($e);
      return errorResponse($e->getMessage());
    }
  }

  /**
   * Logs that ticket has been forwarded in ticket timeline as an internal_note
   * @return void
   */
  private function logTicketForwardThread(array $userEmails, User $forwardBy) : void
  {
    $forwardedTo = $this->getRecieversProfileLinkAsString($userEmails);

    $forwardBy = userProfilePath($forwardBy);

    $ticketForwardInternalNote = Lang::get('lang.ticket_has_been_forwarded', ['to'=>"<b>$forwardedTo</b>", 'by'=> "</b>$forwardBy</b>"]);

    Thread::create([
      'is_internal'=>1, 'ticket_id'=>$this->ticket->id, 'body'=> $ticketForwardInternalNote, 'poster'=>'support', 'thread_type' => 'internal_activity_alert'
    ]);
  }

  /**
   * gets list of users as a link. For eg,
   * there are 2 emails passed, testOne@test.com and testTwo@test.com. Now,
   * testOne@test.com is a user in faveo, so it should be an anchor link.
   * but testTwo@test.com is not a faveo user, so it should not be.
   * so result will be "<a href=''>testOne@test.com</a>, testTwo@test.com"
   * @param  array  $userEmails  array of user emails
   * @return string
   */
  private function getRecieversProfileLinkAsString(array $userEmails) : string
  {
    $userProfileLinks = [];

    foreach ($userEmails as $email) {
      // check if user exists in the DB. If yes, form its user profile link.
      $user = User::where('email', $email)->select('id', 'email', 'first_name', 'last_name', 'user_name')->first();

      if($user){
        // adding additional space so that while thread creation, spaces are even
        $userProfileLinks[] = userProfilePath($user);
      } else {
        // adding additional space so that while thread creation, spaces are even
        $userProfileLinks[] = $email;
      }
    }
    return implode(', ',$userProfileLinks);
  }

  /**
   * Sends ticket forwarding mail to passed user
   * @param  Ticket $ticket    Ticket which is getting forwarded
   * @param  User    $forwardTo User to whom ticket has to be forwarded
   * @return null
   */
  private function sendTicketForwardMail(Ticket $ticket, string $forwardTo, bool $sendAttachments = null)
  {
    $ticketConversation = Template::getTicketThreadsTemplate($ticket->id);

    $to = ['name' => '', 'email' => $forwardTo ];

    $forwardBy = Auth::user();

    $ticketSubject = "FW:". $ticket->firstThread->title . "[#".$ticket->ticket_number."]";

    // if send attachment is false, it means only inline images are required
    $attachments = $this->getTicketAttachments($ticket->id, !$sendAttachments);

    $message = ['subject'=> $ticketSubject,'scenario'=>'ticket-forwarding','attachments'=>$attachments];

    $userProfileLink = Config::get('app.url').'/user'.'/'.$forwardBy->id;

    $forwardBy = "<a href=$userProfileLink target=_blank>$forwardBy->full_name</a>";

    $templateVariables = [
      'ticket_number' => $ticket->ticket_number,
      'user_profile_link'=> $forwardBy,
      'ticket_conversation'=>$ticketConversation
    ];

    $phpMailController = new PhpMailController;

    $from = $phpMailController->mailfrom('0', $ticket->dept_id);

    $phpMailController->sendmail($from, $to, $message, $templateVariables);
  }

    /**
   * gets attachment belong to a thread
   * @param string|int $ticketId
   * @param bool $onlyInline
   * @return array
   */
  private function getTicketAttachments($ticketId, bool $onlyInline)
  {
    // get all threadIds and get all attachments
    $threadIds = Thread::where('ticket_id', $ticketId)->where('is_internal', 0)->pluck('id')->toArray();
    // not sure why PhpMailController asks for `file_path` instead of just `path`
    $attachmentQuery = Attachment::whereIn('thread_id', $threadIds);

    // if send attachment is false, it should only send inline images, else all images
    if($onlyInline){
      $attachmentQuery = $attachmentQuery->where('poster','inline');
    }

    return $attachmentQuery->get()->toArray();
  }
}
