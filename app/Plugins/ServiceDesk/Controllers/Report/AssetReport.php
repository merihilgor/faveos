<?php

namespace App\Plugins\ServiceDesk\Controllers\Report;

use App\Http\Controllers\Controller;

class AssetReport extends Controller {

    public function reports() {
        return view('service::reports.reports');
    }

}
