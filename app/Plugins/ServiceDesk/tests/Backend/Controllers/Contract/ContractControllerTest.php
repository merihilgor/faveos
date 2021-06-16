<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Contract;

use Carbon\Carbon;
use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\User;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Contract\SdContractSdUser;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Plugins\ServiceDesk\Model\Common\SdUser;
use App\Model\helpdesk\Settings\System;
use App\Plugins\ServiceDesk\Controllers\Contract\ContractController;
use App\Plugins\ServiceDesk\Console\Commands\ContractStatusExpired;
use App\Plugins\ServiceDesk\Model\Contract\SdContractThread;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;

/**
 * Tests few ContractController Relations and methods
 * Once the ContractController API's are created, following test cases could be replaced
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ContractControllerTest extends AddOnTestCase
{
  /** @group createContract */
  public function test_createContract_withStatusId()
  {
    $contract = SdContract::create(['status_id' => 1, 'approver_id' => 1, 'owner_id' => 1]);
    $this->assertCount(1, [$contract->owner()->first()->toArray()]);
    $contractArray = SdContract::get()->toArray();
    $this->assertCount(1, $contractArray);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'status_id' => 1]);
  }

  /** @group createContract */
  public function test_createContract_withStatusIdAndRenewalStatusId()
  {
    $contract = SdContract::create(['status_id' => 5, 'renewal_status_id' => 7, 'approver_id' => 1, 'owner_id' => 1, 'notify_before' => 3]);
    $contractArray = SdContract::get()->toArray();
    $this->assertEquals($contract->status_id, $contract->contractStatus()->first()->id);
    $this->assertEquals($contract->renewal_status_id, $contract->contractRenewalStatus()->first()->id);
    $this->assertCount(1, $contractArray);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'status_id' => 5]);
  }

  /** @group createContract */
  public function test_createContract_withNotifyAgents()
  {
    $agent = factory(User::class)->create(['role' => 'agent']);
    $contract = SdContract::create(['status_id' => 1, 'approver_id' => 1, 'owner_id' => 1]);
    $contract->notifyAgents()->sync($agent->id);
    $contractArray = SdContract::get()->toArray();
    $this->assertCount(1, $contractArray);
    $associatedAgent = $contract->notifyAgents()->get()->toArray();
    $this->assertCount(1, $associatedAgent);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract['id'], 'status_id' => 1]);
    $this->assertDatabaseHas('sd_contract_user', ['contract_id' => $contract->id, 'agent_id' => $agent->id]);
  }

  /** @group createContract */
  public function test_createContract_withApprover()
  {
    $agent = factory(User::class)->create(['role' => 'agent']);
    $contract = SdContract::create(['status_id' => 1, 'approver_id' => $agent->id, 'owner_id' => 1]);
    $contractArray = SdContract::get()->toArray();
    $this->assertCount(1, $contractArray);
    $associatedApprover = $contract->approverRelation()->get()->toArray();
    $this->assertCount(1, $associatedApprover);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract['id'], 'status_id' => 1, 'approver_id' => $agent->id]);
  }

  /** @group sendContractNotificationExpiryEmail */
  public function test_sendContractNotificationExpiryEmail_withExpiryCommand()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractIds = factory(SdContract::class, 5)->create()->pluck('id')->toArray();
    $contracts = new SdContract();
    $this->assertCount(5, $contracts->get()->toArray());
      $systemDateSet = Carbon::now('UTC');
    $systemDate =  $systemDateSet->toDateTimeString();
    $contract = $contracts->find(reset($contractIds));
    $contract->update(['contract_start_date' => date('Y-m-d h:i:s', strtotime($systemDate . '-10 day')), 'contract_end_date' => date('Y-m-d h:i:s', strtotime($systemDate . '+2 day')), 'notify_before' => 2]);
    $contract->notifyAgents()->sync([1, $this->user->id]);
    $this->assertEquals(0, $this->artisan('contract:notification-expiry')->run());
  }

  /** @group sendContractNotificationExpiryEmail */
  public function test_sendContractNotificationExpiryEmail_withExpiry()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractIds = factory(SdContract::class, 5)->create()->pluck('id')->toArray();
    $contracts = new SdContract();
    $this->assertCount(5, $contracts->get()->toArray());
    $systemDateSet = Carbon::now('UTC');
    $systemDate =  $systemDateSet->toDateTimeString();
    $contract = $contracts->find(reset($contractIds));
    $contract->update(['contract_start_date' => date('Y-m-d h:i:s', strtotime($systemDate . '-10 day')), 'contract_end_date' => date('Y-m-d h:i:s', strtotime($systemDate . '+2 day')), 'notify_before' => 2]);
    $contract->notifyAgents()->sync([1, $this->user->id]);
    $contractControllerObj = new ContractController();
    $this->assertEquals(null, $contractControllerObj->sendContractNotificationExpiry());
  }

  /** @group makeContractStatusExpired */
  public function test_makeContractStatusExpired_forExpiryDate()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractIds = factory(SdContract::class, 5)->create()->pluck('id')->toArray();
    $contracts = new SdContract();
    $this->assertCount(5, $contracts->get()->toArray());
    $contract = $contracts->find(reset($contractIds));
    $systemDateSet = Carbon::now('UTC');
    $systemDate =  $systemDateSet->toDateTimeString();
    $contract->update(['status_id' => 3,
     'contract_start_date' => date('Y-m-d h:i:s', strtotime($systemDate . '-10 day')),
     'contract_end_date' => date('Y-m-d h:i:s', strtotime($systemDate . '-1 day')),
     'notify_before' => 2
    ]);
    $contract->notifyAgents()->sync([1, $this->user->id]);
    $this->assertEquals(0, $this->artisan('contract:status-expired')->run());
    $this->assertEquals(5, $contracts->find($contract->id)->status_id);
  }

  /** @group makeContractStatusActive */
  public function test_makeContractStatusActive_forStartDate()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractIds = factory(SdContract::class, 5)->create()->pluck('id')->toArray();
    $contracts = new SdContract();
    $this->assertCount(5, $contracts->get()->toArray());
    $contract = $contracts->find(reset($contractIds));
    $systemDateSet = \Carbon\Carbon::createFromFormat(dateTimeFormat(), faveoDate(), timezone())->setTimezone('UTC');
    $systemDate =  $systemDateSet->toDateTimeString();
    $contract->update(['status_id' => 2,
     'contract_start_date' => date('Y-m-d h:i:s', strtotime($systemDate)),
     'contract_end_date' => date('Y-m-d h:i:s', strtotime($systemDate . '+10 day')),
     'notify_before' => 2
    ]);
    $contract->notifyAgents()->sync([1, $this->user->id]);
    $this->assertEquals(0, $this->artisan('contract:status-active')->run());
    $this->assertEquals(3, $contracts->find($contract->id)->status_id);
  }

  /** @group approveContract */
  public function test_approveContract_forExpiredContractRenewedDateIsMoreThenToday_makesContractRenewalStatusRenewed()
  {
    $this->getLoggedInUserForWeb('admin');
    // Created expired contract with renewal request
    $contract = factory(SdContract::class)->create([
      'id' => 5,
      'status_id' => 5,
      'approver_id' => $this->user->id,
      'renewal_status_id' => 7,
      'contract_start_date' => date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . '-20 day')),
      'contract_end_date' => date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . '-2 day'))
    ]);
    // contract thread data
    $thread = [
      'contract_id' => $contract->id,
      'status_id' => $contract->status_id,
      'renewal_status_id' => null,
      'cost' => $contract->cost,
      'contract_start_date' => $contract->contract_start_date,
      'contract_end_date' => $contract->contract_end_date,
      'owner_id' => $contract->owner_id,
      'approver_id' => $contract->approver_id
    ];
    // created contract thread for expired contract
    SdContractThread::create($thread);
    $thread['contract_start_date'] = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . '+2 day'));
    $thread['contract_end_date'] = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . '+20 day'));
    $thread['renewal_status_id'] = $contract->renewal_status_id;
    // created contract thread for renewed contract
    $threadId = SdContractThread::create($thread)->id;
    // contract is not renewed
    $this->assertDatabaseMissing('sd_contracts', ['id' => $contract->id, 'status_id' => $contract->status_id, 'renewal_status_id' => 8]);
    $response = $this->call('POST', url("service-desk/api/contract-approve/$contract->id"));
    // contract is renewed as approver approved
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'status_id' => $contract->status_id, 'renewal_status_id' => 8]);
    // contractract thread for the renewed contract
    $this->assertDatabaseHas('sd_contract_threads', ['id' => $threadId, 'contract_id' => $contract->id, 'status_id' => $contract->status_id, 'renewal_status_id' => 8]);
  }

  /** @group delete */
  public function test_delete_withContractId_returnContractDeleteSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create();
    $this->assertCount(1, SdContract::get()->toArray());
    $response = $this->call('GET', url("service-desk/contracts/$contract->id/delete"));
    $response->assertStatus(302);
    $this->assertDatabaseMissing('sd_contracts', ['id' => $contract->id]);
  }

   /** @group delete */
  public function test_delete_withWrongContractId_returnContractNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/contracts/1/delete"));
    $response->assertStatus(400);
  }

  /** @group handleCreate */
  public function test_handleCreate_createContractWithIdentifierLimitWithin45Characters_returnsRedirectionToContractIndexPage()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractData = ['name' => 'Dell Laptop', 'status_id' => 1, 'cost' => 12345, 'description' => 'Dell Laptop used by Dev1', 'contract_type_id' => 1, 'approver_id' => $this->user->id, 'vendor_id' => $vendorId, 'contract_start_date' => date('F j, Y, g:i a', strtotime(date('Y-m-d h:i:s') . '-2 day')), 'contract_end_date' => date('F j, Y, g:i a', strtotime(date('Y-m-d h:i:s') . '+100 day')), 'identifier' => 'LADY-BIRD-WEB-FAVEO-SERVICE-DESK-ADVANCE-2019'];
    $response = $this->call('POST', url('service-desk/contracts/create'), $contractData);
    $response->assertStatus(302);
    $this->assertTrue(strlen($contractData['identifier'])<=45);
    unset($contractData['contract_start_date'], $contractData['contract_end_date']);
    $this->assertDatabaseHas('sd_contracts', $contractData);
  }

  /** @group handleCreate */
  public function test_handleCreate_createContractWithMoreThanIdentifierLimit_returnsRedirectionToContractCreatePage()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractData = ['name' => 'Dell Laptop', 'status_id' => 1, 'cost' => 12345, 'description' => 'Dell Laptop used by Dev1', 'contract_type_id' => 1, 'approver_id' => $this->user->id, 'vendor_id' => $vendorId, 'contract_start_date' => date('F j, Y, g:i a', strtotime(date('Y-m-d h:i:s') . '-2 day')), 'contract_end_date' => date('F j, Y, g:i a', strtotime(date('Y-m-d h:i:s') . '+100 day')), 'identifier' => 'LADY-BIRD-WEB-FAVEO-SERVICE-DESK-ADVANCE-2019-EXTRA'];
    $response = $this->call('POST', url('service-desk/contracts/create'), $contractData);
    $response->assertStatus(302);
    $this->assertTrue(strlen($contractData['identifier'])>45);
    unset($contractData['contract_start_date'], $contractData['contract_end_date']);
    $this->assertDatabaseMissing('sd_contracts', $contractData);
  }

   /** @group handleCreate */
  public function test_handleCreate_createContractWithDescriptionLimitMoreThan255Characters_returnsRedirectionToContractIndexPage()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractData = ['name' => 'Dell Laptop', 'status_id' => 1, 'cost' => 12345, 'description' => 'Dell is among one of the best and most reliable laptop brands. So, if you have made up your mind in favour of a dell laptop, then you can simply buy from Gadgets Now website.  The online gadgets store makes you available laptops of all types belonging to different series as per the required needs and configuration.', 'contract_type_id' => 1, 'approver_id' => $this->user->id, 'vendor_id' => $vendorId, 'contract_start_date' => date('F j, Y, g:i a', strtotime(date('Y-m-d h:i:s') . '-2 day')), 'contract_end_date' => date('F j, Y, g:i a', strtotime(date('Y-m-d h:i:s') . '+100 day')), 'identifier' => 'LADY-BIRD-WEB-FAVEO-SERVICE-DESK-ADVANCE-2019'];
    $response = $this->call('POST', url('service-desk/contracts/create'), $contractData);
    $response->assertStatus(302);
    $this->assertTrue(strlen($contractData['description'])>255);
    unset($contractData['contract_start_date'], $contractData['contract_end_date']);
    $this->assertDatabaseHas('sd_contracts', $contractData);
  }


}