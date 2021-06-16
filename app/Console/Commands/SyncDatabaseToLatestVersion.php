<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use Illuminate\Console\Command;
use App\Model\Update\BarNotification;
use App\Http\Controllers\Update\SyncFaveoToLatestVersion;

class SyncDatabaseToLatestVersion extends LoggableCommand
{

  //NOTE: it is made only for testing purpose and must be removed once code is merged

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates faveo database to latest version';

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
        echo (new SyncFaveoToLatestVersion)->sync();
        $this->clearUpdateNotification();
       
    }

    /**
     * Deletes row from bar_notification table due to which up new update available notification shows up.
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @date   2020-03-05T10:36:56+0530
     *
     * @return void
     */
    public function clearUpdateNotification()
    {
        $notify = BarNotification::where('key', 'new-version')->delete();
    }


}
