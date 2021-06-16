<?php

namespace App\Plugins\Calendar\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plugins\Calendar\Model\TaskList;
use App\Plugins\Calendar\Requests\TaskListRequest;

class TaskListController extends Controller
{
    public function __construct()
    {
        $this->middleware('role.admin')->except(['index']);
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?: 10;
        $query = TaskList::query();
        $searchTerm = request('search-query');
        $query->when((bool) $searchTerm, function ($q) use ($searchTerm) {
           return $q->where('name', 'LIKE', "%$searchTerm%");
        });
        return successResponse('', $query->simplePaginate($limit));
    }

    public function store(TaskListRequest $request)
    {
        return (TaskList::create($request->all()))
        ? successResponse(trans('Calendar::lang.tasklist_created'))
        : errorResponse(trans('Calendar::lang.tasklist_not_created'));
    }


    public function edit($tasklistId, Request $request)
    {
        return TaskList::where('id', $tasklistId)->update($request->all())
        ? successResponse(trans('Calendar::lang.tasklist_updated'))
        : errorResponse(trans('Calendar::lang.tasklist_not_updated'));
    }


    public function destroy($tasklistId)
    {
        $tasklist = TaskList::where('id', $tasklistId)->first();
        if ($tasklist->tasks()) {
            $tasklist->tasks()->delete();
        }
        return ($tasklist->delete())
        ? successResponse(trans('Calendar::lang.tasklist_deleted'))
        : errorResponse(trans('Calendar::lang.tasklist_not_deleted'));
    }

    public function returnTaskLists(Request $request)
    {
        $query = TaskList::query();
        $query->when((bool)($request->searchTerm), function ($q) use ($request) {
            return $q->where('name', 'LIKE', "%$request->searchTerm%");
        });
        $query->when((bool)($request->tasklistIds), function ($q) use ($request) {
            return $q->whereIn('id', $request->tasklistIds);
        });
        $tasklists =  $query->with('project')
                // ->select('id','name','created_at')
                ->orderBy((($request->sortField) ? : 'created_at'), (($request->sortOrder) ? : 'asc'))
                ->paginate((($request->limit) ? : '10'))
                ->toArray();

        $tasklists['tasklists'] = $tasklists['data'];
        unset($tasklists['data']);

        return successResponse('', $tasklists);
    }

    public function editForm()
    {
        return view('Calendar::tasklistEdit');
    }
}
