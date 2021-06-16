let bootstrap = require('bootstrap');

require('../css/activityLog.css');

import Vue from 'vue';

import Router from 'vue-router'

import {store} from 'store'

import CKEditor from '@ckeditor/ckeditor5-vue';

import vSelect from 'vue-select'
Vue.component('v-select', vSelect)

Vue.use( CKEditor );

bootstrap.injectComponentIntoView('ticket-associated-list', require('./components/Ticket/TicketAssociatedList.vue'),'user-box-mounted','user-view-table');

bootstrap.injectComponentIntoView('associated-problem', require('./components/Problem/AssociatedProblem.vue'),'timeline-meta-box-mounted','timeline-meta-list-box');

bootstrap.injectComponentIntoView('sd-ticket-actions', require('./components/Ticket/SdTicketActions.vue'),'timeline-action-bar-mounted','timeline-action-bar');

//For asociated Assets, Contracts, Changes Tab
bootstrap.injectComponentIntoView('ticket-associated-list', require('./components/Ticket/TicketAssociatedList.vue'),'ticket-tab-mounted','timeline-tab');

bootstrap.injectComponentIntoView('ticket-associated-list', require('./components/Ticket/TicketAssociatedList.vue'),'org-page-mounted','org-page-table');
//

// injecting sd-agent-permission component into permission-list-box at agent CU page on permission-list-box-mounted event
bootstrap.injectComponentIntoView('sd-agent-permission', require("./components/Admin/Staff/Agent/SdAgentPermission.vue"), "permission-list-box-mounted", "permission-list-box");


bootstrap.injectComponentIntoView('create-form-group-button', require('./components/Common/CreateFormGroupButton.vue'),'create-form-group-button-mounted', 'form-group-create-button');

// bootstrap.injectComponentIntoView('ticket-actions', require('./components/Ticket/TicketActions.vue'),'timeline-action-bar-mounted','timeline-action-bar');

// NOTE: Adding change conversation page route to client panel routes

import router from 'router';

import ChangeConversation from './components/Client/Changes/ChangeConversation.vue';

import ContractConversation from './components/Client/Contract/ContractConversation.vue';

router.addRoutes([
      {
            path: '/change-approval/:id',
            component: ChangeConversation,
            name: 'ChangeConversation',
            meta: { breadcrumb: [{text:'ChangeConversation'}]}
      },

      {
            path: '/service-desk/contract-approval/:hash/:id',
            component: ContractConversation,
            name: 'ContractConversation',
            meta: { breadcrumb: [{text:'Contract Approval'}]}
      },
]);

// ==========================================================================================

store.dispatch('deleteUser');
store.dispatch('updateUser');

new Vue({
    el: '#app-sevicedesk',
    store: store,
    components: {
                  
                  'problem' : require('./components/Problem/Problem.vue'),

      'asset-index' : require('./components/Asset/AssetIndex.vue'),

      'agent-asset-index' : require('./components/Report/AgentAssetIndex.vue'),

      'problem-index' : require('./components/Problem/ProblemIndex.vue'),

      'cab-index' : require('./components/Admin/Cab/CabIndex.vue'),

      'cab-create-edit' : require('./components/Admin/Cab/CabCreateEdit.vue'),

      'report-nested-filter' : require('./components/Report/ReportNestedFilter.vue'),

      'report-index' : require('./components/Report/ReportsIndex.vue'),

      'report-filter-view' : require('./components/Report/ReportView.vue'),

      'release-create-edit' : require('./components/Agent/Release/ReleaseCreateEdit.vue'),

      'releases-index' : require('./components/Agent/Release/ReleasesIndex.vue'),

      'release-view' : require('./components/Agent/Release/ReleaseView.vue'),

      'contract-type-index' : require('./components/Admin/ContractType/ContractTypeIndex.vue'),

      'contract-type-create-edit' : require('./components/Admin/ContractType/ContractTypeCreateEdit.vue'),

      'assetstypes-index' : require('./components/Admin/AssetsTypes/AssetTypeIndex.vue'),

      'assetstypes-create-edit' : require('./components/Admin/AssetsTypes/AssetTypeCreateEdit.vue'),



      // CHANGES
      //=======
      'changes-index' : require('./components/Agent/Changes/ChangesIndex.vue'),

      'changes-create-edit' : require('./components/Agent/Changes/ChangesCreateEdit.vue'),

      'changes-view' : require('./components/Agent/Changes/ChangesView.vue'),
      //===========================================
      
      'vendor-index' : require('./components/Admin/Vendor/VendorIndex.vue'),

      'vendor-create-edit' : require('./components/Admin/Vendor/VendorCreateEdit.vue'),

      'vendor-view' : require('./components/Admin/Vendor/VendorView.vue'),

      'procurement-type-index' : require('./components/Admin/ProcurementType/ProcurementTypeIndex.vue'),

      'procurement-type-create-edit' : require('./components/Admin/ProcurementType/ProcurementTypeCreateEdit.vue'),

      'license-type-index' : require('./components/Admin/LicenseType/LicenseTypeIndex.vue'),

      'license-type-create-edit' : require('./components/Admin/LicenseType/LicenseTypeCreateEdit.vue'),

      'announcement' : require('./components/Admin/Announcement/Announcement.vue'),

      //PRODUCTS

      'products-index' : require('./components/Admin/Products/ProductsIndex.vue'),      
      'product-create-edit' : require('./components/Admin/Products/ProductCreateEdit.vue'),   

      'product-view' : require('./components/Admin/Products/ProductView.vue'),

      'barcode-settings' : require('./components/Admin/Barcode/BarcodeSettings.vue'),

      //CONTRACTS
      'contracts-index' : require('./components/Agent/Contract/ContractsIndex.vue'),

      'barcode-settings' : require('./components/Admin/Barcode/BarcodeSettings.vue'),

      'problem-view' : require('./components/Problem/ProblemViewPage.vue'),

      'asset-form': require('./components/Asset/AssetForm.vue'),

      'asset-view': require('./components/Asset/AssetView.vue'),

      //ASSET STATUS
      
      'asset-status-index' : require('./components/Admin/AssetStatus/AssetStatusIndex.vue'),

      'asset-status-create-edit' : require('./components/Admin/AssetStatus/AssetStatusCreateEdit.vue'),

      'barcode-settings' : require('./components/Admin/Barcode/BarcodeSettings.vue'),

      //CONTRACTS
      
      'contract-create-edit' : require('./components/Agent/Contract/ContractCreateEdit.vue'),

      //CONTRACTS
      'contracts-index' : require('./components/Agent/Contract/ContractsIndex.vue'),

      'contract-view' : require('./components/Agent/Contract/ContractView.vue'),
    }
});
