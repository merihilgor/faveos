<?php

\Event::listen('ticket.assign', function($event) {
    $dept_id = $event['department'];
    $type_id = $event['type'];
    $locationId = $event['location'];
    $assign = new \App\AutoAssign\Controllers\AutoAssignController();
    return $assign->getAssigneeId($dept_id,$type_id,$locationId);
});
\Event::listen('user.logout', function() {
    $assign = new \App\AutoAssign\Controllers\AutoAssignController();
    $assign->handleLogout();
});

\Event::listen('user.login', function() {
    $assign = new \App\AutoAssign\Controllers\AutoAssignController();
    $assign->handleLogin();
});

\Event::listen('settings.ticket.view', function() {
    $set = new App\AutoAssign\Controllers\SettingsController();
    echo $set->settingsView();
});

Route::group(['middleware' => ['web','auth','roles']], function() {
    Route::get('auto-assign', [
        'as' => 'auto.assign',
        'uses' => 'App\AutoAssign\Controllers\SettingsController@getSettings'
    ]);

    Route::post('auto-assign', [
        'as' => 'post.auto.assign',
        'uses' => 'App\AutoAssign\Controllers\SettingsController@postSettings'
    ]);
});
