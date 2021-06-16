<?php

namespace App\Plugins\ServiceDesk\Controllers\Common\Dependency;

use App\Http\Controllers\Common\Dependency\BaseDependencyController;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes as Impact;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Products\SdProductstatus;
use App\Plugins\ServiceDesk\Model\Products\SdProductprocmode;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasestatus;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasepriorities;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasetypes;
use App\Plugins\ServiceDesk\Model\Changes\SdChangetypes;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\Plugins\ServiceDesk\Model\Changes\SdChangepriorities;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Model\helpdesk\Workflow\ApprovalWorkflow as Cab;
use App\Model\helpdesk\Manage\UserType;
use App\Plugins\ServiceDesk\Model\Common\Ticket;
use App\Model\helpdesk\Ticket\Ticket_Status as TicketStatus;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Contract\ContractType;
use App\Plugins\ServiceDesk\Model\Contract\License;
use DB;
use App\Model\helpdesk\Form\FormField;
use App\Plugins\ServiceDesk\Model\Assets\SdAssetStatus;

class SdDependencyController extends BaseDependencyController {

    /**
     * Gets list of elements according to the passed Type.
     * @param string $type      dependency type (like impacts)
     * @param object $request
     * @return array            Success response or error response
     */
    public function handle($type, Request $request)
    {
        //populating parameter variables to handle addition params in the request . For eg. search-query, limit, meta, config
        $this->initializeParameterValues($request);

        /*
         * Once class variables like config, meta, limit, search-query, userRole is populated, it can be used throughout the class
         * to give user relavent information according to the paramters passed and userType
         */
        $data = $this->handleDependencies($type);

        if (!$data) {
            return errorResponse(trans('lang.fails'));
        }

        return successResponse('', $data);
    }

    /**
     * Populates class variables to handle addition params in the request . For eg. search-query, limit, meta, config, so that
     * it can be used throughout the class to give user relavent information according to the paramters passed and userType
     * @param object $request
     * @return
     */
    public function initializeParameterValues($request)
    {
        parent::initializeParameterValues($request);

        $this->ids = $request->ids ?: [];

        $this->ticketId = $request->ticket_id;

        $this->changeId = $request->change_id;

        $this->productId = $request->product_id;

        if($this->meta){
            // just giving big enough number
            $this->limit = 100000;
        }

    }

    /**
     * Gets dependency data according to the value of $type
     * @param string $type      Dependency type (like help-topics, priorities etc)
     * @return array|boolean    Array of dependency data on success, false on failure
     */
    public function handleDependencies($type)
    {
        switch ($type) {

            case 'impacts':
                return $this->impacts();

            case 'asset_types':
                return $this->assetTypes();

            case 'products':
                return $this->products();

            case 'assets':
                return $this->assets();

            case 'release_types':
                return $this->releaseTypes();

            case 'release_statuses':
                return $this->releaseStatuses();

            case 'release_priorities':
                return $this->releasePriorities();

            case 'change_types':
                return $this->changeTypes();

            case 'change_statuses':
                return $this->changeStatuses();

            case 'change_priorities':
                return $this->changePriorities();

             case 'changes':
                return $this->changes();

            case 'releases':
                return $this->releases();

            case 'cabs':
                return $this->cabs();

            case 'user_types':
                return $this->userTypes();

            case 'changes_based_on_ticket':
                return $this->changesBasedOnTicket();

            case 'tickets_based_on_change':
                return $this->ticketsBasedOnChange();

            case 'tickets_based_on_problem':
                return $this->ticketsBasedOnProblem();

            case 'product_statuses':
                return $this->productStatuses();

            case 'product_proc_mode':
                return $this->productProcurementModes();

            case 'vendors':
                return $this->vendors();

            case 'vendors_based_on_product':
                return $this->vendorsBasedOnProduct();

            case 'problems':
                return $this->problems();

             case 'contracts':
                return $this->contracts();

            case 'contract_types':
                return $this->contractTypes();

            case 'contract_statuses':
                return $this->contractStatuses();

            case 'contract_renewal_statuses':
                return $this->contractRenewalStatuses();

            case 'license_types':
                return $this->licenseTypes();

            case 'tickets':
                return $this->tickets();

            case 'asset_statuses':
                return $this->assetStatuses();

            default:
                return false;
        }
    }

    /**
     * Gets list of impacts with id and name
     * @return array    list of impacts
    */
    protected function impacts()
    {
        $baseQuery = Impact::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('impacts', $baseQuery);
    }

