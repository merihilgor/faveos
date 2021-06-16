<?php

namespace App\Http\Controllers\Common\TicketsWrite;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Manage\Sla\SlaApproachEscalate;
use App\Model\helpdesk\Manage\Sla\SlaViolatedEscalate;
use App\Model\helpdesk\Ticket\TicketSla;
use App\Traits\EnforcerHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Model\helpdesk\Ticket\TicketWorkflow;
use App\Model\helpdesk\Ticket\TicketListener;
use App\Model\helpdesk\Ticket\TicketAction;
use App\Model\helpdesk\Ticket\TicketRule;
use App\Model\helpdesk\Ticket\TicketEvent;
use App\Http\Requests\helpdesk\Ticket\TicketEnforcerRequest;
use InvalidArgumentException;
use Lang;
use Exception;
use Response;
use App\Traits\EnhancedDependency;

/**
 * Handles workflow/listener/SLA related activity (APIs)
 * @author manish verma <manish.verma@ladybirdweb.com>
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class ApiEnforcerController extends Controller
{
    use EnhancedDependency, EnforcerHelper;

    protected $searchQuery;

    protected $limit;

    protected $sortField;

    protected $sortOrder;

    public function __construct()
    {
        $this->middleware("role.admin");
    }

    /**
     * handles both edit and creating of a workflow
     * @param $request TicketEnforcerRequest
     * @return JsonResponse
     */
    public function postEnforcer(TicketEnforcerRequest $request)
    {
        try {
            $enforcerFromRequest = $request->input('data');

            $type = $request->input('type');

            //if type is passed as listener, it will query from listener table else workflow table
            $modelName = $this->getParentModel($type);

            // if id is null (if it is in create mode)
            !$enforcerFromRequest['id'] && $enforcerFromRequest['order'] = $this->getOrderForEnforcer($modelName);

            $enforcer = $modelName::updateOrCreate(['id' => $enforcerFromRequest['id']], $enforcerFromRequest);

            switch ($type){

                case "listener":
                    $this->saveListener($enforcerFromRequest, $enforcer);
                    break;

                case "sla":
                    $this->saveSla($enforcerFromRequest, $enforcer);
                    break;

                default:
                    $this->saveWorkflow($enforcerFromRequest, $enforcer);
            }

            $message = $enforcerFromRequest['id'] ? 'updated_successfully' : 'created_successfully';

            return successResponse(Lang::get("lang.$message"));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Saves workflow's rules and actions
     * @param array $enforcerFromRequest associative array of enforcer which contains rules and actions
     * @param $enforcer
     */
    private function saveWorkflow($enforcerFromRequest, $enforcer)
    {
        isset($enforcerFromRequest['rules']) && $this->saveRules($enforcerFromRequest['rules'], $enforcer);

        isset($enforcerFromRequest['actions']) && $this->saveActions($enforcerFromRequest['actions'], $enforcer);
    }

    /**
     * Saves listener's rules and actions
     * @param array $enforcerFromRequest associative array of enforcer which contains rules, actions and events
     * @param $enforcer
     */
    private function saveListener($enforcerFromRequest, $enforcer)
    {
        isset($enforcerFromRequest['rules']) && $this->saveRules($enforcerFromRequest['rules'], $enforcer);

        isset($enforcerFromRequest['actions']) && $this->saveActions($enforcerFromRequest['actions'], $enforcer);

        isset($enforcerFromRequest['events']) && $this->saveEvents($enforcerFromRequest['events'], $enforcer);
    }

    /**
     * Saves SLA's rules and actions
     * @param array $enforcerFromRequest associative array of enforcer which contains rules, actions and events
     * @param $enforcer
     * @throws Exception
     */
    private function saveSla($enforcerFromRequest, $enforcer)
    {
        isset($enforcerFromRequest['rules']) && $this->saveRules($enforcerFromRequest['rules'], $enforcer);

        isset($enforcerFromRequest['reminders']) && $this->saveReminders($enforcerFromRequest['reminders'], $enforcer);

        isset($enforcerFromRequest['sla_meta']) && $this->saveSlaMetas($enforcerFromRequest['sla_meta'], $enforcer);
    }

    /**
     * gets listener by the passed Id
     * @param integer $enforcerId
     * @return JsonResponse
     */
    public function getEnforcer($type, $enforcerId)
    {
        $modelName = $this->getParentModel($type);

        $baseQuery = $modelName::whereId($enforcerId);
        switch ($type){

            case "listener":
                return $this->getListener($baseQuery);

            case "sla":
                return $this->getSla($baseQuery);

            default:
                return $this->getWorkflow($baseQuery);
        }
    }

    /**
     * Gets workflow of the parent Id
     * @param Builder $baseQuery
     * @return JsonResponse
     */
    private function getWorkflow(Builder &$baseQuery)
    {
        $this->appendRulesQuery($baseQuery);

        $this->appendActionsQuery($baseQuery);

        $enforcer = $baseQuery->first();

        if (!$enforcer) {
            return errorResponse(Lang::get('lang.not_found'));
        }

        $this->formatValue($enforcer['rules']);

        $this->formatValue($enforcer['actions']);

        return successResponse('', ['workflow' => $enforcer]);
    }

    /**
     * Gets workflow of the parent Id
     * @param Builder $baseQuery
     * @return JsonResponse
     */
    private function getListener(Builder &$baseQuery)
    {
        $this->appendRulesQuery($baseQuery);

        $this->appendActionsQuery($baseQuery);

        $this->appendEventsQuery($baseQuery);

        $enforcer = $baseQuery->first();

        if (!$enforcer) {
            return errorResponse(Lang::get('lang.not_found'));
        }

        $this->formatValue($enforcer['rules']);

        $this->formatValue($enforcer['actions']);

        return successResponse('', ['listener' => $enforcer]);
    }

    /**
     * Gets SLA data
     * @param Builder $baseQuery
     * @return JsonResponse
     */
    private function getSla(Builder &$baseQuery)
    {
        $this->appendRulesQuery($baseQuery);

        $this->appendRemindersQuery($baseQuery);

        $enforcer = $baseQuery->with(["slaMeta",
            // selecting priority_id as priority_id once and id once, so that relationship works and also there should
            // be no need to format it
            "slaMeta.priority:priority_id,priority as name,priority_id as id",
            "slaMeta.businessHour:id,name"])->first();

        if (!$enforcer) {
            return errorResponse(Lang::get('lang.not_found'));
        }

        $this->formatValue($enforcer['rules']);

        $enforcer->reminders = (object)[];
        $enforcer->reminders->approaching = $this->getFormattedReminders($enforcer->approachingReminders);
        $enforcer->reminders->violated = $this->getFormattedReminders($enforcer->violatedReminders);
        unset($enforcer->approachingReminders, $enforcer->violatedReminders);

        return successResponse('',['sla' => $enforcer]);
    }

    /**
     * Deletes workflow/listener related field (entire workflow/listener or
     * workflow-action/listener-action or workflow-rule/listener-rule)
     * @param $type
     * @param $enforcerId
     * @param null $enforcerMetaType
     * @return JsonResponse
     */
    public function deleteEnforcer($type, $enforcerId, $enforcerMetaType = null)
    {
        switch ($type) {
            case "workflow":
                $fieldToBeDeleted = TicketWorkflow::find($enforcerId);
                break;

            case "listener":
                $fieldToBeDeleted = TicketListener::find($enforcerId);
                break;

            case "sla":
                $fieldToBeDeleted = TicketSla::find($enforcerId);
                break;

            case "action":
                $fieldToBeDeleted = TicketAction::find($enforcerId);
                break;

            case "rule":
                $fieldToBeDeleted = TicketRule::find($enforcerId);
                break;

            case "event":
                $fieldToBeDeleted = TicketEvent::find($enforcerId);
                break;

            case "reminder":
                $fieldToBeDeleted = $enforcerMetaType == 'approaching' ? SlaApproachEscalate::find($enforcerId) : SlaViolatedEscalate::find($enforcerId);
                break;

            default:
                throw new InvalidArgumentException("Invalid type passed");
        }

        if (!$fieldToBeDeleted) {
            return errorResponse(Lang::get('lang.not_found'));
        }

        $fieldToBeDeleted->delete();

        return successResponse(Lang::get('lang.deleted_successfully'));
    }

    /**
     * gets listener/workflow by the type
     * @param Request $request request will have 5 params : type, search-query, limit, sort-order and sort-field
     * @return JsonResponse
     */
    public function getEnforcerList(Request $request)
    {
        $this->initializeParameterValues($request);

        if (!in_array($this->sortOrder, array('asc', 'ASC', 'desc', 'DESC'))) {
            return errorResponse("No proper sort order");
        }

        $modelName = $this->getParentModel($request->type);

        $baseQuery = $modelName::orderBy($this->sortField, $this->sortOrder)
            ->where('name', 'like', '%' . $this->searchQuery . '%');

        if ($request->meta) {

            // for SLA, default SLA should not be allowed to reorder
            if($request->type == "sla") {
                $baseQuery = $baseQuery->where("is_default", 0);
            }

            return successResponse('', $baseQuery->get());
        }

        return successResponse('', $baseQuery->paginate($this->limit));
    }

    /**
     * Populates class variables to handle addition params in the request . For eg. search-query, limit,
     * sort-field, sort-order so that it can be used throughout the class to give user relevant information
     * according to the paramters passed.
     * @param Request $request request will have 4 params : search-query, limit, sort-rder and sort-field
     */
    private function initializeParameterValues($request)
    {
        $this->searchQuery = $request->input('search') ? $request->input('search') : '';

        $this->limit = $request->input('limit') ? $request->input('limit') : 10;

        $this->sortField = $request->input('sort') ? $request->input('sort') : 'order';

        $this->sortOrder = $request->input('sort_order') ? $request->input('sort_order') : 'asc';
    }

    /**
     * gets listener/workflow by the type and reorders them
     * @param Request $request request will have 4 params : type, enforcers
     * @return JsonResponse
     */
    public function reorderEnforcer(Request $request)
    {
        $modelName = $this->getParentModel($request->type);

        $order = 0;

        if ($request->has('enforcers')) {
            $enforcers = $request->enforcers;
            foreach ($enforcers as $value) {
                $modelName::where('id', $value)->update(['order' => ++$order]);
            }
        }

        return successResponse(Lang::get('lang.successfully_reordered'));
    }
}
