<?php
namespace App\Http\Controllers\Common\Dependency;

use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent_panel\Canned;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Ticket\TicketSla;
use Auth;
use App\User;
use App\Traits\EnhancedDependency;
use App\Model\helpdesk\Filters\Tag;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Filters\Label;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\Model\helpdesk\Email\Emails as SystemEmail;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Exceptions\DependencyNotFoundException;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Form\FormCategory;
use DB;
use App\Model\helpdesk\Form\FormGroup;
use Event;
use Illuminate\Database\Eloquent\Builder;

/**
 * Contains dependencies which are only accessed by admin or agent.
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class NonPublicDependencies extends BaseDependencyController
{
    use EnhancedDependency;

    /**
     * Gets non-public dependency data according to the value of $type
     * @param string $type Dependency type (like help-topics, priorities etc)
     * @return array|boolean    Array of dependency data on success, false on failure
     * @throws DependencyNotFoundException
     */
    protected function handleNonPublicDependencies($type)
    {
        if ($this->userRole == 'user') {
            throw new DependencyNotFoundException("dependency $this->dependencyKey not found");
        }

        switch ($type) {
            //non-public dependencies
            case 'sla-plans':
                return $this->slaPlans();

            case 'agents':
                return $this->agents();

            case 'teams':
                return $this->teams();

            case 'agents-teams':
                return $this->agentsAndTeams();

            case 'labels':
                return $this->labels();

            case 'tags':
                return $this->tags();

            case 'approval-workflows':
                return $this->approvalWorkflows();

            case 'organisation-departments':
                return $this->organisationDepartments();

            case 'tickets':
                return $this->tickets();

            case 'users':
                return $this->users();

            case 'business-hours':
                return $this->businessHours();

            case 'agent-types':
                return $this->agentTypes();

            case 'form-categories':
                return $this->formCategories();

            case 'canned-responses':
                return $this->cannedResponses();

            case 'form-groups':
                return $this->formGroups();

            default:
                throw new DependencyNotFoundException("dependency $this->dependencyKey not found");
        }
    }

    /**
     * Gets list of available sla-plans
     * @return array    List of available sla plans
     */
    protected function slaPlans()
    {
        $baseQuery = $this->baseQuery(new TicketSla)->where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->where('status', 1)->select('id', 'name');
        }

        return $this->get('sla_plans', $baseQuery);
    }

    /**
     * Gets list of users with necessary fields.
     * @return array    list of users
     */
    protected function users()
    {
        $roles = ['agent', 'admin', 'user'];

        $baseQuery = $this->getBaseQueryForUserByRole($roles);

        return $this->get('users', $baseQuery, function($element){
            $element->name = $element->meta_name;
            unset($element->meta_name);
            return $element;
        });
    }

    /**
     * Gets list of agents with necessary fields.
     * @return array    list of agents/admin
     */
    protected function agents()
    {
        $roles = ['agent', 'admin'];

        $baseQuery = $this->getBaseQueryForUserByRole($roles);

        return $this->get('agents', $baseQuery, function($element){
            $element->name = $element->meta_name;
            unset($element->meta_name);
            return $element;
        });
    }

    /**
     * Gets list of teams with necessary fields.
     * @return array  list of teams
     */
    protected function teams()
    {
        $baseQuery = $this->baseQuery(new Teams)->where('name', 'LIKE', "%$this->searchQuery%");

        if ($this->meta) {
            $baseQuery = $this->baseQuery(new Teams)->has('agents')->where('name', 'LIKE', "%$this->searchQuery%");
        }

        if (!$this->config) {
            $baseQuery = $baseQuery->where('status', 1)->select('id', 'name');
        }

        return $this->get('teams', $baseQuery);
    }

    /**
     * Gets list of people and teams to whom a ticket can be assigned with necessary fields.
     * @return array list of agents/admin and teams
     */
    protected function agentsAndTeams()
    {
        $agents = $this->agents();
        $teams = $this->teams();

        return array_merge($agents, $teams);
    }

    /**
     * Gets list of labels
     * @return array  list of labels
     */
    protected function labels()
    {
        $user_role = Auth::user()->role;
        $isManager = DepartmentAssignManager::where('manager_id', Auth::user()->id)->count();
        $baseQuery = $this->baseQuery(new Label)->where('title', 'LIKE', '%' . $this->searchQuery . '%')
        ->where('status', 1)
        ->orderBy('order', 'asc')
        ->select('id', 'title AS name', 'color')
        ->where(function ($q) use ($user_role, $isManager) {
            $q->whereRaw("find_in_set('" .$user_role. "', visible_to)")
            ->orWhereRaw("find_in_set('all', visible_to)")
            ->orWhere('visible_to', null);
            if ($isManager > 0) {
                $q->orWhereRaw("find_in_set('dept-mngr', visible_to)");
            }
        });
        return $this->get('labels', $baseQuery);
    }

    /**
     * Gets list of tags
     * @return array list of tags
     */
    protected function tags()
    {
        $baseQuery = $this->baseQuery(new Tag)->where('name', 'LIKE', '%' . $this->searchQuery . '%')
            ->select('id', 'name');

        return $this->get('tags', $baseQuery);
    }

    /**
     * Gets list of users
     * @param array $roles      Array of roles according to which users has to be fetched.
     *                          for eg. for fetching all users $roles will be ['user','agent','admin']
     * @return array            List of users
     */
    protected function getBaseQueryForUserByRole($roles)
    {
        // inactive agents should not come but inactive users should
        // QUERY LOGIC :all active agents or admins OR all users including inactive users
        // check if role has user in it, remove that from
        // same query as before OR users who are /inactive

        $rolesWithInactiveAllowed = array_diff($roles, ['admin', 'agent']);
        $rolesWithInactiveNotAllowed = array_diff($roles, ['user']);

        $this->sortField = 'first_name';

        $baseQuery = $this->baseQuery(new User)->where(function ($q) use ($rolesWithInactiveAllowed, $rolesWithInactiveNotAllowed) {
                $q->whereIn('role', $rolesWithInactiveAllowed)
                ->orWhere(function ($q2) use ($rolesWithInactiveNotAllowed) {
                    $q2->whereIn('role', $rolesWithInactiveNotAllowed)->where('active', 1);
                });
            })->where('is_delete', '!=', 1)->whereIn('role', $roles)
            ->where(function ($q){
                $q->where('first_name', 'LIKE', "%$this->searchQuery%")
                ->orWhere('last_name', 'LIKE', "%$this->searchQuery%")
                ->orWhere('email', 'LIKE', "%$this->searchQuery%")
                ->orWhere('user_name', 'LIKE', "%$this->searchQuery%");
            })->select('id', 'first_name', 'last_name', 'user_name', 'email', "profile_pic", 'updated_at','role');

        if ($this->supplements) {
            $this->appendSystemEmailQuery($baseQuery);
        }

        return $baseQuery;
    }

    /**
     * Appends system email to normal result
     * @param Builder $baseQuery
     * @return null
     */
    private function appendSystemEmailQuery(&$baseQuery)
    {
        $emailQuery = $this->baseQuery(new SystemEmail)->where('email_address', 'LIKE', "%$this->searchQuery%")
            ->select('id', DB::raw("email_name as first_name"), DB::raw("'' as last_name"), DB::raw("'' as user_name"),
                'email_address as email', DB::raw('"" as profile_pic'), 'updated_at', DB::raw('"system_mail" as role'));

        $baseQuery->union($emailQuery);
    }

    /**
     * Gets list of approval workflow
     * @return mixed
     */
    protected function approvalWorkflows()
    {
        $baseQuery = $this->baseQuery(new ApprovalWorkflow)->where([['name', 'LIKE', "%$this->searchQuery%"], ['type', 'approval_workflow']]);

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('approval_workflows', $baseQuery);
    }

    /**
     * Gives the list of avaible organisation departments
     * @return array
     */
    protected function organisationDepartments()
    {
        $baseQuery = $this->baseQuery(new OrganizationDepartment)
            ->leftJoin('organization', function ($q) {
                $q->on('organization.id', '=', 'organization_dept.org_id');
            })
            ->where('organization_dept.org_deptname', 'LIKE', '%' . $this->searchQuery . '%')
            ->select('organization_dept.id',DB::raw("CONCAT(organization_dept.org_deptname, '(', organization.name, ')') as name"));

        return $this->get('organisation_departments', $baseQuery);
    }

    /**
     * gets ticket numbers based on search string. Search string can be ticket subject or ticket number
     * @return mixed
     */
    private function tickets()
    {
        $this->sortField = "ticket_number";

        // check where status is not trash
        $tickets = $this->baseQuery(new Tickets)
                // 4 stands for trash
                // not querying for purpose of status to save queries, since
                ->where('status','!=', 4)
                ->join("ticket_thread","tickets.id", "=","ticket_thread.ticket_id")
                ->where('ticket_thread.title', '!=', null)
                ->where('ticket_thread.title', '!=', '')
                ->where(function($q){
                    $q->where('tickets.ticket_number','LIKE', "%$this->searchQuery%")
                        ->orWhere('ticket_thread.title','LIKE', "%$this->searchQuery%");
                })->select('tickets.id', DB::raw("CONCAT(title,'(#',ticket_number,')') as name"))->groupBy('tickets.id');
        return $this->get('tickets',$tickets);
    }

    /**
     * Gets list of system business hours
     */
    private function businessHours()
    {
        $baseQuery = $this->baseQuery(new BusinessHours)
            ->select('id','name')
            ->where('name', "LIKE", "%$this->searchQuery%")
            ->where('status', 1);

        return $this->get('business_hours', $baseQuery);
    }

    /**
     * Gets list of agent types present in the system
     */
    protected function agentTypes()
    {
        $this->sortField = "name";
        $this->sortOrder = "asc";

        $keys = ['department_manager', 'team_lead', 'assignee', 'admin'];

        $baseQuery = $this->baseQuery(new UserType)->where('name', 'LIKE', "%$this->searchQuery%")
            ->whereIn('key', $keys)->select('id', 'name');

        return $this->get('agent_types', $baseQuery);
    }

    /**
     * Gets list of form categories
     */
    protected function formCategories()
    {
        $this->sortField = "id";
        $this->sortOrder = "asc";

        $baseQuery = $this->baseQuery(new FormCategory)->where([['name', 'LIKE', "%$this->searchQuery%"], ['type', 'helpdesk']]);

        Event::dispatch('form-category-query',[$baseQuery]);

        return $this->get('form_categories', $baseQuery);
    }

    /**
     * gets canned response allowed for logged in user
     */
    public function cannedResponses()
    {
        $deptIds = DepartmentAssignAgents::select('department_id')->where('agent_id', Auth::user()->id)->pluck('department_id')->toArray();

        $baseQuery = Canned::where(function($q){
          $q->where('title', "LIKE", "%$this->searchQuery%")
              ->orWhere('message', "LIKE", "%$this->searchQuery%");
        })->where(function($q1) use ($deptIds){
            $q1->whereHas('departments', function ($q) use ($deptIds) {
                $q->whereIn('department.id', $deptIds);
            })->orWhere('user_id', Auth::user()->id);
        })->select('id', 'title as name');

        return $this->get('canned_responses', $baseQuery);
    }

    /**
     * Gets list of form groups
     * @return array $formGroups
     */
    protected function formGroups()
    {
        $formGroupQuery = $this->baseQuery(new FormGroup)->where([['name', 'LIKE', "%$this->searchQuery%"], ['active', 1]]);
        
        ($this->supplements == 'ticket' || empty($this->supplements)) ? $formGroupQuery = $formGroupQuery->where('group_type', 'ticket') : Event::dispatch('form-group-query',[$formGroupQuery, $this->supplements]);


        $formGroupQuery = $formGroupQuery->select('id', 'name');

        return $this->get('form_groups', $formGroupQuery);

    }
}
