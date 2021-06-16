<template>

  	<div v-if="actions" class="pull-right">

      <button v-if="actions.contract_expiry_reminder" class="btn btn-default" 
        @click="showReminder = true">
      
        <i class="fa fa-server"> </i> {{trans('contract_expiry_reminder')}}
      </button>

      <button v-if="actions.contract_asset_attach" class="btn btn-default" @click="showAssetModal = true">
      
        <i class="fa fa-server"> </i> {{trans('attach_asset')}}
      </button>

    	<a  v-if="actions.contract_edit" class="btn btn-default" 
        :href="basePath()+'/service-desk/contracts/'+contract.id+'/edit'">
     	
     		<i class="fa fa-edit"> </i> {{trans('edit')}}
    	</a>

      <button v-if="actions.contract_approve" class="btn btn-default" @click="commonMethod('approve')">
      
        <i class="fa fa-check"> </i> {{trans('approve')}}
      </button>

      <button v-if="actions.contract_approve" class="btn btn-default" @click="commonMethod('reject')">
      
        <i class="fa fa-ban"> </i> {{trans('reject')}}
      </button>

      <button v-if="actions.contract_renew" class="btn btn-default" @click="extendAndRenew('renew')">
      
        <i class="fa fa-refresh"> </i> {{trans('renew')}}
      </button>

      <button v-if="actions.contract_extend" class="btn btn-default" @click="extendAndRenew('extend')">
      
        <i class="fa fa-expand"> </i> {{trans('extend')}}
      </button>

      <button v-if="actions.contract_terminate" class="btn btn-default" @click="commonMethod('terminate')">
      
        <i class="fa fa-times"> </i> {{trans('terminate')}}
      </button>

      <button v-if="actions.contract_delete" class="btn btn-default" @click="showDeleteModal = true">
      
        <i class="fa fa-trash"> </i> {{trans('delete')}}
      </button>

      <transition name="modal">

        <contract-update-modal v-if="showUpdateModal" :onClose="onClose" :showModal="showUpdateModal" 
          :contract="contract" :updateData="updateContractDetails" :type="actionType">

        </contract-update-modal>
      </transition>

      <transition name="modal">

        <contract-reminder-modal v-if="showReminder" :onClose="onClose" :showModal="showReminder" 
          :contract="contract" :updateData="updateContractDetails">

        </contract-reminder-modal>
      </transition>

      <transition name="modal">

        <contract-assets  v-if="showAssetModal" :onClose="onClose" :showModal="showAssetModal" :contractId="contract.id">

        </contract-assets>
      </transition>

      <transition name="modal">

        <contract-action-modal v-if="showCommonModal" :onClose="onClose" :showModal="showCommonModal" 
          :contractId="contract.id" :type="actionType" :updateData="updateContractDetails">

        </contract-action-modal>
      </transition>

      <transition name="modal">

        <delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
          alertComponentName="contract-view" :deleteUrl="'/service-desk/api/contract/' + contract.id" 
          redirectUrl="/service-desk/contracts">

        </delete-modal>
      </transition>
  	</div>
</template>

<script>
  
  import axios from 'axios'
  
  export default {

    name : 'contract-actions',

    description : 'Contract actions component',

    props : {

      contract : { type : Object, default : ()=> {}},

      updateContractDetails : { type : Function, default : ()=>{}}
    },

    data() {

      return {

        actions : '',

      	showUpdateModal : false,

        showAssetModal : false,

        showCommonModal : false,

        actionType : '',

        showReminder : false,

        showDeleteModal : false,
      }
    },

    beforeMount() {

      this.getActions();
    },

    methods : {

      getActions() {

         axios.get('/service-desk/api/contract-action/'+this.contract.id).then(res=>{

          this.actions = res.data.data.actions;

          window.eventHub.$emit('updateContractTableActions',this.actions);

        }).catch(error=>{

          this.actions = '';
        })
      },

      extendAndRenew(type){

        this.actionType = type;

        this.showUpdateModal = true;
      },

      onClose(){

        this.showUpdateModal = false;
        	
        this.showAssetModal = false;

        this.showCommonModal = false;

        this.showReminder = false;

        this.showDeleteModal = false;

		    this.$store.dispatch('unsetValidationError');
		  },

       commonMethod(type){

        this.showCommonModal = true;

        this.actionType = type;
      },
    },

    components : {

      'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),

      'contract-update-modal': require('./Child/ContractUpdateModal'),

      'contract-assets': require('./Child/ContractAssetModal'),

      'contract-action-modal': require('./Child/ContractActionModal'),

      'contract-reminder-modal': require('./Child/ContractReminderModal'),
    }
  };
</script>

<style scoped>

  #more_actions {
    right: 0 !important;
    left: unset !important;
  }
</style>