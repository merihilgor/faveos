<?php

namespace App\Plugins\ServiceDesk\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController;

class ContractNotificationExpiry extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:notification-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will send contract expiry notification by email to selected agents and vendor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        $contract = new ApiContractController();
        $contract->sendContractNotificationExpiry();
    }
}
