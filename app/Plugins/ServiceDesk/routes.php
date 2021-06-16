<?php

Route::group(['prefix' => 'service-desk', 'middleware' => ['web', 'auth']], function() {

    \Event::listen('service.desk.admin.settings', function() {
        $controller = new App\Plugins\ServiceDesk\Controllers\InterfaceController();
        echo $controller->adminSettings();
    });
    \Event::listen('App\Events\TicketDetailTable', function($event) {
        $controller = new App\Plugins\ServiceDesk\Controllers\InterfaceController();
        echo $controller->ticketDetailTable($event);
    });

    \Event::listen('service.desk.organization.assets.show', function($data) {
         $controller = new App\Plugins\ServiceDesk\Controllers\InterfaceController();
         echo $controller->assetListBasedOrganization($data);
    });

    \Event::listen('service.desk.user.assets.show', function($data) {
         $controller = new App\Plugins\ServiceDesk\Controllers\InterfaceController();
         echo $controller->assetListBasedUser($data);
    });

    //Servicedesk Agent permission Create & Update
    \Event::listen('agent-permission-data-submiting', function($agent, $user) {
         $controller = new App\Plugins\ServiceDesk\Controllers\Permission\PermissionController();
         echo $controller->handlecreateUpdate($agent, $user);
    });

    //To fetch the agent details
    \Event::listen('agent-permission-data-getting', function($id, &$agent) {
         $controller = new App\Plugins\ServiceDesk\Controllers\Permission\PermissionController();
         $controller->editAgent($id, $agent);
    });

    //To fetch contracts
    \Event::listen('service.desk.organization.contracts.show', function($organizationId) {
         $controller = new App\Plugins\ServiceDesk\Controllers\InterfaceController();
         echo $controller->contractListBasedOnOrganization($organizationId);
    });


// ======================================= ======================================= =======================================
// =======================================   NEWLY CREATED LISTENERS BY AVINASH    =======================================
// ======================================= ======================================= =======================================

    \Event::listen('timeline-data-dispatch', function(&$ticket) {
         (new App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController)->appendProblemToTimelineData($ticket);
    });

    \Event::listen('timeline-actions-visibility-dispatch', function(&$allowedActions, $ticketId) {
         (new App\Plugins\ServiceDesk\Controllers\Ticket\TicketsActionOptionsController)->appendSdTicketActions($allowedActions, $ticketId);
    });

        \Event::listen('agent-panel-navigation-data-dispatch', function(&$navigationContainer) {
          (new App\Plugins\ServiceDesk\Controllers\Navigation\SdAgentNavigationController)->injectSdAgentNavigation($navigationContainer);
        });

        \Event::listen('agent-panel-report-navigation-data-dispatch', function(&$navigationObject) {
          (new App\Plugins\ServiceDesk\Controllers\Navigation\SdAgentNavigationController)->injectSdReportNavigation($navigationObject);
        });

        \Event::listen('admin-panel-navigation-data-dispatch', function(&$navigationContainer) {
          (new App\Plugins\ServiceDesk\Controllers\Navigation\SdAdminNavigationController)->injectSdAdminNavigation($navigationContainer);
        });

    // ====================================================================================================

    //Servicedesk in app push notification
    \Event::listen('push-in-app-notification', function(&$content, $model, $key) {
         $notification = new App\Plugins\ServiceDesk\Controllers\Common\SdNotificationController();
         $notification->getSdContent($content, $model, $key);
    });

    //Servicedesk to fetch specific module view url
    \Event::listen('fetch-sd-url', function(&$url, $model) {
         $notification = new App\Plugins\ServiceDesk\Controllers\Common\SdNotificationController();
         $notification->getSdUrl($url, $model);
    });

    //Servicedesk event to update template variables
    \Event::listen('update_template_variable_shortcode_query_builder', function($data) {
        (new App\Plugins\ServiceDesk\Controllers\Common\SdTemplateController)->updateTemplateVariables($data);
    });

    //Servicedesk event to update template list
    \Event::listen('update_template_list_query_builder', function($data) {
        (new App\Plugins\ServiceDesk\Controllers\Common\SdTemplateController)->updateTemplateList($data);
    });

    //Servicedesk event to update approval workflow type to cab
    \Event::listen('approvalworkflow-type', function(&$type) {
        (new App\Plugins\ServiceDesk\Controllers\Cab\ApiCabController)->changeTypeToCab($type);
    });

    // Servicedesk event to update approval workflow/cab status on approve and deny
    \Event::listen('approvalworkflow-action-status', function(&$data) {
        (new App\Plugins\ServiceDesk\Controllers\Cab\ApiCabController)->changeApproveDenyStatus($data);
    });

    // Servicedesk event to add servicedesk form categories
    \Event::listen('form-category-query', function(&$formCategoryQuery) {
        (new App\Plugins\ServiceDesk\Controllers\Common\Dependency\SdDependencyController)->updateFormCategoryQuery($formCategoryQuery);
    });

    // Servicedesk event to add servicedesk form groups
    \Event::listen('form-group-query', function(&$formGroupQuery, $supplements) {
        (new App\Plugins\ServiceDesk\Controllers\Common\Dependency\SdDependencyController)->updateFormGroupQuery($formGroupQuery, $supplements);
    });

    // Servicedesk event to add append assets relation with tickets query
    // filter tickets based on asset id/ids
    \Event::listen('append-extra-filter-parameters', function($ticketsQuery, $request) {
        (new App\Plugins\ServiceDesk\Controllers\Ticket\TicketsListController)->addFilterTicketByServiceDeskRelationsQuery($ticketsQuery, $request);
    });

    // update base query from helpdesk ticket model to servicedesk ticket model
    \Event::listen('update-base-query', function(&$baseQuery) {
        (new App\Plugins\ServiceDesk\Controllers\Ticket\TicketsListController)->updateBaseQuery($baseQuery);
    });

    // update service-desk prefix in asset formgroups for edit
    \Event::listen('update-other-prefix-url', function(&$otherUrlPrefix) {
        (new App\Plugins\ServiceDesk\Controllers\FormGroup\SdFormController)->updateServiceDeskPrefix($otherUrlPrefix);
    });

    // delete extra dependencies while deleting form field
    \Event::listen('delete-extra-entries', function($formFieldId) {
        App\Plugins\ServiceDesk\Model\FormGroup\FormField::find($formFieldId)->customFieldActivityLog()->delete();
    });




    /**
     * Admin module
     */
    Route::group(['middleware' => 'role.agent'], function() {

        /**
         * Routes for QR Code Generation
         */
        //For Generating QR Barcode
        Route::get('generate-barcode','App\Plugins\ServiceDesk\Controllers\Assets\BarcodeController@generate');

        //For Deleting Barcode Template
        Route::delete('barcode/template/delete/{template}','App\Plugins\ServiceDesk\Controllers\Assets\BarcodeTemplateController@deleteTemplate');

        //For Getting barcode templates
        Route::get('barcode-template', 'App\Plugins\ServiceDesk\Controllers\Assets\BarcodeTemplateController@index');

        //For Barcode Settings View Page
        Route::get('barcode/settings', 'App\Plugins\ServiceDesk\Controllers\Assets\BarcodeTemplateController@create')->name('servicedesk.barcode.settings');

        //For Creating New Barcode Template
        Route::post('barcode/template/create', 'App\Plugins\ServiceDesk\Controllers\Assets\BarcodeTemplateController@store');

        //For updating existing Barcode Template
        Route::post('barcode/template/update/{template}', 'App\Plugins\ServiceDesk\Controllers\Assets\BarcodeTemplateController@update');

        /**
         * Product Managing Module
         */
        
        Route::get('products', ['as' => 'service-desk.products.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@productsIndexPage']);

        Route::get('products/create', ['as' => 'service-desk.products.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@productCreatePage']);
        Route::post('products/create', ['as' => 'service-desk.post.products', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@productshandleCreate']);
        Route::get('products/{id}/edit', ['as' => 'service-desk.products.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@productEditPage']);
        Route::patch('products/{id}', ['as' => 'service-desk.products.postedit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@productshandleEdit']);
        Route::get('products/{id}/delete', ['as' => 'service-desk.products.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@productsHandledelete']);
        Route::get('get-products', ['as' => 'service-desk.products.get', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@getProducts']);
        Route::post('products/add/vendor', ['as' => 'products.vendor.add', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@addVendor']);
        Route::get('products/{productid}/remove/{vendorid}/vendor', ['as' => 'products.vendor.add', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@removeVendor']);
        Route::post('products/add-existing/vendor', ['as' => 'products.vendor.add', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@addExistingVendor']);

        //Route to get Product based on product id
        Route::get('api/product/{productId}', 'App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@getProduct');
        //Route to get Product List
        Route::get('api/product-list', 'App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@getProductList');
        //Route to delete product based on product Id
        Route::delete('api/product-delete/{productId}','App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@deleteProduct');
        //route to create edit product
        Route::post('api/product', 'App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@createUpdateProduct');
        //Route to get associated vendor list based on product id
        Route::get('api/product/vendor/{productId}', 'App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@getAssociatedVendorList');
        //Route to attach vendor to product based on productId and vendor ids (vendor ids is passed in request)
        Route::post('api/product/attach/vendor','App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@attachVendor');
        //Route to attach asset to product based on productId and asset ids (asset ids is passed in request)
        Route::post('api/product/attach-asset','App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@attachAsset');
        //Route to detach asset to product based on productId and assetId
        Route::delete('api/product/detach-asset/{productId}/{assetId}','App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@detachAsset');
        //Route to detach vendor from product based on productId and vendorId
        Route::delete('api/product/detach-vendor/{productId}/{vendorId}','App\Plugins\ServiceDesk\Controllers\Products\ApiProductController@detachVendor');
        /**
         * Contract Managing Module
         */
        Route::get('contracts', ['as' => 'service-desk.contract.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@contractIndexPage']);
        Route::get('contracts/{contract}/edit', ['as' => 'service-desk.contract.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@contractEditPage']);
        Route::get('contracts/create', ['as' => 'service-desk.contract.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@contractCreatePage']);
        Route::post('contracts/create', ['as' => 'service-desk.contract.postcreate', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@handleCreate']);
        Route::patch('contracts/{id}', ['as' => 'service-desk.contract.postedit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@handleEdit']);
        Route::get('contracts/{id}/delete', ['as' => 'service-desk.contract.postdelete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@delete']);
        Route::get('get-contracts', ['as' => 'service-desk.contract.get', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@getContracts']);
        Route::post('contract/uploadfile/delete/{id}', ['as' => 'contract.uploadfile.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@deleteUploadfile']);
        Route::get('contract/export',['as'=>'contract.export', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@exportContract']);
        Route::post('asset/attach/contracts', ['as' => 'asset.attach.contract', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@attachAssetToContract']);

        // Route to renew contract based on contract id
        //Route::post('api/contract-renew/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@renewContract');

        // Route to extend contract based on contract id
        //Route::post('api/contract-extend/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@extendContract');

        // Route to terminate contract based on contract id
        //Route::post('api/contract-terminate/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@terminateContract');

        // Route to approve contract based on contract id
        //Route::post('api/contract-approve/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@approveContract');

        // Route to approve contract based on contract id
        //Route::post('api/contract-reject/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@rejectContract');

        // route to delete contract based on contract id
        Route::delete('api/contract/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@deleteContract')->middleware('sd-permissions');

        // route to get contract list
        Route::get('api/contract-list',
            'App\Plugins\ServiceDesk\Controllers\Contract\ContractListController@getContractList');

        //list of notify agents
        Route::get('get/notifiers/{id}','App\Plugins\ServiceDesk\Controllers\Contract\ContractController@getNotifiedAgents');

        Route::get('contract/organization/{orgid}', ['as' => 'get.contracts.organization', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@getAttachedContractsBasedOnOrganization']);

        // route to get contracts based on ticket id through asset attached to contract and ticket
        Route::get('api/contract/ticket/{ticketId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@getContractsBasedOnAssetAttachedToTicket');

        // route to create update contract
        Route::post('api/contract', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@createUpdateContract');
        // route to get contract based on contractId
        Route::get('api/contract/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@getContract');
        //route to get organization based on User id
        Route::get('api/user-organization/{userId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@organizationBasedOnUser');

        // route to attach assets to contracts (contractId and assetIds will be passed through request)
        Route::post('api/contract-attach-asset', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@attachAssets');

        // route to detach assets from contracts based on contractId and assetId
        Route::delete('api/contract-detach-asset/{contract}/{asset}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@detachAsset');

        // route to renew contract based on contract id
        Route::post('api/contract-renew/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@renewContract');
        // route to approve contract based on contract id
        Route::post('api/contract-approve/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@approveContract');
        // route to extend contract based on contract id
        Route::post('api/contract-extend/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@extendContract');
        // route to terminate contract based on contract id
        Route::post('api/contract-terminate/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@terminateContract');
        // route to reject contract based on contract id
        Route::post('api/contract-reject/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@rejectContract');
        // route for contract activity-log based on contract-log
        Route::get('api/contract-log/{contractId}','App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@getContractActivityLog');
        //route to update contract expiry reminder based on contract id
        Route::post('api/contract-expiry-reminder/{contract}','App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@expiryReminderContract');
        //route to get notifiedTo based on contract id
        Route::get('api/contract-notifyTo/{contract}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@getNotifiedTo');
        //route to get contract action list based on contractId
        Route::get('api/contract-action/{contract}','App\Plugins\ServiceDesk\Controllers\Contract\ContractActionOptionsController@getContractActionList');
        /**
         * Contract Thread Managing Module
        */

        // Route to create and update contract thread for contract history
        Route::post('api/contract-thread/', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController@createUpdateContractThread');

        // Route to edit contract thread based on contract thread id
        Route::get('api/contract-thread/{contractThreadId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController@editContractThread');

        // Route to edit contract threads based on contract id
        Route::get('api/contract-threads/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController@getContractThreads');

        // Route to delete contract threads based on contract id
        Route::delete('api/contract-threads/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController@deleteContractThreads');

        // Route to delete contract thread based on contract thread id
        Route::delete('api/contract-thread/{contractThreadId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController@deleteContractThread');

        /**
         * Vendor  Managing Module
         */
        Route::get('vendor', ['as' => 'service-desk.vendor.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@vendorsIndexPage']);
        Route::get('vendor/create', ['as' => 'service-desk.vendor.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@vendorCreatePage']);
        Route::get('vendor/{vendorId}/edit', ['as' => 'service-desk.vendor.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@vendorEditPage']);
        Route::get('vendor/{id}/show', ['as' => 'service-desk.vendor.show', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@vendorViewPage']);

        // Route to create and update vendor
        Route::post('api/vendor', 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@createUpdateVendor');

        // Route to get vendor details based on vendor id
        Route::get('api/vendor/{vendorId}', 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@getVendor');

        // Route to get vendor list
        Route::get('api/vendor-list', 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@getVendorList');

        // Route to delete vendor based on vendor id
        Route::delete('api/vendor/{vendor}', 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@deleteVendor');

        // Route to get associated product list based on vendor id
        Route::get('api/vendor/product/{vendorId}', 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@getAssociatedProductList');

        // Route to get associated contract list based on vendor id
        Route::get('api/vendor/contract/{vendorId}', 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@getAssociatedContractList');

        Route::post('vendor/create', ['as' => 'service-desk.vendor.postcreate', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Vendor\ApiVendorController@handleCreate']);


        /**
         * Assets type Managing Module
         */
        Route::get('assetstypes', ['as' => 'service-desk.assetstypes.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@index']);
        Route::get('assetstypes/create', ['as' => 'service-desk.assetstypes.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@create']);
        Route::post('assetstypes/create', ['as' => 'service-desk.post.assetstypes', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@handleCreate']);
        Route::get('assetstypes/{id}/edit', ['as' => 'service-desk.assetstypes.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@edit']);
        Route::patch('assetstypes/{id}', ['as' => 'service-desk.assetstypes.postedit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@handleEdit']);
        Route::get('assetstypes/{id}/delete', ['as' => 'service-desk.assetstypes.delet', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@assetHandledelete']);
        Route::get('get-assetstypes', ['as' => 'service-desk.assetstypes.get', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@getAssetstypes']);
        Route::get('asset-types/form/{assetid?}', ['as' => 'service-desk.assetstypes.form', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assetstypes\AssetstypesController@renderForm']);

        // Route to create and update asset type
        Route::post('api/asset-type', 'App\Plugins\ServiceDesk\Controllers\Assetstypes\ApiAssetTypeController@createUpdateAssetType');
        
        // Route to get asset type based on assetType(assetTypeId)
        Route::get('api/asset-type/{assetType}', 'App\Plugins\ServiceDesk\Controllers\Assetstypes\ApiAssetTypeController@getAssetType');

        //Route to get asset type list
        Route::get('api/asset-type-list', 'App\Plugins\ServiceDesk\Controllers\Assetstypes\ApiAssetTypeController@getAssetTypeList');
        // Route to delete asset type based on assetType(assetTypeId)
        Route::delete('api/asset-type/{assetType}', 'App\Plugins\ServiceDesk\Controllers\Assetstypes\ApiAssetTypeController@deleteAssetType');

        /**
         * Asset statuses module routes
         */
        // Route to asset status index blade page
        Route::get('asset-statuses', ['as' => 'service-desk.asset-status.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\AssetStatus\ApiAssetStatusController@assetStatusesIndexPage']);
        // Route to asset create blade page
        Route::get('asset-status/create', ['as' => 'service-desk.asset-status.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\AssetStatus\ApiAssetStatusController@assetStatusCreatePage']);
        // Route to asset edit blade page based on assetStatus (assetStatusId)
        Route::get('asset-status/{assetStatus}/edit', ['as' => 'service-desk.asset-status.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\AssetStatus\ApiAssetStatusController@assetStatusEditPage']);
        // Route to create and update asset status
        Route::post('api/asset-status', 'App\Plugins\ServiceDesk\Controllers\AssetStatus\ApiAssetStatusController@createUpdateAssetStatus');
        // Route to get asset status based on assetStatusId (assetStatus)
        Route::get('api/asset-status/{assetStatus}', 'App\Plugins\ServiceDesk\Controllers\AssetStatus\ApiAssetStatusController@getAssetStatus');
        //Route to get asset status list
        Route::get('api/asset-status-list', 'App\Plugins\ServiceDesk\Controllers\AssetStatus\ApiAssetStatusController@getAssetStatusList');
        // Route to delete asset type based on assetStatusId (assetStatus)
        Route::delete('api/asset-status/{assetStatus}', 'App\Plugins\ServiceDesk\Controllers\AssetStatus\ApiAssetStatusController@deleteAssetStatus');

        /**
         * Contract type Managing Module
         */Route::get('contract-types', ['as' => 'service-desk.contractstypes.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@contractTypesIndexPage']);
        Route::get('contract-types/create', ['as' => 'service-desk.contractstypes.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@contractTypeCreatePage']);
        Route::get('contract-types/{contractTypeId}/edit', ['as' => 'service-desk.contractstypes.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@contractTypeEditPage']);
        Route::get('contract-types', ['as' => 'service-desk.contractstypes.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@contractTypesIndexPage']);
        Route::get('contract-types/create', ['as' => 'service-desk.contractstypes.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@contractTypeCreatePage']);
        Route::get('contract-types/{contractTypeId}/edit', ['as' => 'service-desk.contractstypes.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@contractTypeEditPage']);
        // Route to create and update contract type
        Route::post('api/contract-type', 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@createUpdateContractType');
        // Route to get contract type based on contract type id
        Route::get('api/contract-type/{contractTypeId}', 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@getContractType');
        //Route to get contract type list
        Route::get('api/contract-type-list', 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@getContractTypeList');
        // Route to delete contract type based on contract type id
        Route::delete('api/contract-type/{contractType}', 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@deleteContractType');
        Route::post('contract-types/create', ['as' => 'service-desk.post.contractstypes', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contracttypes\ApiContractTypeController@handleCreate']);


        /**
         * License type Managing Module
         */
        Route::get('license-types', ['as' => 'service-desk.licensetypes.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Licensetypes\ApiLicenseTypeController@licenseTypesIndexPage']);
        Route::get('license-types/create', ['as' => 'service-desk.licensetypes.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Licensetypes\ApiLicenseTypeController@licenseTypeCreatePage']);
        Route::get('license-types/{licenseTypeId}/edit', ['as' => 'service-desk.licensetypes.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Licensetypes\ApiLicenseTypeController@licenseTypeEditPage']);
        // Route to create and update license type
        Route::post('api/license-type', 'App\Plugins\ServiceDesk\Controllers\Licensetypes\ApiLicenseTypeController@createUpdateLicenseType');
        // Route to get license type based on license type id
        Route::get('api/license-type/{licenseTypeId}', 'App\Plugins\ServiceDesk\Controllers\Licensetypes\ApiLicenseTypeController@getLicenseType');
        //Route to get license type list
        Route::get('api/license-type-list', 'App\Plugins\ServiceDesk\Controllers\Licensetypes\ApiLicenseTypeController@getLicenseTypeList');
        // Route to delete license type based on license type id
        Route::delete('api/license-type/{licenseTypeId}', 'App\Plugins\ServiceDesk\Controllers\Licensetypes\ApiLicenseTypeController@deleteLicenseType');

        /**
         * Procurment  Managing Module
         */
        Route::get('procurement/create', ['as' => 'service-desk.procurment.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Procurment\ApiProcurementModeController@procurementModeCreatePage']);
        Route::get('procurement', ['as' => 'service-desk.procurment.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Procurment\ApiProcurementModeController@procurementModesIndexPage']);
        Route::get('procurement/{procurementModeId}/edit', ['as' => 'service-desk.procurment.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Procurment\ApiProcurementModeController@procurementModeEditPage']);
        // Route to create and update procurement mode
        Route::post('api/procurement-mode', 'App\Plugins\ServiceDesk\Controllers\Procurment\ApiProcurementModeController@createUpdateProcurementMode');
        // Route to get procurement mode based on procurement mode id
        Route::get('api/procurement-mode/{procurementModeId}', 'App\Plugins\ServiceDesk\Controllers\Procurment\ApiProcurementModeController@getProcurementMode');
        //Route to get procurement mode list
        Route::get('api/procurement-mode-list', 'App\Plugins\ServiceDesk\Controllers\Procurment\ApiProcurementModeController@getProcurementModeList');
        // Route to delete procurement mode based on procurement mode id
        Route::delete('api/procurement-mode/{procurementMode}', 'App\Plugins\ServiceDesk\Controllers\Procurment\ApiProcurementModeController@deleteProcurementMode');

        /**
         * Form Group
         */
        // route to form group create blade page
        Route::get('form-group/create', ['as' => 'form.group.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\FormGroup\SdFormController@formGroupCreatePage']);

        // route to form group edit blade page based on formGroup (formGroupId)
        Route::get('form-group/edit/{formGroupId}', ['as' => 'form.group.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\FormGroup\SdFormController@formGroupEditPage']);

        // route to create update form group
        Route::post('api/post-form-group', 'App\Plugins\ServiceDesk\Controllers\FormGroup\SdFormController@createUpdateFormGroupAndFormFields');

        // route to get form group details based on form group id
        Route::get('api/get-form-group/{formGroup}', 'App\Plugins\ServiceDesk\Controllers\FormGroup\SdFormController@getFormGroupFormFields');

        // route to get form group list
        Route::get('api/form-group-list', 'App\Plugins\ServiceDesk\Controllers\FormGroup\SdFormController@getFormGroupList');

        // route to get asset custom field list along with its form group fields
        Route::get('api/asset-custom-field-list', 'App\Plugins\ServiceDesk\Controllers\FormGroup\SdFormController@getAssetCustomFieldList');

        /**
         * Cab
         */
        // get cab index blade page
        Route::get('cabs', ['as' => 'cabs.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Cab\ApiCabController@index']);

        // get create cab blade page
        Route::get('cabs/create', ['as' => 'cabs.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Cab\ApiCabController@create']);

        // get edit cab blade page
        Route::get('cabs/{cabId}/edit', ['as' => 'cabs.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Cab\ApiCabController@edit']);

        // api to create or update cab
        Route::post('api/cab', 'App\Http\Controllers\Admin\helpdesk\ApiApprovalWorkflowController@createUpdateApprovalWorkflow');

        // api to get cab based on cabId
        Route::get('api/cab/{cabId}', 'App\Http\Controllers\Admin\helpdesk\ApiApprovalWorkflowController@getApprovalWorkflow');

        // api to get cab list
        Route::get('api/cab', 'App\Http\Controllers\Admin\helpdesk\ApiApprovalWorkflowController@getApprovalWorkflowList');

        // api to delete cab based on cabOrLevelId and type (type could be workflow or level)
        Route::delete('api/cab/{cabOrLevelId}/{type}', 'App\Http\Controllers\Admin\helpdesk\ApiApprovalWorkflowController@deleteApprovalWorkflow');

        // apply cab approval
        Route::post('api/apply-cab-approval', 'App\Plugins\ServiceDesk\Controllers\Cab\ApiApproverController@applyApproval');

        // remove cab approval
        Route::delete('api/remove-cab-approval/{changeId}', 'App\Plugins\ServiceDesk\Controllers\Cab\ApiApproverController@removeApprovalWorkflow');

        Route::get('api/change-approval-status/{changeId}', 'App\Plugins\ServiceDesk\Controllers\Cab\ApiApproverController@getChangeApprovalStatus');

        Route::post('api/approval-action-without-hash/{changeId}', 'App\Plugins\ServiceDesk\Controllers\Cab\ApiApproverController@approvalActionByChangeId');

        /**
         * Announcement
         */
        // Route for announcement blade page
        Route::get('announcement',['as'=>'announcement','uses'=>'App\Plugins\ServiceDesk\Controllers\Announcement\ApiAnnouncementController@announcementPage']);
        
        // Route to make announcement to organization or department members
        Route::post('api/announcement',['as'=>'announcement','uses'=>'App\Plugins\ServiceDesk\Controllers\Announcement\ApiAnnouncementController@makeAnnouncement']);


        /**
         * Reports
         */

        Route::get('reports/agent/assets',['as'=>'service.asset.agent.report','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@agentAssetReport']);
        Route::get('reports/department/assets',['as'=>'service.asset.department.report','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@departmentAssetReport']);
        Route::get('reports/product/assets',['as'=>'service.asset.product.report','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@productAssetReport']);
        Route::get('reports/location/assets',['as'=>'service.asset.location.report','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@locationAssetReport']);
        Route::get('reports/type/assets',['as'=>'service.asset.type.report','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@assetTypeAssetReport']);
        // route to view report list blade page
        Route::get('reports/{reporttype}',['as' => 'servicedesk.report.list','uses' => 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@reportIndex']);
        // route to create report blade page
        Route::get('reports/{reportType}/create', ['as' => 'servicedesk.report.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@createReport']);
        // route to edit report blade page
        Route::get('reports/{reportType}/edit/{reportFilterId}', ['as' => 'servicedesk.report.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@editReport']);
        // route to view report blade page
        Route::get('reports/{reportType}/{reportFilterId}', ['as' => 'servicedesk.report.view', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@viewReport']);
        // create reportFilter api
        Route::post('api/reports/create','App\Plugins\ServiceDesk\Controllers\Report\ReportController@createUpdateReportFilter');
        // report list api
        Route::get('api/reports','App\Plugins\ServiceDesk\Controllers\Report\ReportController@getReportFilterList');
        // edit report api based on reportFilter id
        Route::get('api/reports/{reportFilterId}','App\Plugins\ServiceDesk\Controllers\Report\ReportController@getReportFilter');
        // delete report api based on reportFilter id
        Route::delete('api/reports/{reportFilterId}','App\Plugins\ServiceDesk\Controllers\Report\ReportController@deleteReportFilter');

        Route::get('export', 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@export');
        Route::get('api/export/excel', 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@exportReportToExcel');
        Route::get('api/export/csv', 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@exportReportToCsv');




        /**
         * Report Api
         */

        Route::get('reports/agent/assets/api',['as'=>'service.asset.agent.report.api','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@agentAssetReportApi']);
        Route::get('reports/department/assets/api',['as'=>'service.asset.deparment.report.api','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@departmentAssetReportApi']);
        Route::get('reports/product/assets/api',['as'=>'service.asset.product.report.api','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@productAssetReportApi']);
        Route::get('reports/location/assets/api',['as'=>'service.asset.product.report.api','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@locationAssetReportApi']);
        Route::get('reports/asset-type/assets/api',['as'=>'service.asset.type.report.api','uses'=>'App\Plugins\ServiceDesk\Controllers\Report\AssetReport@assetTypeAssetReportApi']);


    });
    /**
     * Agent module
     */
    Route::group(['middleware' => 'role.agent'], function() {
        /**
         * Asset Managing Module
         */
        Route::get('assets', ['as' => 'service-desk.asset.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@index']);
        Route::get('assets/create', ['as' => 'service-desk.asset.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@create']);

        Route::post('assets/post/create', ['as' => 'service-desk.post.asset', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@handleCreate']);

        Route::get('assets/{id}/edit', ['as' => 'service-desk.asset.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@edit']);
        Route::get('assets/{id}/editapi/{asset_type?}', ['as' => 'service-desk.asset.editapi', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@editapi']);

        Route::post('assets/{id}', ['as' => 'service-desk.asset.postedit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@handleEdit']);

        Route::get('assets/{id}/delete', ['as' => 'service-desk.asset.delet', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@assetHandledelete']);
        Route::get('get-assets', ['as' => 'service-desk.asset.get', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAsset']);
        Route::get('assets/tag', ['as' => 'service-desk.asset.tag', 'uses' => 'App\Plugins\ServiceDesk\Controllers\assets\AssetController@search']);
        Route::post('attach-asset/ticket', ['as' => 'service-desk.post.asset.tag', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@attachAssetToTicket']);
        Route::post('assets/assettype', ['as' => 'service-desk.post.asset.types', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@assetType']);
        Route::get('asset-type/{id?}', ['as' => 'service-desk.post.asset.type', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAssetType']);
        Route::get('attach-assets/contract', ['as' => 'service-desk.filtered.asset.type', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAssetsForAttachAssets']);
        Route::get('asset/detach/{ticketid}/{assetid}', ['as' => 'service-desk.post.asset.detach', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@detach']);
        Route::get('assets/{id}/show', ['as' => 'service-desk.asset.show', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@show']);
        Route::get('assets/requesters', ['as' => 'asset.requesters.ajax', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@ajaxRequestTable']);
        Route::get('assets/export', ['as' => 'asset.export', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@export']);

        Route::post('assets/post/export', ['as' => 'asset.export.post', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@exportAsset']);

         Route::post('assets/uploadfile/delete/{id}', ['as' => 'assets.uploadfile.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@deleteUploadfile']);

         Route::get('assets/uploadfile/download/{filename}', ['as' => 'assets.uploadfile.download', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@downloadDocs']);
         Route::get('get/assetlist',['as'=>'get.allasset','uses'=>'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAllAsset']);
         Route::get('get/filterd/asset',['as'=>'get.filtered.asset','uses'=>'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAssetsBasedOnContract']);
         Route::get('attached/assets/ticket/{ticketid}',['as'=>'attached.assets.ticket','uses'=>'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAttachedAssetsBasedTicket']);
         Route::get('assets/organization/{orgid}', ['as' => 'get.assets.organization', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAttachedAssetsBasedOrganization']);
         Route::get('assets/user/{userid}', ['as' => 'get.assets.user', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Assets\AssetController@getAttachedAssetsBasedUser']);

        // Route to get asset list
        Route::get('api/asset-list',
            'App\Plugins\ServiceDesk\Controllers\Assets\AssetListController@getAssetList');

        // Route to create update asset
        Route::post('api/asset',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@createUpdateAsset');

        // Route to edit asset based on asset(asset id)
        Route::get('api/edit-asset/{asset}',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@editAsset');

        // Route to get specific asset details based on asset(asset id)
        Route::get('api/asset/{asset}',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@getAsset');

        // Route to get asset relation data based on Id
        Route::get('api/asset-relation/{assetId}',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@assetRelation');

        // Route to detach asset with ticket
        Route::delete('api/detach-asset/{ticketId}/{assetId}',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@detachAsset');

        // Route to attach ticket based on asset ids and ticket id
        Route::post('api/ticket-attach-assets',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@attachAssets');

        // Route to delete asset based on asset {assetId}
        Route::delete('api/asset-delete/{asset}',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@deleteAsset')->middleware('sd-permissions');

        // Route to delete asset based on asset_id
        Route::get('api/asset-contract/{asset_id}',
            'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@getContractBasedOnAssetApi');

        // Route to attach services to asset(assetId)
        Route::post('api/attach-asset-services/{asset}', 'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@attachServicesToAsset');

        // Route to detachServices to asset(assetId)
        Route::delete('api/detach-asset-services/{asset}', 'App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@detachServicesFromAsset');

        // get asset activity log based on asset
        Route::get('api/asset-log/{asset}','App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController@getAssetActivityLog');

        // get asset action list based on asset (assetId)
        Route::get('api/asset-action/{asset}','App\Plugins\ServiceDesk\Controllers\Assets\AssetActionOptionsController@getAssetActionList');

        /**
         * Route to dependency
         */
        Route::get('api/dependency/{type}', 'App\Plugins\ServiceDesk\Controllers\Common\Dependency\SdDependencyController@handle');



        /**
         * Release Managing Module
         */
        Route::get('releases', ['as' => 'service-desk.releases.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@releaseIndexPage']);
        Route::get('releases/create', ['as' => 'service-desk.releases.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@releaseCreatePage']);
        Route::get('releases/{releaseId}/edit', ['as' => 'service-desk.releases.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@releaseEditPage']);
        Route::get('releases/{id}/delete', ['as' => 'service-desk.releases.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\RelesesController@releasesHandledelete']);
        Route::get('releases/{id}/show', ['as' => 'service-desk.releases.view', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\RelesesController@view']);
        Route::get('get-releases', ['as' => 'service-desk.releases.get', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\RelesesController@getReleases']);
        Route::get('releases/{id}/complete', ['as' => 'service-desk.releases.view', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\RelesesController@complete']);
        Route::post('releases/uploadfile/delete/{id}', ['as' => 'releases.uploadfile.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\RelesesController@deleteUploadfile']);
        Route::post('asset/attach/releases', ['as' => 'asset.attach.releases', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Releses\RelesesController@attachAssetToRelease']);

        // Route to create update release
        Route::post('api/release',
            'App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@createUpdateRelease');
        // Route to get release based on releaseId
        Route::get('api/release/{release}',
            'App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@getRelease');
        // attach assets to release based on releaseId and asset ids (asset ids is passed in request)
        Route::post('api/release/attach-assets/{release}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@attachAssets');
        // detach asset attached to release based on releaseId and asset id
        
        // Route to get Release List
        Route::get('api/release-list', 'App\Plugins\ServiceDesk\Controllers\Releses\ReleaseListController@getReleaseList');

        // Route to delete a release based on release(releaseId)
        Route::delete('api/release/{release}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@deleteRelease');

        //  detach assets to release based on releaseId and assetId
        Route::delete('api/release/detach-asset/{release}/{asset}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@detachAsset');

        // mark release as completed based on releaseId
        Route::post('api/complete-release/{release}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@markReleaseAsCompleted');

        // attach change to release based on releaseId and changeId
        Route::post('api/release/attach-change/{release}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@attachChanges');

        // detach change to release based on releaseId and changeId
        Route::delete('api/release/detach-change/{release}/{change}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@detachChanges')->middleware('sd-permissions');
        
        // Route to get release activity log
        Route::get('api/release-activitylog/{release}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@getReleaseActivityLog');

        //Route to get release planning popup details
        Route::get('api/release-planning/{releaseId}','App\Plugins\ServiceDesk\Controllers\Releses\ApiReleaseController@planningPopups');

        // Route to get action buttons details for release view
        Route::get('api/release-action/{release}','App\Plugins\ServiceDesk\Controllers\Releses\ReleaseActionOptionsController@getReleaseActionList');
        /**
         * Changes Managing Module
         */
        Route::get('changes', ['as' => 'service-desk.changes.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@changesIndexPage']);
        Route::post('changes/create', ['as' => 'service-desk.post.changes', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@changeshandleCreate']);
        Route::patch('changes/{id}', ['as' => 'service-desk.changes.postedit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@changeshandleEdit']);
        Route::get('changes/{id}/delete', ['as' => 'service-desk.changes.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@changesHandledelete']);
        Route::get('get-changes', ['as' => 'service-desk.changes.get', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@getChanges']);
        Route::get('changes/{changeId}/show', ['as' => 'service-desk.changes.show', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@changeViewPage']);
        Route::get('changes/{id}/close', ['as' => 'change.close', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@close']);
        Route::post('changes/release/{id}', ['as' => 'change.release.post', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@attachNewRelease']);
        Route::get('changes/release', ['as' => 'change.release', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@getReleases']);
        Route::post('changes/release/attach/{id}', ['as' => 'change.release.attach', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@attachExistingRelease']);
        Route::get('changes/{changeid}/detach', ['as' => 'change.release.detach', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@detachRelease']);
        Route::post('changes/uploadfile/delete/{id}', ['as' => 'changes.uploadfile.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@deleteUploadfile']);
        Route::post('asset/attach/changes', ['as' => 'asset.attach.changes', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@attachAssetToChange']);

        // change create blade page
        Route::get('changes/create', ['as' => 'service-desk.changes.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@changeCreatePage']);

        // change edit blade page based on change id
        Route::get('changes/{changeId}/edit', ['as' => 'service-desk.changes.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@changeEditPage']);
        // based on cab level get it's approvers and it's status
        Route::get('api/cab-level/{levelId}', 'App\Plugins\ServiceDesk\Controllers\Changes\ChangesController@getCabLevelApprovers');
        
        // create or update change
        Route::post('api/change','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@createUpdateChange');
        // edit change based on change id
        Route::get('api/change/{changeId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@getChange');

        // Route to get change list
        Route::get('api/change-list','App\Plugins\ServiceDesk\Controllers\Changes\ChangeListController@getChangeList');

        // Route to delete change based on change(changeId)
        Route::delete('api/change/{change}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@deleteChange')->middleware('sd-permissions');

        // attach assets to change based on changeId and asset ids (asset ids is passed in request)
        Route::post('api/change/attach-assets/{changeId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@attachAssets');

        // detach asset attached to change based on changeId and asset id
        Route::delete('api/change/detach-asset/{changeId}/{assetId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@detachAsset');

        // attach release to change based on changeId and releaseId
        Route::post('api/change/attach-release/{changeId}/{releaseId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@attachRelease');

        // detach release attached to change based on changeId
        Route::delete('api/change/detach-release/{changeId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@detachRelease');

        // update change status to closed based on change id
        Route::post('api/change-close/{changeId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@updateChangeStatusToClose');

        // get change activity log based on change id
        Route::get('api/change-log/{changeId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@getChangeActivityLog');

        // get change action list based on changeId
        Route::get('api/change-action/{changeId}','App\Plugins\ServiceDesk\Controllers\Changes\ChangeActionOptionsController@getChangeActionList');

        // get planning popups details
        Route::get('api/change-planning/{changeId}','App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@planningPopups');

        // Route to attach change to ticket
        Route::post('api/attach-change/ticket', 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@attachChangeToTicket');

        // Route to detach change from ticket
        Route::delete('api/detach-change/ticket', 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@detachChangeFromTicket');

        // Route to get associated changes attached to ticket
        Route::get('api/attached-change/ticket', 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@associatedChangesLinkedToTicket');

        // Route to get associated changes attached to ticket
        Route::get('api/attached-ticket/change', 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@associatedTicketsLinkedToChange');

        // Route to attach ticket to change
        Route::post('api/attach-ticket/change', 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@attachTicketToChange');

        // Route to detach ticket from change
        Route::delete('api/detach-ticket/change', 'App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController@detachTicketFromChange');

        /**
         * Problem Managing Module
         */
        Route::get('problems', ['as' => 'service-desk.problem.index', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@problemsIndexPage']);
        Route::get('problem/create', ['as' => 'service-desk.problem.create', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@problemCreatePage']);
        Route::get('problem/{id}/edit', ['as' => 'service-desk.problem.edit', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@problemEditPage']);
        Route::get('problem/{id}/delete', ['as' => 'service-desk.problem.postdelete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@delete']);
        Route::get('get-problems', ['as' => 'service-desk.problem.get', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@getproblems']);
        Route::post('attach-problem/ticket/new', ['as' => 'attach.problem.ticket.new', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@attachNewProblemToTicket']);
        Route::post('attach-problem/ticket/existing', ['as' => 'attach.problem.ticket.existing', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@attachExistingProblemToTicket']);
        Route::get('problems/attach/existing', ['as' => 'attach.problem.ticket.existing.ajax', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@getAttachableProblem']);
        Route::get('problem/detach/{ticketid}/{problemid}', ['as' => 'detach.problem.ticket', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@detach']);
        Route::get('problem/{id}/show', ['as' => 'show.problem', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@show']);
        Route::get('problem/{id}/close', ['as' => 'problem.close', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@close']);
        Route::post('problem/change/{id}', ['as' => 'problem.change.post', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@attachNewChange']);
        Route::get('problem/getChanges', ['as' => 'problem.getChanges', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@getChanges']);
        Route::post('problem/change/attach/{id}', ['as' => 'problem.change.attach', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@attachExistingChange']);
        Route::get('problem/{problemid}/detach', ['as' => 'problem.change.detach', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@detachChange']);
        Route::post('uploadfile/delete/{id}', ['as' => 'uploadfile.delete', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@deleteUploadfile']);
        Route::post('asset/attach/problem', ['as' => 'asset.attach.problem', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@attachAssetToProblem']);
        Route::get('get/tickets/problem/{problemid}', ['as' => 'associated.tickets.problem', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Problem\ProblemController@getAssociatedTicketsBasedProblem']);

        // Route to get asset list
        Route::get('api/problem-list',
            'App\Plugins\ServiceDesk\Controllers\Problem\ProblemListController@getProblemList');

        Route::get('api/problem/identifer',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@identifier');

        // Route to create and update problem
        Route::post('api/problem',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@createUpdateProblem');

        // Route to edit problem based on problem id
        Route::get('api/problem/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@editProblem');

        // Route to get problem based on problem id
        Route::get('api/get-problem/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@getProblem');

        // Route to get assets based on problem id
        Route::get('api/problem-assets/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@getAssets');

        // Route to get change based on problem id
        Route::get('api/problem-change/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@getChange');

        // Route to get tickets based on problem id
        Route::get('api/problem-tickets/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@getTickets');

        // Route to get attachment based on problem id
        Route::get('api/problem-attachment/{problemId}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@getAttachment');

        // Route to detach ticket based on problem id and ticket id
        Route::delete('api/problem-detach-ticket/{problem}/{ticket}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@detachTicket');

        // Route to detach asset based on problem id and asset id
        Route::delete('api/problem-detach-asset/{problem}/{asset}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@detachAsset');

        // Route to delete problem based on problem id
        Route::delete('api/problem-delete/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@deleteProblem')->middleware('sd-permissions');

        // Route to attach ticket based on problem id and ticket id
        Route::post('api/problem-attach-ticket/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@attachTicket');

        Route::post('api/problem-attach-ticket/{ticket}/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@attachTickets');

        // Route to attach change based on problem id and ticket id
        Route::post('api/problem-attach-change/{problem}/{change}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@attachChange');

        // Route to detach change based on problem id and ticket id
        Route::delete('api/problem-detach-change/{problem}/{change}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@detachChange');

        // Route to attach asset asset on problem id and ticket id
        Route::post('api/problem-attach-assets/{problem}/',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@attachAsset');

        // Route update problem status to close based on problemId
        Route::post('api/problem-close/{problem}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@updateProblemStatusToClose');

        // Route to get activity log of problem based on problemId
        Route::get('api/problem-log/{problemId}',
            'App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@getProblemActivityLog');
        // get problem action list based on problemId
        Route::get('api/problem-action/{problem}','App\Plugins\ServiceDesk\Controllers\Problem\ProblemActionOptionsController@getProblemActionList');

        Route::get('api/problem-planning/{problemId}','App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController@planningPopups');

        /**
         * Common
         */

        Route::get('products/{id}/show', ['as' => 'service-desk.products.show', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Products\ProductsController@show']);
        Route::get('contracts/{id}/show', ['as' => 'service-desk.contract.show', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@show']);
        Route::post('general/{id}/{table}',['as'=>'general.post','uses'=>'App\Plugins\ServiceDesk\Controllers\InterfaceController@generalInfo']);
        Route::get('delete/{attachid}/{owner}/attachment',['as'=>'attach.delete','uses'=>'App\Plugins\ServiceDesk\Controllers\InterfaceController@deleteAttachments']);
        Route::get('download/{attachid}/{owner}/attachment',['as'=>'attach.delete','uses'=>'App\Plugins\ServiceDesk\Controllers\InterfaceController@downloadAttachments']);
        Route::get('general/{owner}/{identifier}/delete',['as'=>'attach.delete','uses'=>'App\Plugins\ServiceDesk\Controllers\InterfaceController@deleteGeneralByIdentifier']);
        Route::get('vendor/api',[ 'as'=>'servicedesk.vendor.api.',
                                  'uses'=>'App\Plugins\ServiceDesk\Controllers\Contract\ContractController@vendorApi']);
        Route::get('attached/assets/{ticketid}', ['as' => 'service-desk.getasset.ticket', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Library\UtilityController@getAssetsByTicketid']);
        Route::get('attached/problem/{ticketid}', ['as' => 'service-desk.getproblem.ticket', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Library\UtilityController@getProblemsByTicketid']);
        Route::get('detach/asset/{assetid}/{typeid}/{type}', ['as' => 'service-desk.asset.detach', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Library\UtilityController@commonAssetDetach']);
        Route::get('get/attached/assets/{typeid}/{type}', ['as' => 'get.attached.assetslist', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Library\UtilityController@getListOfAttachedAssets']);

        //To get all servicedesk permissions lists
        Route::get('get/permissions', ['as' => 'all.servicedesk.permissions', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Permission\PermissionController@permissions']);
        //To get all servicedesk permissions
        Route::get('get/permission/api', 'App\Plugins\ServiceDesk\Controllers\Permission\PermissionController@getActionList');

        // create update popup data for change based on modelId, tableName and request parameters
        Route::post('api/general-popup/{modelId}/{tableName}','App\Plugins\ServiceDesk\Controllers\Common\SdGeneralController@createUpdatePopup');

        // edit popup data for change based on modelId, tableName and identifier parameters
        Route::get('api/general-popup/{modelId}/{tableName}/{identifier}','App\Plugins\ServiceDesk\Controllers\Common\SdGeneralController@editPopup');

        // delete popup data for change based on modelId, tableName and identifier parameters
        Route::delete('api/delete/general-popup/{modelId}/{tableName}/{identifier}','App\Plugins\ServiceDesk\Controllers\Common\SdGeneralController@deletePopupDetails');

        // route to get ticket list
        Route::get('api/ticket-list', 'App\Plugins\ServiceDesk\Controllers\Ticket\TicketsListController@getTicketsList');

    });


});

        //Routes for copy old relation data
        Route::group(['prefix' => 'servicedesk'], function () {
        //For Copy values from ticket relation table to common ticket relation table
        Route::get('copy/ticket/relations', ['as' => 'copy.ticket.relations', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Library\UtilityController@TicketRelationToCommonTicketRelation']);
        //For Copy values from ticket relation table to common ticket relation table
        Route::get('copy/asset/relations', ['as' => 'copy.asset.relations', 'uses' => 'App\Plugins\ServiceDesk\Controllers\Library\UtilityController@AssetRelationToCommonAssetRelation']);
    });

    \Event::listen('agent-panel-scripts-dispatch', function() {
      echo "<script src=".bundleLink('js/serviceDesk.js')."></script>";
    });

    \Event::listen('admin-panel-scripts-dispatch', function() {
      echo "<script src=".bundleLink('js/serviceDesk.js')."></script>";
    });

     \Event::listen('client-panel-scripts-dispatch', function() {
      echo "<script src=".bundleLink('js/serviceDesk.js')."></script>";
    });

    Route::group(['prefix' => 'service-desk','middleware' => ['web']], function() {

        
        // // get change conversation based on hash for the cab approval
        Route::get('api/change-conversation/{hash}', 'App\Plugins\ServiceDesk\Controllers\Cab\ApiApproverController@getConversationByHash')->middleware('web');

        Route::post('api/approval-action/{hash}', 'App\Plugins\ServiceDesk\Controllers\Cab\ApiApproverController@approvalActionByHash');

        Route::post('api/approval-action/{hash}', 'App\Plugins\ServiceDesk\Controllers\Cab\ApiApproverController@approvalActionByHash');

        Route::get('reports', 'App\Plugins\ServiceDesk\Controllers\Report\ReportController@reports')->name("servicedesk.reports");

        Route::post('api/contract-approval/{hash}/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@contractApprovalActionByHash');

        Route::get('api/contract-details/{hash}/{contractId}', 'App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController@contractDetailsByHash');

        
    });
