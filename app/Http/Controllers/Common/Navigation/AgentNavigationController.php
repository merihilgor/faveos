<?php

namespace App\Http\Controllers\Common\Navigation;

use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use App\Http\Controllers\Controller;
use Lang;
use Illuminate\Support\Collection;
use Auth;
use Event;
use App\Traits\NavigationHelper;
use App\Policies\TicketPolicy;

/**
 * Handles Agent panel Navigation
 * @author Avinash Kumar <avinash.kumar@ladybirdweb.com>
 */
class AgentNavigationController extends Controller
{

    public function __construct()
    {
        $this->middleware('role.agent');
    }

    use NavigationHelper;

  /**
   * Injects all ticket related navigations into parent ticket navigation
   * @return Response
   */
  public function getAgentNavigation()
  {
    $navigationArray = $this->getNavigationArray();

    $navigationContainer = collect();

    $navigationContainer->push($this->createNavigationCategory(Lang::get('lang.helpdesk'), $navigationArray));

    Event::dispatch('agent-panel-navigation-data-dispatch', [&$navigationContainer]);

    return successResponse('', $navigationContainer);
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk agent panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectDashboardNavigation($navigationArray);

    $this->injectTicketNavigation($navigationArray);

    //below checking user and organization access permission based on side bar user tab will appear

    $accessPolicy = new TicketPolicy();
    if($accessPolicy->accessUserProfile() || $accessPolicy->accessOrganizationProfile()){

      $this->injectUsersNavigation($navigationArray);
    }

    $this->injectToolsNavigation($navigationArray);

    $this->injectReportsNavigation($navigationArray);

    $this->injectGoToAdminPanelNavigation($navigationArray);

    $this->injectOldDashboardNavigation($navigationArray);

    $this->injectSignoutNavigation($navigationArray);

    return $navigationArray;
  }

  /**
   * Injects Logout Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectGoToAdminPanelNavigation(Collection $navigationArray)
  {
    if(Auth::user()->role == 'admin'){
      $navigationArray->push(
        $this->getNavigationObject(Lang::get('lang.go_to_admin_panel'), 'fa fa-level-up', 'admin', '/admin')
      );
    }
  }

  /**
   * Injects Logout Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectSignoutNavigation(Collection $navigationArray)
  {
    $navigationArray->push(
       $this->getNavigationObject(Lang::get('lang.sign_out'), 'fa fa-sign-out', 'auth/logout', 'auth/logout')
    );
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectTicketNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.tickets'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-ticket');

    $navigationObject->setHasChildren(true);

    $ticketNavigation = new TicketNavigationController(new TicketListController);

    $ticketNavigation->injectTicketNavigation($navigationObject);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectDashboardNavigation(Collection &$navigationArray)
  {
    $navigationArray->push(
       $this->getNavigationObject(Lang::get('lang.dashboard'), 'fa fa-dashboard', 'dashboard', 'dashboard')
    );
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectUsersNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.users'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-users');

    $navigationObject->setHasChildren(true);


    $accessPolicy = new TicketPolicy();
    //if agent have user access permission then button will appar
    if($accessPolicy->accessUserProfile()){

    $this->injectChildNavigation($navigationObject, 'user_directory', 'fa fa-circle-o', 'user', 'user');

    }
    
    //if agent have organization access permission then button will appar

    if($accessPolicy->accessOrganizationProfile())
    {

    $this->injectChildNavigation($navigationObject, 'organization', 'fa fa-circle-o', 'organizations', 'organizations');

    }
    $navigationArray->push($navigationObject);
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectReportsNavigation(Collection &$navigationArray)
  {

    if((new TicketPolicy)->report()){

        $navigationObject = new Navigation;

        $navigationObject->setName(Lang::get('lang.reports'));

        $navigationObject->setHasCount(false);

        $navigationObject->setIconClass('fa fa-line-chart');

        $navigationObject->setHasChildren(true);

        $this->injectChildNavigation($navigationObject, 'helpdesk_reports', 'fa fa-circle-o', 'report/get', 'report/get');

        // this event can be used go inject more navigations by other plugins
        Event::dispatch('agent-panel-report-navigation-data-dispatch', [&$navigationObject]);

        $this->injectChildNavigation($navigationObject, 'activity_downloads', 'fa fa-download', 'report/activity-download', 'report/activity-download');

        if(Auth::user()->role == "admin"){
            $this->injectChildNavigation($navigationObject, 'settings', 'fa fa-cog', 'report/settings', 'report/settings');
        }

        $navigationArray->push($navigationObject);
    }
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectToolsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.tools'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('glyphicon glyphicon-wrench');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'canned_response', 'fa fa-circle-o', 'canned/list', 'canned');

    $this->injectKnowledgeBaseNavigation($navigationObject);

    $this->injectRecurNavigation($navigationObject);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects recur navigation into tools navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectRecurNavigation(&$navigationObject)
  {
    ((new TicketPolicy)->accessRecurTicket()) ? $this->injectChildNavigation($navigationObject, 'recur', 'fa fa-circle-o', 'agent/recur/list', 'recur') : '';
  }


  /**
   * Adds knowledgebase navigation which includes Category, Article, Pages, Comments, Settings
   * @param Collection $navigationArray
   * @return null
   */
  private function injectKnowledgeBaseNavigation(Navigation &$navigation)
  {
    if((new TicketPolicy)->kb()){

      $navigationObject = new Navigation;

      $navigationObject->setName(Lang::get('lang.knowledge_base'));

      $navigationObject->setHasCount(false);

      $navigationObject->setIconClass('glyphicon glyphicon-book');

      $navigationObject->setHasChildren(true);

      $navigationObject->injectChildNavigation($this->getCategoriesNavigation());

      $navigationObject->injectChildNavigation($this->getArticlesNavigation());

      $navigationObject->injectChildNavigation($this->getPagesNavigation());

      $this->injectChildNavigation($navigationObject, 'comments', 'fa fa-comments-o', 'comment', 'comment');

      $this->injectChildNavigation($navigationObject, 'settings', 'fa fa-wrench', 'kb/settings', 'kb/settings');

      $navigation->injectChildNavigation($navigationObject);
    }
  }

