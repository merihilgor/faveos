<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Agent;

use Tests\DBTestCase;
use Tests\AddOnTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Contract\ContractType;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\User;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;


/**
 * Tests For Contract dependency with ContractType, Product, Vendor
 * 
 * @author Danis John <danis.john@ladybirdweb.com>
 */


class ContractDependencyTest extends AddOnTestCase {

    
    /** It returns contract-type params **/
    public function getContractTypeParams (){

    	    $params = [ "name" => "Motog4plus", "page" => 1 ];
            return $params;
    }

    // /** It returns product params **/
    // public function getProductParams(){

    // 	    $params = [ "name" => "Motog4plus", "status" => 1, "manufacturer" => "Motorola", "Product_status" => 1,
	   //                  "mode_procurement" => 1, "department_access" => 1,"description" => "Product Motorola", "page" => 1 ];
    //         return $params;
    // }
    
    /** It returns vendor params **/
    public function getVendorParams(){

    	    $params = [ "name" => "Motorola", "email" => "moto@gmail.com", "primary_contact" => "97333987800",
    	                "address" => "Bangalore", "status" => "1", "description" => "Motorola Company",
    	                "page" => 1  ];
            return $params;
    }

    
    /** @group handleCreate **/ /** To create Contract-type via admin **/
    public function test_handleCreate_createContractTypeByAdmin(){

    	   $this->getLoggedInUserForWeb("admin");
    	   $params = $this->getContractTypeParams();
    	   $response = $this->call("POST",url("service-desk/contract-types/create"), $params);
    	   $response->assertStatus(200);
    }

    // /** @group productshandleCreate **/ /** To create Product via admin **/
    // public function test_productshandleCreate_createProductByAdmin(){

    // 	   $this->getLoggedInUserForWeb("admin");
    // 	   $params = $this->getProductParams();
    //        $response = $this->call("POST",url("service-desk/products/create"), $params);
    //        $response->assertStatus(200);
    // }

    /** @group handleCreate **/ /** To create vendor via admin **/
    public function test_handleCreate_createVendorByAdmin(){

    	   $this->getLoggedInUserForWeb("admin");
    	   $params = $this->getVendorParams();
           $response = $this->call("POST",url("service-desk/vendor/create"), $params);
           $response->assertStatus(200);
    }



     /** @group handleCreate **/ /** To create Contract-type via agent **/
    public function test_handleCreate_createContractTypeByAgent(){

    	   $this->getLoggedInUserForWeb("agent");
    	   $params = $this->getContractTypeParams();
    	   $response = $this->call("POST",url("service-desk/contract-types/create"), $params);
    	   $response->assertStatus(200);
    }

    // /** @group productshandleCreate **/ /** To create Product  via agent**/
    // public function test_productshandleCreate_createProductByAgent(){

    // 	   $this->getLoggedInUserForWeb("agent");
    // 	   $params = $this->getProductParams();
    //        $response = $this->call("POST",url("service-desk/products/create"), $params);
    //        $response->assertStatus(200);
    // }



   
      /** @group handleCreate **/ /** To create Contract-type via user **/
    public function test_handleCreate_createContractTypeByUser(){

    	   $this->getLoggedInUserForWeb("user");
    	   $params = $this->getContractTypeParams();
    	   $response = $this->call("POST",url("service-desk/contract-types/create"), $params);
    	   $response->assertStatus(302);
    }

    // /** @group productshandleCreate **/ /** To create Product via user **/
    // public function test_productshandleCreate_createProductByUser(){

    // 	   $this->getLoggedInUserForWeb("user");
    // 	   $params = $this->getProductParams();
    //        $response = $this->call("POST",url("service-desk/products/create"), $params);
    //        $response->assertStatus(302);
    // }

    /** @group handleCreate **/ /** To create vendor via user **/
    public function test_handleCreate_createVendorByUser(){

    	   $this->getLoggedInUserForWeb("user");
    	   $params = $this->getVendorParams();
           $response = $this->call("POST",url("service-desk/vendor/create"), $params);
           $response->assertStatus(302);
    }



    /** @group handleCreate **/ /**To create a contract via admin **/
    public function test_handleCreateContractByAdmin(){

            $this->getLoggedInUserForWeb("admin");
            $contractType = factory(ContractType::class)->create();
            $vendor       = factory(SdVendors::class)->create();
            $admin        = factory(User::class)->create(['role' => 'admin']);
            $agent        = factory(User::class)->create(['role' => 'agent']);
            $user         = factory(User::class)->create(['role' => 'user']);
            $response     = $this->call("POST",url("service-desk/contracts/create"),[

                           "name" => "Contract with Moto", "status_id" => "1",
                           "contract_type_id" => $contractType->id, "licensce_count" => "209",
                           "cost" => "212", "approver_id" => $admin->id, "description" => "xxxxxxxxxxxx",
                           "license_type_id" => "1", "vendor_id" => $vendor->id,
                           "contract_start_date" => "February 7, 2019, 3:46 pm", "contract_end_date" => "February 23, 2019, 3:46 pm",
                           "notify_before" => "12", "agent_ids" => [ 0 => $agent->id ],
                        ]);
            $response->assertStatus(302);
            $contract = SdContract::first();
            $this->assertDatabaseHas("sd_contracts", [ "name" =>"Contract with Moto", "description" => "xxxxxxxxxxxx", "cost" => "212",
                                                       "contract_type_id" => $contractType->id, "approver_id" => $admin->id,
                                                       "vendor_id" => $vendor->id, "license_type_id" => "1", 
                                                       "licensce_count" => "209",  "status_id" => "1", "notify_before" => "12"]);

    }

    /** @group handleCreate **/ /**To create a contract via agent **/
    public function test_handleCreateContractByAgent(){

            $this->getLoggedInUserForWeb("agent");
            $contractType = factory(ContractType::class)->create();
            $vendor       = factory(SdVendors::class)->create();
            $admin        = factory(User::class)->create(['role' => 'admin']);
            $agent        = factory(User::class)->create(['role' => 'agent']);
            $user         = factory(User::class)->create(['role' => 'user']);
            $response     = $this->call("POST",url("service-desk/contracts/create"),[

                           "name" => "Contract with Moto", "status_id" => "1",
                           "contract_type_id" => $contractType->id, "licensce_count" => "209",
                           "cost" => "212", "approver_id" => $admin->id, "description" => "xxxxxxxxxxxx",
                           "license_type_id" => "1", "vendor_id" => $vendor->id,
                           "contract_start_date" => "February 7, 2019, 3:46 pm", "contract_end_date" => "February 23, 2019, 3:46 pm",
                           "notify_before" => "12", "agent_ids" => [ 0 => $agent->id ]
                        ]);
            $response->assertStatus(302);
            $contract = SdContract::first();
            $this->assertDatabaseHas("sd_contracts", [ "name" =>"Contract with Moto", "description" => "xxxxxxxxxxxx", "cost" => "212",
                                                       "contract_type_id" => $contractType->id, "approver_id" => $admin->id,
                                                       "vendor_id" => $vendor->id, "license_type_id" => "1", 
                                                       "licensce_count" => "209",  "status_id" => "1", "notify_before" => "12"]);

    }


}