<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['roles']);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $details = \App\Model\Api\ApiSetting::pluck('value', 'key')->toArray();
        return view('themes.default1.common.api.webhook', compact('details'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'create_ticket_detail' => 'url',
            'update_ticket_detail' => 'url',
        ]);
        try {
            $settings = new \App\Model\Api\ApiSetting();
            $values   = $request->except('_token');
            foreach ($values as $key => $value) {
                $settings->updateOrCreate(['key' => $key], ['value' => $value]);
            }
            return redirect()->back()->with('success', 'Saved Successfully');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    
}
