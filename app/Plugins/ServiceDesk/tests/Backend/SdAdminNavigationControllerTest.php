<?php

  namespace App\Plugins\ServiceDesk\tests\Backend;

  use Tests\AddOnTestCase;
  use App\Plugins\ServiceDesk\Controllers\Navigation\SdAdminNavigationController;

  class SdAdminNavigationControllerTest extends AddOnTestCase
  {

      private $adminNavigation;

      public function setUp():void
      {
          parent::setUp();
          $this->adminNavigation = new SdAdminNavigationController;
      }

      /** @group injectSdAdminNavigation */
      public function test_injectSdAdminNavigation_forSuccess()
      {
        $navigationContainer = collect();

        $this->adminNavigation->injectSdAdminNavigation($navigationContainer);

        $this->assertEquals('Service Desk', $navigationContainer[0]->name);
        $navigations = $navigationContainer[0]->navigations;
        $this->assertCount(10, $navigations);
        $this->assertEquals('Products', $navigations[0]->name);
        $this->assertEquals('Procurement Modes', $navigations[1]->name);
        $this->assertEquals('Contract Types', $navigations[2]->name);
        $this->assertEquals('License Type', $navigations[3]->name);
        $this->assertEquals('Vendor', $navigations[4]->name);
        $this->assertEquals('Asset Types', $navigations[5]->name);
        $this->assertEquals('Asset Statuses', $navigations[6]->name);
        $this->assertEquals('CAB', $navigations[7]->name);
        $this->assertEquals('Announcement', $navigations[8]->name);
        $this->assertEquals('QR Code', $navigations[9]->name);
      }
  }
