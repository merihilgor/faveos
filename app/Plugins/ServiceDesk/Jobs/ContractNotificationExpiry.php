<?php

namespace App\Plugins\ServiceDesk\Jobs;

use Illuminate\Bus\Queueable;
use App\Model\MailJob\QueueService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\Common\FaveoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController;
use App\Http\Controllers\Common\PhpMailController;
use lang;
use Exception;
use Log;

class ContractNotificationExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $agent, $contract;

    public function __construct($agent, $contract)
    {
        $this->setDriver();
        $this->agent = $agent;
        $this->contract = $contract;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info(app('queue')->getDefaultDriver());
            $this->sendContractNotificationExpiryEmail();   
        } catch (Exception $ex) {
            loging('servicedesk', $ex->getMessage());
        }     
    }

  /**
   * Function to send contract expiry email to selected agents and vendor
   * @return 
   */
  private function sendContractNotificationExpiryEmail()
  {
      $phpMailController = new PhpMailController();
      $from = $phpMailController->mailfrom('1', '0');
      $to = ['name' => $this->agent['full_name'], 'email' => $this->agent['email']];
      if(!array_key_exists('full_name', $this->agent)) {
          $to = ['email' => $this->agent['email']];
      }
      $message = ['message' => '','scenario' => 'notify-expiry-contract'];
      $templateVariables = ['contract_id' => $this->contract['id'],
        'contract_name' => $this->contract['name'],
        'contract_expiry' => $this->contract['contract_end_date'],
        'contract_link' => faveoUrl('service-desk/contracts/' . $this->contract['id'] . '/show')
      ];
      $phpMailController->sendmail($from, $to, $message, $templateVariables);
  }

  /**
   * Function to set queue driver
   * @return
   */
  private function setDriver() {
      $queue_driver = 'sync';
      if ($driver = QueueService::where('status', 1)->first()) {
          $queue_driver = $driver->short_name;
      }
      app('queue')->setDefaultDriver($queue_driver);
    }

}
