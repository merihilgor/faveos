<?php

namespace App\Plugins\ServiceDesk\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\ServiceDesk\Controllers\Contract\ContractController;

class ContractStatusExpired extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:status-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will change contract status to expired';

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
        $contract = new ContractController();
        $contract->makeContractStatusExpired();
    }
}