  /**
   * Gets Category Navigation
   * @return Navigation
   */
  private function getCategoriesNavigation()
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.categories'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-list-ul');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'addcategory', 'fa fa-circle-o', 'category/create', 'category/create');

    $this->injectChildNavigation($navigationObject, 'allcategory', 'fa fa-circle-o', 'category', 'category');

    return $navigationObject;
  }

  /**
   * Gets Article Navigation
   * @return Navigation
   */
  private function getArticlesNavigation()
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.articles'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-edit');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'addarticle', 'fa fa-circle-o', 'article/create', 'article/create');

    $this->injectChildNavigation($navigationObject, 'allarticle', 'fa fa-circle-o', 'article', 'article');

    $this->injectChildNavigation($navigationObject, 'addarticletemplate', 'fa fa-circle-o', 'article/create/template', 'article/create/template');

    $this->injectChildNavigation($navigationObject, 'allarticletemplate', 'fa fa-circle-o', 'article/alltemplate/list', 'article/alltemplate/list');

    return $navigationObject;
  }

  /**
   * Gets Pages Navigation
   * @return Navigation
   */
  private function getPagesNavigation()
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.pages'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fa fa-file-text');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'addpages', 'fa fa-circle-o', 'page/create', 'page/create');

    $this->injectChildNavigation($navigationObject, 'allpages', 'fa fa-circle-o', 'page', 'page');

    return $navigationObject;
  }

    /**
     * Injects old dashboard navigation into parent navigation
     * @param  Collection &$navigationArray
     * @return null
     */
    private function injectOldDashboardNavigation(Collection &$navigationArray)
    {
        $navigationObject = $this->getNavigationObject(Lang::get('lang.old_dashboard'), 'fa fa-dashboard', 'dashboard-old-layout', 'dashboard-old-layout');
        $navigationArray->push($navigationObject);
    }

}