    /**
     * Gets list of asset types with id and name
     * @return array    list of asset types
    */
    protected function assetTypes()
    {
        $baseQuery = SdAssettypes::where('name', 'LIKE', "%$this->searchQuery%");
        // appending ids parameter to pick specific asset types, useful in formbuilder functionality
        if (!empty($this->ids)) {
            $baseQuery->whereIn('id', $this->ids);
        }

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        if($this->meta){
            (new FormField)->getFormQueryByParentQuery($baseQuery, null, $this->config);
        }

        return $this->get('asset_types', $baseQuery, function($element){
            $this->meta && FormField::formatFormElements($element, $this->config);
            $element->makeHidden('is_default');
            
            return $element;
        });
    }

    /**
     * Gets list of products with id and name
     * @return array    list of products
    */
    protected function products()
    {
        $baseQuery = SdProducts::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('products', $baseQuery);
    }

    /**
     * Gets list of assets with id and name
     * @return array    list of assets
     */
    protected function assets()
    {
        $baseQuery = SdAssets::whereRaw("concat(identifier, ' ', name) LIKE ?", "%$this->searchQuery%");
        $this->excludeAttachedItemsQuery($baseQuery);

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name', 'identifier');
        }

        return $this->get('assets', $baseQuery, function($asset){
            $asset->name = implode(' ', [$asset->identifier, $asset->name]);
            unset($asset->identifier);
            return $asset;
        });
    }

    /**
     * Gets list of releaseTypes with id and name
     * @return array    list of releaseTypes
     */
    protected function releaseTypes()
    {
        $baseQuery = SdReleasetypes::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('release_types', $baseQuery);
    }

    /**
     * Gets list of releaseStatuses with id and name
     * @return array    list of releaseStatuses
     */
    protected function releaseStatuses()
    {
        $baseQuery = SdReleasestatus::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('release_statuses', $baseQuery);
    }

    /**
     * Gets list of releasePriorities with id and name
     * @return array    list of releasePriorities
     */
    protected function releasePriorities()
    {
        $baseQuery = SdReleasepriorities::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('release_priorities', $baseQuery);
    }

    /**
     * Gets list of changeTypes with id and name
     * @return array    list of changeTypes
     */
    protected function changeTypes()
    {
        $baseQuery = SdChangetypes::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('change_types', $baseQuery);
    }

     /**
     * Gets list of changeStatuses with id and name
     * @return array    list of changeStatuses
     */
    protected function changeStatuses()
    {
        $baseQuery = SdChangestatus::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('change_statuses', $baseQuery);
    }

    /**
     * Gets list of changePriorities with id and name
     * @return array    list of changePriorities
     */
    protected function changePriorities()
    {
        $baseQuery = SdChangepriorities::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('change_priorities', $baseQuery);
    }

    /**
     * Gets list of releases with id and name
     * @return array    list of releases
     */
    protected function releases()
    {
        $baseQuery = SdReleases::WhereRaw("concat('identifier',' ',subject) LIKE ?", ['%'.$this->searchQuery.'%'])
            ->select('id', 'subject as name', 'identifier');

        $this->excludeAttachedItemsQuery($baseQuery);

        return $this->get('releases', $baseQuery, function($release){
            $release->name = implode(' ', [$release->identifier, $release->name]);
            unset($release->identifier);
            return $release;
        });
    }

    /**
     * Gets list of changes with id and name
     * @return array    list of changes
     */
    protected function changes()
    {
        $baseQuery = SdChanges::WhereRaw("concat(identifier,' ',subject) LIKE ?", ['%'.$this->searchQuery.'%'])
            ->select('id', 'subject as name','identifier');
        $this->excludeAttachedItemsQuery($baseQuery);

        return $this->get('changes', $baseQuery, function ($change) {
            $change->name = implode(' ', [$change->identifier, $change->name]);
            unset($change->identifier);
            return $change;
        });
    }

    /** 
     * method to exclude changes which are linked to existing ticket
     * only two separate changes can be linked to ticket
     * @return array list of changes
     */
    protected function changesBasedOnTicket()
    {
        $ticketId = $this->ticketId;

        $baseQuery = SdChanges::WhereRaw("concat('#CHN-',id,' ',subject) LIKE ?", ['%'.$this->searchQuery.'%'])
                ->whereDoesntHave('attachTickets',function($subQuery) use($ticketId) {
                    $subQuery->where('ticket_id', $ticketId);
                })->select('id', 'subject as name','identifier');

        return $this->get('changes', $baseQuery, function ($change) {
            $change->name = implode(' ', [$change->identifier, $change->name]);
            unset($change->identifier);
            return $change;
        });
    }

    /**
     * Gets list of cabs with id and name
     * @return array    list of cabs
     */
    protected function cabs()
    {
        $baseQuery = Cab::where([['name', 'LIKE', "%$this->searchQuery%"], ['type', 'cab']])
            ->select('id', 'name');

        return $this->get('cabs', $baseQuery);
    }

     /**
     * Gets list of user types with id and name for cab
     * @return array    list of user types
     */
    protected function userTypes()
    {
        $this->sortField = 'name';
        $this->sortOrder ='asc';

        $baseQuery = UserType::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->meta) {
            $keys = ['department_manager', 'team_lead'];
            $baseQuery = $baseQuery->whereIn('key', $keys)->select('id', 'name');
        }

        return $this->get('user_types', $baseQuery);
    }

    /** 
     * method to exclude tickets which are linked to existing change
     * @return array list of tickets
     */
    protected function ticketsBasedOnChange()
    {
        $changeId = $this->changeId;
        $searchString = $this->searchQuery;
        $openStatusIds = TicketStatus::where('purpose_of_status', 1)->pluck('id')->toArray();
        $baseQuery = Ticket::with('firstThread')
                        ->where(function($query) use($searchString) {
                            $query
                                ->where('ticket_number', 'LIKE', "%$searchString%")
                                ->orWhereHas('firstThread', function ($subQuery) use ($searchString) {
                                    $subQuery->where('title', 'LIKE', "%$searchString%");
                                });
                            })->whereDoesntHave('changes',function($query) use($changeId) {
                                $query->where('change_id', $changeId);
                            })->whereIn('status', $openStatusIds)->select('id', 'ticket_number as name');

        return $this->get('tickets', $baseQuery, function ($element) {
            return (object)['id'=>$element->id,
                'name'=> "($element->name)". (isset($element->firstThread) ? $element->firstThread->title : "")
            ];
        });
    }

    /** 
     * method to exclude tickets which are linked to existing problem
     * @return array list of tickets
     */
    protected function ticketsBasedOnProblem()
    {
        if(!($problemId = $this->supplements))
        $problemId=NULL;
        $searchString = $this->searchQuery;
        $openStatusIds = TicketStatus::where('purpose_of_status', 1)->pluck('id')->toArray();
        $baseQuery = Ticket::with(['firstThread'])
                        ->where(function($query) use($searchString) {
                            $query
                                ->where('ticket_number', 'LIKE', "%$searchString%")
                                ->orWhereHas('firstThread', function ($subQuery) use ($searchString) {
                                    $subQuery->where('title', 'LIKE', "%$searchString%");
                                });
                            })->whereDoesntHave('problems',function($query) use($problemId) {
                                $query->where('type_id', $problemId);
                            })->whereIn('status', $openStatusIds)
                        ->select('id', 'ticket_number as name');

        if (!$this->meta) {
            $baseQuery = $baseQuery->take($this->limit);
        }

        return $this->get('tickets', $baseQuery, function ($element) {
            return (object)['id'=>$element->id,
                'name'=> "($element->name)". (isset($element->firstThread) ? $element->firstThread->title : "")
            ];
        });
    }

    /**
     * Gets list of products with id and name
     * @return array    list of products
    */
    protected function vendors()
    {
        $baseQuery = SdVendors::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('vendors', $baseQuery);
    }

    /** 
     * method to exclude vendors which are linked to existing product
     * @return array list of vendors
     */
    protected function vendorsBasedOnProduct()
    {   
         $productId = $this->productId;
         $searchString = $this->searchQuery;
         $baseQuery = SdVendors::where(function($query) use($searchString) {
                            $query
                                ->where('name', 'LIKE', "%$searchString%")
                                ->orWhere('email', 'LIKE', "%$searchString%")
                                ->orWhere('primary_contact', 'LIKE', "%$searchString%");
                            })
                                ->whereDoesntHave('attachProducts',function($subQuery) use($productId) {
                                $subQuery->where('product_id', $productId);
                                })->select('id', 'name');

        return $this->get('vendors', $baseQuery);
    }

    /**
     * Gets list of productStatuses with id and name
     * @return array    list of productStatuses
     */
    protected function productStatuses()
    {
        $baseQuery = SdProductstatus::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('product_statuses', $baseQuery);
    }

    /**
     * Gets list of productProcModes with id and name
     * @return array    list of productProcModes
     */
    protected function productProcurementModes()
    {
        $baseQuery = SdProductprocmode::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('product_proc_mode', $baseQuery);
    }

    /**
     * method to update form category query to include service desk form categories
     * @param FormCategory $formCategoryQuery
     * @return null
     */
    public function updateFormCategoryQuery(&$formCategoryQuery)
    {
        $formCategoryQuery = $formCategoryQuery->orWhere('type', 'servicedesk');
    }

     /**
     * Gets list of problems with id and name
     * @return array    list of problems
     */
    protected function problems()
    {
        $baseQuery = SdProblem::WhereRaw("concat(identifier,' ',subject) LIKE ?", ['%'.$this->searchQuery.'%'])
            ->select('id', 'subject as name','identifier');

        $this->excludeAttachedItemsQuery($baseQuery);

        return $this->get('problems', $baseQuery, function ($problem) {
            $problem->name = implode(' ', [$problem->identifier, $problem->name]);
            unset($problem->identifier);
            return $problem;
        });
    }
    
    /**
     * Gets list of contracts with id and name
     * @return array    list of contracts
     */
    protected function contracts()
    {
        $baseQuery = SdContract::whereRaw("concat(identifier, ' ', name) LIKE ?", "%$this->searchQuery%");
        $this->excludeAttachedItemsQuery($baseQuery);

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name', 'identifier');
        }

        return $this->get('contracts', $baseQuery, function ($contract){
            $contract->name = "{$contract->identifier} {$contract->name}";
            unset($contract->identifier);
            return $contract;
        });
    }

    /**
     * Gets list of contract types with id and name
     * @return array    list of contract types
     */
    protected function contractTypes()
    {
        $baseQuery = ContractType::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('contract_types', $baseQuery);
    }

    /**
     * Gets list of contract statuses with id and name
     * @return array    list of contract statuses
     */
    protected function contractStatuses()
    {
        $baseQuery = SdContractStatus::where([['name', 'LIKE', "%$this->searchQuery%"], ['type', 'status']]);

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('contract_statuses', $baseQuery);
    }

    /**
     * Gets list of contract renewal statuses with id and name
     * @return array    list of contract renewal statuses
     */
    protected function contractRenewalStatuses()
    {
        $baseQuery = SdContractStatus::where([['name', 'LIKE', "%$this->searchQuery%"], ['type', 'renewal_status']]);

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('contract_renewal_statuses', $baseQuery);
    }

    /**
     * Gets list of license types with id and name
     * @return array    list of license types
     */
    protected function licenseTypes()
    {
        $baseQuery = License::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('license_types', $baseQuery);
    }

    /**
     * method to update form group query to include service desk form groups
     * @param FormGroup $formGroupQuery
     * @param String $supplements
     * @return null
     */
    public function updateFormGroupQuery(&$formGroupQuery, $supplements)
    {
        if ($supplements == 'asset') {
            $formGroupQuery = $formGroupQuery->where('group_type', 'asset');
        }
    }

    /**
     * method to exclude attached items for asset, ticket, problem, change, release and contract
     * while detaching
     * @param $baseQuery
     * @return null
     */
    private function excludeAttachedItemsQuery($baseQuery)
    {
        if (array_key_exists('relation_name', $this->supplements) && array_key_exists('attribute_name', $this->supplements) && array_key_exists('attribute_value', $this->supplements) && $baseQuery->count()) {
            $relationName = $this->supplements['relation_name'];
            $attributeName = $this->supplements['attribute_name'];
            $attributeValue = $this->supplements['attribute_value'];  
            $relationCheckQuery = clone $baseQuery;

            if ($relationCheckQuery->first()->$relationName) {
                $baseQuery->whereDoesntHave($relationName,function($subQuery) use($attributeName, $attributeValue) {
                    $subQuery->where($attributeName, $attributeValue);
                });
            }
        }
    }

    /**
     * Gets list of tickets with id and name
     * @param array list of tickets
     */
    private function tickets()
    {
        $searchString = $this->searchQuery;
        $openStatusIds = TicketStatus::where('purpose_of_status', 1)->pluck('id')->toArray();
        $baseQuery = Ticket::with('firstThread:id,title,ticket_id')
                        ->where(function($query) use($searchString) {
                            $query
                                ->where('ticket_number', 'LIKE', "%$searchString%")
                                ->orWhereHas('firstThread', function ($subQuery) use ($searchString) {
                                    $subQuery->where('title', 'LIKE', "%$searchString%");
                                });
                            })->whereIn('status', $openStatusIds);
        $this->excludeAttachedItemsQuery($baseQuery);
        $baseQuery->select('id', 'ticket_number as name');

        return $this->get('tickets', $baseQuery, function ($element) {
            return (object)['id'=>$element->id,
                'name'=> "($element->name)". (isset($element->firstThread) ? $element->firstThread->title : "")
            ];
        });
    }

    /**
     * Gets list of asset statuses with id and name
     * @return array    list of asset statuses
    */
    protected function assetStatuses()
    {
        $baseQuery = SdAssetStatus::where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('asset_statuses', $baseQuery);
    }

}
