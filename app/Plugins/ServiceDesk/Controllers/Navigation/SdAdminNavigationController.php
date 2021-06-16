<?php

namespace App\Plugins\ServiceDesk\Controllers\Navigation;

use App\Http\Controllers\Common\Navigation\AgentNavigation;
use App\Http\Controllers\Common\Navigation\Navigation;
use App\Http\Controllers\Common\Navigation\NavigationCategory;
use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;

/**
 * Handles Admin Navigation for service desk
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class SdAdminNavigationController
{
  use NavigationHelper;

  /**
   * Injects service desk specific navigation to core agent navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectSdAdminNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();

    $coreNavigationContainer->push(
      $this->createNavigationCategory(Lang::get('ServiceDesk::lang.service_desk'), $navigationArray)
    );
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk agent panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectNavigationIntoCollection($navigationArray, 'products', 'fa fa-industry','service-desk/products','service-desk/products');

    $this->injectNavigationIntoCollection($navigationArray, 'procurement_types', 'fa fa-phone','service-desk/procurement','service-desk/procurement');

    $this->injectNavigationIntoCollection($navigationArray, 'contract_types', 'fa fa-paper-plane','service-desk/contract-types','service-desk/contract-types');

    $this->injectNavigationIntoCollection($navigationArray, 'license_type', 'fa fa-paste','service-desk/license-types','service-desk/license-types');

    $this->injectNavigationIntoCollection($navigationArray, 'vendor', 'fa fa-barcode','service-desk/vendor','service-desk/vendor');

    $this->injectNavigationIntoCollection($navigationArray, 'asset_types', 'fa fa-briefcase','service-desk/assetstypes','service-desk/assetstypes');

    $this->injectNavigationIntoCollection($navigationArray, 'asset_statuses', 'fa fa-briefcase','service-desk/asset-statuses','service-desk/asset-statuses');

    $this->injectNavigationIntoCollection($navigationArray, 'cabs', 'fa fa-users','service-desk/cabs','service-desk/cabs');

    $this->injectNavigationIntoCollection($navigationArray, 'announcement', 'fa fa-volume-up','service-desk/announcement','service-desk/announcement');

    $this->injectNavigationIntoCollection($navigationArray, 'barcode', 'fa fa-qrcode','service-desk/barcode/settings','service-desk/barcode/settings');

    return $navigationArray;
  }

  private function injectNavigationIntoCollection(Collection &$navigationArray, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("ServiceDesk::lang.$name");
    $navigationArray->push(
      $this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString)
    );
  }
}
