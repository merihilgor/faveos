<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Announcement;

use Tests\AddOnTestCase;
// to fetch data through extra relation
use App\Plugins\ServiceDesk\Model\Common\SdDepartment;
// to use it in factory
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\User;
// to use it in factory
use App\Model\helpdesk\Agent_panel\Organization;
// to fetch data through extra relation
use App\Plugins\ServiceDesk\Model\Common\SdOrganization;

/**
 * Tests ApiAnnouncementController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiAnnouncementControllerTest extends AddOnTestCase
{
  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withoutRequiredOptionField_returnsOptionFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'department_id' => 1,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(412);
  }

   /** @group makeAnnouncement */
  public function test_makeAnnouncement_withDepartmentAndAgentLoggedIn_returnsMiddlewareErrorPageWithRedirection()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'department_id',
        'department_id' => 1,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    // html pages comes with redirection (not passing middleware , as logged in user is agent instead of admin)
    $response->assertStatus(302);
  }


  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withDepartmentAndWithDepartmentMembers_returnsAnnouncementHasBeenMadeSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $admin = factory(User::class)->create(['is_delete' => 0, 'active' => 1]);
    $departmentId = factory(Department::class)->create(['name' => 'Test'])->id;
    $department = SdDepartment::find($departmentId);
    $department->agents()->sync($admin->id);
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'department_id',
        'department_id' => $departmentId,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(200);
    $successMessage = json_decode($response->content())->message;
    $this->assertEquals(trans('ServiceDesk::lang.announced'), $successMessage);
  }

  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withDepartmentAndWithoutDepartmentMembers_returnsNoMembers()
  {
    $this->getLoggedInUserForWeb('admin');
    $departmentId = factory(Department::class)->create(['name' => 'Test'])->id;
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'department_id',
        'department_id' => $departmentId,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(400);
    $errorMessage = json_decode($response->content())->message;
    $this->assertEquals(trans('lang.no_members'), $errorMessage);
  }

  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withWrongDepartment_returnsExceptionSelectedDepartmentIdFieldIsInvalid()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'department_id',
        'department_id' => 'wrong-department',
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(412);
  }

  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withDepartmentOptionAndPassedOrganizationId_returnsDepartmentIdFieldIsRequiredWhenOptionIsDepartmentId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'department_id',
        'organization_id' => 1,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(412);
  }

  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withOrganization_returnsSelectedOrganizationFieldIsInvalid()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'organization_id',
        'organization_id' => 1,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(412);
  }

  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withOrganizationAndWithOrganizationMembers_returnsAnnouncementHasBeenMadeSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $admin = factory(User::class)->create(['is_delete' => 0, 'active' => 1]);
    $organizationId = factory(Organization::class)->create(['name' => 'Test'])->id;
    $organization = SdOrganization::find($organizationId);
    $organization->members()->sync($admin->id);
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'organization_id',
        'organization_id' => $organizationId,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(200);
    $successMessage = json_decode($response->content())->message;
    $this->assertEquals(trans('ServiceDesk::lang.announced'), $successMessage);
  }

  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withOrganizationAndWithoutOrganizationMembers_returnsNoMembers()
  {
    $this->getLoggedInUserForWeb('admin');
    $organizationId = factory(Organization::class)->create(['name' => 'Test'])->id;
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'organization_id',
        'organization_id' => $organizationId,
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(400);
    $errorMessage = json_decode($response->content())->message;
    $this->assertEquals(trans('lang.no_members'), $errorMessage);
  }

  /** @group makeAnnouncement */
  public function test_makeAnnouncement_withWrongOrganization_returnsExceptionSelectedOrganizationIdFieldIsInvalid()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/announcement'), [
        'option' => 'organization_id',
        'organization_id' => 'wrong-organization',
        'announcement' => "Laptop battery damaged",
        'subject'=>"Laptop not working"
      ]
    );
    $response->assertStatus(412);
  }

}