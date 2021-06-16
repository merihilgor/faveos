<?php

namespace App\Jobs;

use App\User;
use App\Jobs\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use App\Model\MailJob\QueueService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\Common\FaveoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\helpdesk\Utility\CountryCode;
use App\Http\Requests\helpdesk\Ticket\AgentPanelTicketRequest;
use App\Http\Controllers\Agent\helpdesk\UserController;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use Illuminate\Http\UploadedFile;
use App\Traits\UserVerificationHelper;

class BatchTicketCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UserVerificationHelper;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $data, $users_list = [], $file_name, $ticket, $code;

    public function __construct($data, $file_name)
    {
        $this->setDriver();
        $this->data = $data;
        $this->file_name = $file_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TicketController $ticket, CountryCode $code)
    {
        \Log::info(app('queue')->getDefaultDriver());
        $this->users_list = [];
        $this->ticket = $ticket;
        $this->code = $code;

        $this->data['attachments'] = [];

        foreach ($this->data as $field => $value) {

          if(is_array($value)){

            foreach ($value as $index => $element) {

              if(\Storage::exists($element)) {

                $filePath = storage_path(). DIRECTORY_SEPARATOR. 'app'. DIRECTORY_SEPARATOR .'attachments'. DIRECTORY_SEPARATOR .$element;

                $this->data['attachments'][] = new UploadedFile($filePath, $element);

                unset($this->data[$field][$index]);
              }
            }
          }
        }

        $this->setUsers();
    }

    private function setUsers(){
        \Log::info("set user executed");
        \Excel::load(storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'attachments'.DIRECTORY_SEPARATOR.$this->file_name, function($reader){
                // Getting all results
                // $results = $reader->get();

                // ->all() is a wrapper for ->get() and will work the same
                $results = $reader->all();
                if(!isset($results->first()->email))
                    $results = $results[0];
                $this->createBatchTickets($results);
            });
    }

    private function createBatchTickets($results){

        foreach ($results as $key => $value) {
            if($user = User::where('email', $value->email)
              ->where('email', '!=', null)->where('email','!=','')->first()){
                array_push($this->users_list, $user->user_name);
            }
            else{
                $isValidEmail = filter_var($value->email, FILTER_VALIDATE_EMAIL);
                if(!$isValidEmail){
                  continue;
                }

                // Creating user in faveo if not found
                $request = new \Illuminate\Http\Request;
                $request->merge([
                    'email' => $value->email,
                    'user_name' => $value->email,
                    'first_name' => $value->first_name,
                    'last_name' => $value->last_name,
                    'role' => 'user',
                    'full_name' => $value->first_name. ' '. $value->last_name
                ]);
                // dd($request->all());
                $obj = new UserController(new \App\Http\Controllers\Common\PhpMailController);
                $new_user = $obj->createRequester($request, 'batch-ticket' );

                // NOTE: adding it so that it skips the user which gives exception in user creation
                // REASON: createRequester is an outdated method which returns a response, from which
                // getting the exception is not simple
                if(!is_array($new_user)){
                    continue;
                }
                $this->setEntitiesVerifiedByModel(User::find($new_user['id']));
                array_push($this->users_list, $new_user['id']);
            }
        }
        \Log::info("Executing batch Ticket");

        foreach ($this->users_list as $key => $value) {
            $this->data['requester'] = $value;

            // get all attachments from the request in the memory
            $request = new AgentPanelTicketRequest($this->data);
            $this->ticket->post_newticket($request,$this->code ,true);
        }
        \Log::info("Batch Ticket processed successfully");
        try{
            $notify = [
                'message' => __('lang.batch-ticket-created-success'),
                'to'      => \Auth::user()->id,
                'by'      => \Auth::user()->id,
                'table'   => "",
                'row_id'  => "",
                'url'     => url('/tickets'),
            ];
            $email_data = [
                "content" => "Hi ".\Auth::user()->user_name. ",<br> <br>".__('lang.batch-ticket-created-success'),
                "subject" => __('lang.Ticket-created-successfully')
            ];
            \Event::dispatch('batch.ticket.notify', [$notify]);
            \Event::dispatch('batch.ticket.email.notify', [$email_data]);
        }
        catch(\Exception $e){
            \Log::info("Exception caught while sending batch ticket notify: ".$e);
        }
        // $this->notifyUser();
    }

    private function setDriver(){
        $queue_driver = 'sync';
        if($driver = QueueService::where('status', 1)->first())
            $queue_driver = $driver->short_name;
        app('queue')->setDefaultDriver($queue_driver);
    }

    public function notifyUser(){
        $noti = \App\Model\helpdesk\Notification\Notification::create([
                    'message' => __('lang.batch-ticket-created-success'),
                    'to'      => \Auth::user()->id,
                    'by'      => \Auth::user()->id,
                    'table'   => "",
                    'row_id'  => "",
                    'url'     => "",
        ]);

        $content['content'] = "Hi ".\Auth::user()->user_name. ",<br> <br>".__('lang.batch-ticket-created-success');
        $content['subject'] = __('lang.Ticket-created-successfully');
        \Log::info($content);
        $faveoMail =  new FaveoMail;
        $faveoMail->sendMail(\Auth::user()->email,  $content, []);
    }
}
