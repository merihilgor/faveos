<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Contract;

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
use Lang;

/**
 * Tests ContractThreadController for contract history
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ContractThreadControllerTest extends AddOnTestCase
{
  /**
   * method to create a contract thread
   * @return array contractThreadId ContractId
  */
  private function createContractThread()
  {
    $contract = factory(SdContract::class)->create(['renewal_status_id' => 12]);
    $response = $this->call('POST', url('service-desk/api/contract-thread'), [
      'contract_id' => $contract->id,
      'status_id' => $contract->status_id,
      'renewal_status_id' => $contract->renewal_status_id,
      'cost' => $contract->cost,
      'contract_start_date' => $contract->contract_start_date,
      'contract_end_date' => $contract->contract_end_date,
      'approver_id' => $contract->approver_id
    ]);
    return [SdContractThread::first()->id, $contract->id];
  }

  /** @group createUpdateContractThread */
  public function test_createUpdateContractThread_withoutRequiredFields()
  {
    $this->getLoggedInUserForWeb('agent');
    $contract = factory(SdContract::class)->create();
    $response = $this->call('POST', url('service-desk/api/contract-thread'));
    $response->assertStatus(412);
    $this->assertTrue(SdContractThread::get()->isEmpty());
  }

  /** @group createUpdateContractThread */
  public function test_createUpdateContractThread_withoutThreadId()
  {
    $this->getLoggedInUserForWeb('agent');
    $contract = factory(SdContract::class)->create();
    $response = $this->call('POST', url('service-desk/api/contract-thread'), [
      'contract_id' => $contract->id,
      'status_id' => $contract->status_id,
      'renewal_status_id' => $contract->renewal_status_id,
      'cost' => $contract->cost,
      'contract_start_date' => $contract->contract_start_date,
      'contract_end_date' => $contract->contract_end_date,
      'approver_id' => $contract->approver_id
    ]);
    $response->assertStatus(200);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_thread_saved_successfully'));
    $this->assertCount(1, SdContractThread::get()->toArray());
    $this->assertDatabaseHas('sd_contract_threads', ['contract_id' => $contract->id, 'status_id' => $contract->status_id]);
  }

  /** @group createUpdateContractThread */
  public function test_createUpdateContractThread_withThreadId()
  {
    $this->getLoggedInUserForWeb('agent');
    [$contractThreadId, $contractId] = $this->createContractThread();
    $contract = SdContract::find($contractId);
    $response = $this->call('POST', url('service-desk/api/contract-thread'), [
      'id' => $contractThreadId,
      'contract_id' => $contract->id,
      'status_id' => $contract->status_id,
      'cost' => 50000,
      'contract_start_date' => $contract->contract_start_date,
      'contract_end_date' => $contract->contract_end_date,
      'approver_id' => $contract->approver_id
    ]);
    $response->assertStatus(200);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_thread_saved_successfully'));
    $this->assertCount(1, SdContractThread::get()->toArray());
    $this->assertDatabaseHas('sd_contract_threads', ['contract_id' => $contract->id, 'cost' => 50000]);
  }

  /** @group editContractThread */
  public function test_editContractThread_withThreadId()
  {
    $this->getLoggedInUserForWeb('agent');
    $contractThreadId = $this->createContractThread();
    $contractThreadId = reset($contractThreadId);
    $response = $this->call('GET', url("service-desk/api/contract-thread/$contractThreadId"));
    $contractThread = json_decode($response->content())->data->contract_thread;
    $response->assertStatus(200);
    $this->assertEquals(1, SdContractThread::count());
    $this->assertDatabaseHas('sd_contract_threads', ['id' => $contractThread->id, 'cost' => $contractThread->cost]);
  }

  /** @group editContractThread */
  public function test_editContractThread_withWrongThreadId()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url("service-desk/api/contract-thread/wrong-thread-id"));
    $response->assertStatus(400);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_thread_not_found'));
    $this->assertEquals(0, SdContractThread::count());
  }

  /** @group getContractThreads */
  public function test_getContractThreads_withWrongContractId()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url("service-desk/api/contract-threads/wrong-contract-id"));
    $response->assertStatus(400);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_not_found'));
    $this->assertEquals(0, SdContract::count());
  }

  /** @group getContractThreads */
  public function test_getContractThreads_withContractId()
  {
    $this->getLoggedInUserForWeb('agent');
    [$contractThreadId, $contractId] = $this->createContractThread();
    $response = $this->call('GET', url("service-desk/api/contract-threads/$contractId"));
    $response->assertStatus(200);
    $contractThreads = json_decode($response->content())->data->contract_threads;
    $this->assertCount(1, $contractThreads);
    $this->assertEquals(1, SdContract::count());
    $this->assertEquals(1, SdContractThread::count());
    $contractThread = reset($contractThreads);
    $this->assertDatabaseHas('sd_contract_threads', ['id' => $contractThread->id, 'cost' => $contractThread->cost]);
  }

  /** @group deleteContractThread */
  public function test_deleteContractThread_withWrongThreadId()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('DELETE', url("service-desk/api/contract-thread/wrong-thread-id"));
    $response->assertStatus(400);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_thread_not_found'));
    $this->assertEquals(0, SdContractThread::count());
  }

  /** @group deleteContractThread */
  public function test_deleteContractThread_withContractThreadId()
  {
    $this->getLoggedInUserForWeb('agent');
    $contractThreadId = $this->createContractThread();
    $contractThreadId = reset($contractThreadId);
    $response = $this->call('DELETE', url("service-desk/api/contract-thread/$contractThreadId"));
    $response->assertStatus(200);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_thread_deleted_successfully'));
    $this->assertEquals(0, SdContractThread::count());
  }

  /** @group deleteContractThreads */
  public function test_deleteContractThreads_withWrongContractId()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('DELETE', url("service-desk/api/contract-threads/wrong-Contract-id"));
    $response->assertStatus(400);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_not_found'));
    $this->assertEquals(0, SdContract::count());
  }

  /** @group deleteContractThreads */
  public function test_deleteContractThreads_withContractId()
  {
    $this->getLoggedInUserForWeb('agent');
    $contractId = $this->createContractThread();
    $contractId = last($contractId);
    $response = $this->call('DELETE', url("service-desk/api/contract-threads/$contractId"));
    $response->assertStatus(200);
    $message = json_decode($response->content())->message;
    $this->assertEquals($message, Lang::get('ServiceDesk::lang.contract_threads_deleted_successfully'));
    $this->assertEquals(0, SdContractThread::count());
  }

}