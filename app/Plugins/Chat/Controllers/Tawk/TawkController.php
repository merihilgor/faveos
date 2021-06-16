<?php

namespace App\Plugins\Chat\Controllers\Tawk;

use Logger;
use App\User;
use App\Plugins\Chat\Model\Chat;
use App\Http\Controllers\Controller;
use App\Plugins\Chat\Model\TawkChat;

class TawkController extends Controller
{
    /**
     * Department to generate ticket
     * @var  $request
     */
    private $department;

    /**
     * Helptopic to generate ticket
     * @var  $request
     */
    private $helptopic;

    /**
     * @var Request $request
     */
    private $request;

    /**
     * Email of the Requester
     * @var $email
     */
    private $email;

    /**
     * body from the Requester
     * @var $body
     */
    private $body;

    /**
     * subject from the Requester
     * @var $subject
     */
    private $subject;

    /**
     * chatId of the Requester
     * @var $chatId
     */
    private $chatId;


     /**
     * name of the Requester
     * @var $name
     */
    private $name;


    public function __construct($request,$dept,$helptopic)
    {
        $this->request = $request;
        $this->department = $dept;
        $this->helptopic = $helptopic;
        
    }

    /**
     * Verify the request is from Tawk or not.
     * @param \Illuminate\Http\Request $request
     * @return Boolean
     */
    private function isThisRequestSecure()
    {
        $digest = hash_hmac('sha1', $this->request->getContent(), $this->getTawkSecret());
        return $this->request->header('X_TAWK_SIGNATURE') !== $digest ;
    }
    

    /**
     * Gets the Tawk Webhook Secret Key from database
     * @return mixed
     */
    private function getTawkSecret()
    {
        return Chat::where('short','tawk')->first()->secret_key;
        
    }

    /**
     * Twilio Webhook EntryPoint
     */
    public function webhookEntry()
    {
        if($this->isThisRequestSecure()){
            Logger::exception(
                new \Exception(trans('chat::lang.webhook_insecure'))
            );
            return errorResponse(trans('chat::lang.webhook_insecure'));
        }
        $request = $this->request->all();
        $event = $request['event'];
        $this->prepareDataBasedOnEvent($event);

    }

    /**
     * Prepares the data required to create ticket based on various events.
     * @param String $event_type
     */
    private function prepareDataBasedOnEvent($event_type)
    {
        switch($event_type) {
            case "ticket:create" :
                $this->prepareDataForTicketCreateEvent();
                break;
            case "chat:end" :
                $this->prepareDataForChatEndEvent();
                break;
            case "chat:start": 
                $this->persistChat();
                break;
        }
    }

    /**
     * Prepares data to create ticket when the ticket:create event is fired from Tawk.to
     * 
     */
    private function prepareDataForTicketCreateEvent()
    {
        $request = $this->request->all();
        $this->email = $request['requester']['email'];
        $this->name  = $request['requester']['name'];
        $this->subject = $request['ticket']['subject'];
        $this->body = $request['ticket']['message'];
        $this->generateTicket();
     }


     /**
     * Prepares data to create ticket when the chat:end event is fired from Tawk.to
     * 
     */
    private function prepareDataForChatEndEvent()
    {
        $request = $this->request->all();
        $this->chatId = $request['chatId'];
        $this->email  = $request['visitor']['email'];
        $this->addInternalNote($this->chatId);
    }


    /**
     * Generates Ticket
     * @return Array $result
     */
    private function generateTicket()
    {
        $ticketController = new \App\Http\Controllers\Agent\helpdesk\TicketController();
        $priority = \App\Model\helpdesk\Settings\Ticket::find(1)->priority ?: '';
        $type = \App\Model\helpdesk\Manage\Tickettype::select('id')->first()->id ?: '';
        $user = $this->checkUser($this->email);
        $user_name = ($user) ? $user['user_name'] : $this->email;
        $source = $ticketController->getSourceByname("Chat")->id;

        if($user)  {

            if($user['active'] == '1') {
                $result = $ticketController->createTicket(
                    $user['id'], $this->subject, $this->body, 
                    $this->helptopic, '', $priority, 
                    $source, [], $this->department, 
                    null, [], "", 
                    $type, [], [], 
                    $email_content = [], $company = "",$domainId=""
                );
               
            }
        }

        else {
            $result = $ticketController->create_user(
                $user_name, "", $this->subject, 
                $this->body, "", "", "",
                $this->helptopic, '', $priority, $source, 
                [], $this->department, null, 
                [], "", "", 
                $type,[],[]
            );


        }

        return $result;

    }


    /**
     * Add Internal Note on Chat End
     */
    private function addInternalNote($chatId)
    {
        $chat = TawkChat::where('chat_id',$chatId)->first();
        if($chat) {
            $this->body = $chat->body;
            $this->chatId = $chat->chat_id;
            $this->email = ($chat->from)?:$this->email;
            $this->subject = "Message from Tawk.to";
            $this->generateTicket();
        }

    }


    /**
     * Checks whether the user exists in the system based on the email
     * From which the tawk message is recieved.
     * @param string $email
     * @return mixed
     */
    private function checkUser($email)
    {
        $user = User::where('email',$email);
        return ($user->first()) ? ($user->first()->toArray()) : '';
    }

    /**
     * Saves information about tawk message for further processing.
     * @param string $from
     * @param array $result
     */
    private function persistChat()
    {
        $request = $this->request->all();
        $tawk_chat = [
            'chat_id' => $request['chatId'],
            'body' => $request['message']['text']
        ];
        TawkChat::create($tawk_chat);
    }
}


