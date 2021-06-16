<?php

namespace App\Plugins\Twitter\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Twitter\Controllers\TwitterController;
use Logger;

class TicketFetchCommand extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fetches tickets from twitter.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        $twitter = new TwitterController(new \App\Plugins\Twitter\Controllers\Core\SettingsController);
        $twitter->getTweets();
        $twitter->getMessages();
    }
}
