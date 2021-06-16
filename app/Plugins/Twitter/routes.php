<?php

Route::group(['middleware' => ['web', 'auth', 'roles']], function() {
    Route::group(['prefix' => 'twitter'], function() {

        Route::get('app','App\Plugins\Twitter\Controllers\TwitterController@getTwitterApp');
        Route::put('update/{id}','App\Plugins\Twitter\Controllers\TwitterController@updateApp');
        Route::get('credentials', 'App\Plugins\Twitter\Controllers\TwitterController@getCredentials');
        Route::post('create', 'App\Plugins\Twitter\Controllers\TwitterController@createApp');
        Route::delete('delete/{id}','App\Plugins\Twitter\Controllers\TwitterController@deleteApp');
        Route::get('dm', 'App\Plugins\Twitter\Controllers\TwitterController@getMessages');
        Route::get('twit', 'App\Plugins\Twitter\Controllers\TwitterController@getTweets');
        Route::get('mentions','App\Plugins\Twitter\Controllers\TwitterController@getMentionTweets');
        Route::get('interval/{value}', 'App\Plugins\Twitter\Controllers\TwitterController@changeNewTicketInterval');
        Route::get('condition/{enable}','App\Plugins\Twitter\Controllers\TwitterController@changeCondition');
        Route::get('check/condition', 'App\Plugins\Twitter\Controllers\TwitterController@checkCondition');
        Route::get('update/{hashTagtext}','App\Plugins\Twitter\Controllers\TwitterController@updateTwitterQuery');
        Route::get('settings', 'App\Plugins\Twitter\Controllers\TwitterController@settingsView')->name('twitter.settings');

    }); //prefix twitter
    
    \Event::listen('Reply-Ticket',function($event){
        
        $controller = new \App\Plugins\Twitter\Controllers\ReplyToTwitter();
        $controller->replyTwitter($event);
        
    });

});

\Event::listen('admin-panel-navigation-data-dispatch', function(&$navigationContainer) {
    (new App\Plugins\Twitter\Controllers\TwitterAdminNavigationController)
      ->injectTwitterAdminNavigation($navigationContainer);
  });

