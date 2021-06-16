<?php

namespace App\Plugins\ServiceDesk\Controllers\Navigation;

use App\Http\Controllers\Common\Navigation\AgentNavigation;
use App\Http\Controllers\Common\Navigation\Navigation;
use App\Http\Controllers\Common\Navigation\NavigationCategory;
use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;

/**
 * Handles Agent Navigation for service desk
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class SdAgentNavigationController
{
  protected $agentPermission;
  use NavigationHelper;

  public function __construct(){
      $this->agentPermission = new AgentPermissionPolicy();
  }

  /**
   * Injects service desk specific navigation to core agent navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectSdAgentNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();

    $coreNavigationContainer->push($this->createNavigationCategory(Lang::get('ServiceDesk::lang.service_desk'), $navigationArray));
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk agent panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectProblemsNavigation($navigationArray);

    $this->injectChangesNavigation($navigationArray);

    $this->injectReleasesNavigation($navigationArray);

    $this->injectAssetsNavigation($navigationArray);

    $this->injectContractsNavigation($navigationArray);

    return $navigationArray;
  }

  /**
   * Injects report navigation for servicedesk
   * @param Navigation $navigationObject
   */
  public function injectSdReportNavigation(Navigation &$navigationObject)
  {
      $this->injectChildNavigation($navigationObject, 'servicedesk_reports', 'fa fa-circle-o', 'service-desk/reports', 'service-desk/reports');
  }

  /**
   * Injects Problem Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectProblemsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    if($this->agentPermission->problemsView()){
    $navigationObject->setName(Lang::get('ServiceDesk::lang.problems'));
    }

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-bug');

    $navigationObject->setHasChildren(true);
  
    if($this->agentPermission->problemCreate() && $this->agentPermission->problemsView()){
    $this->injectChildNavigation($navigationObject, 'new_problem', 'fa fa-circle-o', 'service-desk/problem/create', 'service-desk/problem/create');
    }
    
    if($this->agentPermission->problemsView()){
    $this->injectChildNavigation($navigationObject, 'all_problem', 'fa fa-circle-o', 'service-desk/problems', 'service-desk/problems');
    }

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Changes Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectChangesNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    if($this->agentPermission->changesView()){
    $navigationObject->setName(Lang::get('ServiceDesk::lang.changes'));
    }

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-refresh');

    $navigationObject->setHasChildren(true);

    if($this->agentPermission->changeCreate() && $this->agentPermission->changesView()){
    $this->injectChildNavigation($navigationObject, 'new_change', 'fa fa-circle-o', 'service-desk/changes/create', 'service-desk/changes/create');
    } 
    
    if($this->agentPermission->changesView()){
    $this->injectChildNavigation($navigationObject, 'all_changes', 'fa fa-circle-o', 'service-desk/changes', 'service-desk/changes');
    }

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Releases Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectReleasesNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;
    
    if($this->agentPermission->releasesView()){
    $navigationObject->setName(Lang::get('ServiceDesk::lang.releases'));
    }

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-newspaper-o');

    $navigationObject->setHasChildren(true);
    
    if($this->agentPermission->releaseCreate() && $this->agentPermission->releasesView()){
    $this->injectChildNavigation($navigationObject, 'new_release', 'fa fa-circle-o', 'service-desk/releases/create', 'service-desk/releases/create');
    }
    
    if($this->agentPermission->releasesView()){
    $this->injectChildNavigation($navigationObject, 'all_releases', 'fa fa-circle-o', 'service-desk/releases', 'service-desk/releases');
    }

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Assets Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectAssetsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;
    
    if($this->agentPermission->assetsView()){
    $navigationObject->setName(Lang::get('ServiceDesk::lang.assets'));
    }

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-server');

    $navigationObject->setHasChildren(true);
    
    if($this->agentPermission->assetCreate() && $this->agentPermission->assetsView()){
    $this->injectChildNavigation($navigationObject, 'new_assets', 'fa fa-circle-o', 'service-desk/assets/create', 'service-desk/assets/create');
    }
    
    if($this->agentPermission->assetsView()){
    $this->injectChildNavigation($navigationObject, 'all_assets', 'fa fa-circle-o', 'service-desk/assets', 'service-desk/assets');
    } 

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Contracts Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectContractsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;
    
    if($this->agentPermission->contractsView()){
    $navigationObject->setName(Lang::get('ServiceDesk::lang.contract'));
    }

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-paperclip');

    $navigationObject->setHasChildren(true);
    
    if($this->agentPermission->contractCreate() && $this->agentPermission->contractsView()){
    $this->injectChildNavigation($navigationObject, 'new_contract', 'fa fa-circle-o', 'service-desk/contracts/create', 'service-desk/contracts/create');
    }
    
    if($this->agentPermission->contractsView()){
    $this->injectChildNavigation($navigationObject, 'all_contracts', 'fa fa-circle-o', 'service-desk/contracts', 'service-desk/contracts');
    } 

    $navigationArray->push($navigationObject);
  }


  /**
   * Creates navigation object with no children
   * @param Navigation $parentNavigation parent into which child has to be injected
   * @param  string $name        name of the navigation
   * @param  string $iconClass
   * @param  string $redirectUrl the url to which it should redirect
   * @param  string $routeString the string by which it has to be identified as active route
   * @return null
   */
  public function injectChildNavigation(Navigation &$parentNavigation, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("ServiceDesk::lang.$name");
    $parentNavigation->injectChildNavigation($this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString));
  }
}
