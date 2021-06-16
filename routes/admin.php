<?php

Route::resource('listener', 'Admin\helpdesk\Listener\ListenerController', ['except' => ['show']]);
Route::post('listener/order','Admin\helpdesk\Listener\ListenerController@reorder');
Route::post('listener/update/{id}','Admin\helpdesk\Listener\ListenerController@update');


/**
 * Workflow
 */

Route::post('workflow/order','Admin\helpdesk\WorkflowController@reorder');


/**
 * Webhook
 */

Route::get('webhook',['as'=>'webhook','uses'=>'Common\WebhookController@create']);
Route::post('webhook','Common\WebhookController@store');
