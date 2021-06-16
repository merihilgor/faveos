<?php

  namespace App\Plugins\ServiceDesk\tests\Backend;

  use Tests\AddOnTestCase;
  use App\Plugins\ServiceDesk\Controllers\Navigation\SdAgentNavigationController;
  use Auth;

  class SdAgentNavigationControllerTest extends AddOnTestCase
  {
    private $agentNavigation;

    public function setUp():void
    {
        parent::setUp();
        $this->agentNavigation = new SdAgentNavigationController;
    }

    /** @group injectSdAgentNavigation */
    public function test_injectSdAgentNavigation_forSuccess()
    {
      $this->getLoggedInUserForWeb('admin');
      $navigationContainer = collect();

      $this->agentNavigation->injectSdAgentNavigation($navigationContainer);
      $this->assertEquals('Service Desk', $navigationContainer[0]->name);
      $navigations = $navigationContainer[0]->navigations;
      $this->assertCount(5, $navigations);
      $this->assertEquals('Problems', $navigations[0]->name);
      $this->assertEquals('Changes', $navigations[1]->name);
      $this->assertEquals('Releases', $navigations[2]->name);
      $this->assertEquals('Assets', $navigations[3]->name);
      $this->assertEquals('Contracts', $navigations[4]->name);
    }

    /** @group injectProblemsNavigation */
    public function test_injectProblemsNavigation_forSuccess()
    {
      $this->getLoggedInUserForWeb('admin');
      $navigationArray = collect();

      $this->getPrivateMethod($this->agentNavigation, 'injectProblemsNavigation', [&$navigationArray]);

      $this->assertCount(2, $navigationArray[0]->children);

      $this->assertEquals('New Problem', $navigationArray[0]->children[0]->name);

      $this->assertEquals('All Problems', $navigationArray[0]->children[1]->name);
    }

    /** @group injectChangesNavigation */
    public function test_injectChangesNavigation_forSuccess()
    { 
      $this->getLoggedInUserForWeb('admin');
      $navigationArray = collect();

      $this->getPrivateMethod($this->agentNavigation, 'injectChangesNavigation', [&$navigationArray]);

      $this->assertCount(2, $navigationArray[0]->children);

      $this->assertEquals('New Change', $navigationArray[0]->children[0]->name);

      $this->assertEquals('All Changes', $navigationArray[0]->children[1]->name);
    }


    /** @group injectReleasesNavigation */
    public function test_injectReleasesNavigation_forSuccess()
    {
      $this->getLoggedInUserForWeb('admin');
      $navigationArray = collect();

      $this->getPrivateMethod($this->agentNavigation, 'injectReleasesNavigation', [&$navigationArray]);

      $this->assertCount(2, $navigationArray[0]->children);

      $this->assertEquals('New Release', $navigationArray[0]->children[0]->name);

      $this->assertEquals('All Releases', $navigationArray[0]->children[1]->name);
    }

    /** @group injectAssetsNavigation */
    public function test_injectAssetsNavigation_forSuccess()
    {
      $this->getLoggedInUserForWeb('admin');
      $navigationArray = collect();

      $this->getPrivateMethod($this->agentNavigation, 'injectAssetsNavigation', [&$navigationArray]);

      $this->assertCount(2, $navigationArray[0]->children);

      $this->assertEquals('New Asset', $navigationArray[0]->children[0]->name);

      $this->assertEquals('All Assets', $navigationArray[0]->children[1]->name);
    }

    /** @group injectContractsNavigation */
    public function test_injectContractsNavigation_forSuccess()
    {
      $this->getLoggedInUserForWeb('admin');
      $navigationArray = collect();

      $this->getPrivateMethod($this->agentNavigation, 'injectContractsNavigation', [&$navigationArray]);

      $this->assertCount(2, $navigationArray[0]->children);

      $this->assertEquals('Create Contract', $navigationArray[0]->children[0]->name);

      $this->assertEquals('All Contracts', $navigationArray[0]->children[1]->name);
    }
  }
