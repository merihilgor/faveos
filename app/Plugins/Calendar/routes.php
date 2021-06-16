<?php

    \Event::listen('agent-panel-navigation-data-dispatch', function (&$navigationContainer) {
        $task = new App\Plugins\Calendar\Controllers\TaskController();
        $task->injectTaskAgentNavigation($navigationContainer);
    });

    \Event::listen('admin-panel-navigation-data-dispatch', function (&$navigationContainer) {
        (new App\Plugins\Calendar\Controllers\CalendarAdminNavigationController)
          ->injectCalendarAdminNavigation($navigationContainer);
    });

    \Event::listen('agent-panel-scripts-dispatch', function () {
        echo "<script src=".bundleLink('js/calendar.js')."></script>";
    });

    Route::group(['prefix' => 'tasks','middleware' => ['web']], function () {
        /////////////////////////////////////////Project///////////////////////////////////////////////////////
        Route::post('project/create', 'App\Plugins\Calendar\Controllers\ProjectController@store');

        Route::get('project/view', 'App\Plugins\Calendar\Controllers\ProjectController@index');

        Route::delete('project/delete/{projectId}', 'App\Plugins\Calendar\Controllers\ProjectController@destroy');

        Route::put('project/edit/{projectId}', 'App\Plugins\Calendar\Controllers\ProjectController@edit');

        Route::get('project/view-with-tasklists', 'App\Plugins\Calendar\Controllers\ProjectController@indexWithTaskList');

        Route::get('project/view-with-tasks', 'App\Plugins\Calendar\Controllers\ProjectController@indexAll');

        Route::get('projects', 'App\Plugins\Calendar\Controllers\ProjectController@returnProjects');

        /////////////////////////////////////////TaskList/////////////////////////////////////////////////
        Route::post('tasklist/create', 'App\Plugins\Calendar\Controllers\TaskListController@store');

        Route::get('tasklist/view', 'App\Plugins\Calendar\Controllers\TaskListController@index');

        Route::delete('tasklist/delete/{tasklistId}', 'App\Plugins\Calendar\Controllers\TaskListController@destroy');

        Route::put('tasklist/edit/{tasklistId}', 'App\Plugins\Calendar\Controllers\TaskListController@edit');

        Route::get('tasklists', 'App\Plugins\Calendar\Controllers\TaskListController@returnTaskLists');

    });

    Route::group(['prefix' => 'tasks','middleware' => ['web', 'role.agent']], function () {

    //////////////////////////////////////////Task/////////////////////////////////////////////////////////////
        Route::get('get-all-tasks', 'App\Plugins\Calendar\Controllers\TaskController@returnTasks');

        Route::resource('task', 'App\Plugins\Calendar\Controllers\TaskController');

        Route::get('get-task-by-id/{id}', 'App\Plugins\Calendar\Controllers\TaskController@getTaskById');

        Route::get('change-task/{id}/{status}', 'App\Plugins\Calendar\Controllers\TaskController@changeStatus');

        Route::get('list', 'App\Plugins\Calendar\Controllers\TaskController@getListOfTasks');

        Route::get('get-all-ticket-tasks', 'App\Plugins\Calendar\Controllers\TaskController@getAllTicketTasks');

        //For Calendar View
        Route::get('get-all-tasks-for-calendar', 'App\Plugins\Calendar\Controllers\TaskController@getTasks');

        /////////////////////////////////////////View///////////////////////////////////////////////////
        Route::get('/', 'App\Plugins\Calendar\Controllers\TaskController@getCalendarTaskPage')
            ->name('calender.alltasks');


        Route::get('project/edit/{projectID}', 'App\Plugins\Calendar\Controllers\ProjectController@editForm')
            ->name('project.edit');

        Route::get('tasklist/edit/{tasklistID}', 'App\Plugins\Calendar\Controllers\TaskListController@editForm')
            ->name('tasklist.edit');
    });

    Route::group(['prefix' => 'tasks', "middleware" => ['web','role.admin']], function () {
        Route::get('settings', 'App\Plugins\Calendar\Controllers\TaskController@viewWithTaskSettings')
            ->name('tasks.settings');
    });
