<?php

namespace App\Http\Controllers\Common\Navigation;

use App\Http\Controllers\Common\Navigation\Navigation;
use App\Http\Controllers\Controller;
use Lang;
use Illuminate\Support\Collection;
use App\Traits\NavigationHelper;
use Event;

/**
 * Handles all Admin panel navigation
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class AdminNavigationController extends Controller
{
  public function __construct()
  {
    $this->middleware('role.admin');
  }

  use NavigationHelper;

  /**
   * Injects all ticket related navigations into parent ticket navigation
   * @return Response
   */
  public function getAdminNavigation()
  {
    $navigationArray = $this->getNavigationArray();

    $navigationContainer = collect();

    $navigationContainer->push($this->createNavigationCategory(Lang::get('lang.helpdesk'), $navigationArray));

    Event::dispatch('admin-panel-navigation-data-dispatch', [&$navigationContainer]);

    return successResponse('',$navigationContainer);
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk admin panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectHomeNavigation($navigationArray);

    $this->injectStaffNavigation($navigationArray);

    $this->injectEmailNavigation($navigationArray);

    $this->injectManageNavigation($navigationArray);

    $this->injectTicketsNavigation($navigationArray);

    $this->injectSettingsNavigation($navigationArray);

    $this->injectAddOnsNavigation($navigationArray);

    $this->injectDebugNavigation($navigationArray);

    Event::dispatch('admin-panel-navigation-array-data-dispatch', [&$navigationArray]);

    $this->injectReturnToAgentPanelNavigation($navigationArray);

    $this->injectSignoutNavigation($navigationArray);

    return $navigationArray;
  }

  /**
   * Injects Home navigation to collection passed to it
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectHomeNavigation(Collection $navigationArray)
  {
    $navigationArray->push(
      $this->getNavigationObject(Lang::get('lang.home'), 'fa fa-home', 'admin', 'admin')
    );
  }

  /**
   * Injects Return to Agent Panel navigation to collection passed to it
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectReturnToAgentPanelNavigation(Collection $navigationArray)
  {
    $navigationArray->push(
      $this->getNavigationObject(Lang::get('lang.return_to_agent_panel'), 'fa fa-level-down', 'dashboard', 'dashboard')
    );
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
   * Injects staff Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectStaffNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.staffs'));

    $navigationObject->setIconClass('fa fa-users');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject, 'agents', 'fa fa-user', 'agents', 'agents');

    $this->injectChildNavigation($navigationObject, 'departments', 'fa fa-sitemap', 'departments', 'departments');

    $this->injectChildNavigation($navigationObject, 'teams', 'fa fa-users', 'teams', 'teams');

    Event::dispatch('admin-panel-staff-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects staff Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectEmailNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.email'));

    $navigationObject->setIconClass('fa fa-envelope-o');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject ,'emails_settings', 'fa fa-envelope', 'emails', 'emails');

    $this->injectChildNavigation($navigationObject, 'templates', 'fa fa-file-text-o', 'template-sets', 'template');

    $this->injectChildNavigation($navigationObject, 'queues', 'fa fa-upload', 'queue', 'queue');

    $this->injectChildNavigation($navigationObject, 'diagnostics', 'fa fa-plus', 'getdiagno', 'getdiagno');

    Event::dispatch('admin-panel-email-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects staff Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectManageNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.manage'));

    $navigationObject->setIconClass('fa fa-cubes');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject ,'help_topics', 'fa fa-file-text-o', 'helptopic', 'helptopic');

    $this->injectChildNavigation($navigationObject, 'sla_plans', 'fa fa-clock-o', 'sla', 'sla');

    $this->injectChildNavigation($navigationObject, 'business_hours', 'fa fa-calendar', 'sla/business-hours/index', 'business-hours');

    $this->injectChildNavigation($navigationObject, 'form-builder', 'fa fa-file-text-o', 'forms/create', 'forms/create');

    $this->injectChildNavigation($navigationObject, 'form-groups', 'fa fa-object-group', 'form-groups', 'form-groups');

    $this->injectChildNavigation($navigationObject, 'workflow', 'fa fa-sitemap', 'workflow', 'workflow');

    $this->injectChildNavigation($navigationObject, 'approval_workflow', 'fa fa-sitemap', 'approval-workflow', 'approval-workflow');

    $this->injectChildNavigation($navigationObject, 'priority', 'fa fa-asterisk', 'ticket/priority', 'priority');

    $this->injectChildNavigation($navigationObject, 'ticket_type', 'fa fa-list-ol', 'ticket-types', 'ticket-types');

    $this->injectChildNavigation($navigationObject, 'listeners', 'fa fa-magic', 'listener', 'listener');

    $this->injectChildNavigation($navigationObject, 'widgets', 'fa fa-list-alt', 'widgets', 'widgets');

    Event::dispatch('admin-panel-manage-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Tickets Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectTicketsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.tickets'));

    $navigationObject->setIconClass('fa fa-ticket');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject ,'ticket_settings', 'fa fa-file-text', 'getticket', 'getticket');

    $this->injectChildNavigation($navigationObject, 'alert_notices', 'fa fa-bell', 'alert', 'alert');

    $this->injectChildNavigation($navigationObject, 'status', 'fa fa-plus-square-o', 'setting-status', 'status');

    $this->injectChildNavigation($navigationObject, 'labels', 'fa fa-lastfm', 'labels', 'labels');

    $this->injectChildNavigation($navigationObject, 'ratings', 'fa fa-star', 'getratings', 'ratings');

    $this->injectChildNavigation($navigationObject, 'close_ticket_workflow', 'fa fa-sitemap', 'close-workflow', 'close-workflow');

    $this->injectChildNavigation($navigationObject, 'tags', 'fa fa-tags', 'tag', 'tag');

    $this->injectChildNavigation($navigationObject, 'auto_assign', 'fa fa-check-square-o', 'auto-assign', 'auto-assign');

    $this->injectChildNavigation($navigationObject, 'location', 'fa fa-magic', 'helpdesk/location-types', 'location');

    $this->injectChildNavigation($navigationObject, 'source', 'fa fa-gg', 'source', 'source');

    $this->injectChildNavigation($navigationObject, 'recurring', 'fa fa-copy', 'recur/list', 'recur');

    Event::dispatch('admin-panel-tickets-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Settings Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectSettingsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.settings'));

    $navigationObject->setIconClass('fa fa-cog');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject ,'dashboard-statistics', 'fa fa-dashboard', 'dashboard-settings', 'dashboard-settings');

    $this->injectChildNavigation($navigationObject ,'company', 'fa fa-building', 'getcompany', 'company');

    $this->injectChildNavigation($navigationObject, 'system', 'fa fa-laptop', 'getsystem', 'getsystem');

    $this->injectChildNavigation($navigationObject, 'user-options', 'fa fa-user', 'user-options', 'user-options');

    $this->injectChildNavigation($navigationObject, 'social-login', 'fa fa-globe', 'social/media', 'social/media');

    $this->injectChildNavigation($navigationObject, 'language', 'fa fa-language', 'languages', 'languages');

    $this->injectChildNavigation($navigationObject, 'cron', 'fa fa-hourglass', 'job-scheduler', 'job-scheduler');

    $this->injectChildNavigation($navigationObject, 'security', 'fa fa-lock', 'security', 'security');

    $this->injectChildNavigation($navigationObject, 'notifications', 'fa fa-bell', 'settings-notification', 'settings-notification');

    $this->injectChildNavigation($navigationObject, 'storage', 'fa fa-save', 'storage', 'storage');

    $this->injectChildNavigation($navigationObject, 'system-backup', 'fa fa-hdd-o', 'system-backup', 'system-backup');

    $this->injectChildNavigation($navigationObject, 'social-icon-links', 'fa fa-external-link', 'widgets/social-icon', 'widgets/social-icon');

    $this->injectChildNavigation($navigationObject, 'api', 'fa fa-cogs', 'api', 'api');

    $this->injectChildNavigation($navigationObject, 'websockets', 'fa fa-bolt', 'websockets/settings', 'websockets/settings');

    $this->injectChildNavigation($navigationObject, 'webhook', 'fa fa-server', 'webhook', 'webhook');

    $this->injectChildNavigation($navigationObject, 'importer_user_import', 'fa fa-download', 'importer', 'importer.form');
    
    $this->injectChildNavigation($navigationObject, 'recaptcha', 'fa fa-refresh', 'recaptcha', 'recaptcha');

    Event::dispatch('admin-panel-settings-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }


  /**
   * Injects Add ons Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectAddOnsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.add_ons'));

    $navigationObject->setIconClass('fa fa-puzzle-piece');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject, 'plugins', 'fa fa-plug', 'plugins', 'plugins');

    $this->injectChildNavigation($navigationObject, 'modules', 'fa fa-link', 'modules', 'modules');

    Event::dispatch('admin-panel-addons-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Add ons Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectDebugNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.debug'));

    $navigationObject->setIconClass('fa fa-heartbeat');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject, 'debug-options', 'fa fa-bug', 'error-and-debugging-options', 'error-and-debugging-options');

    $this->injectChildNavigation($navigationObject, 'system-logs', 'fa fa-history', 'system-logs', 'system-logs');

    if(getActiveQueue() == 'redis'){
      $this->injectChildNavigation($navigationObject, 'queue-monitor', 'fa fa-desktop', 'horizon', 'horizon');
    }

    Event::dispatch('admin-panel-debug-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }
}
