<template>

  <div id="sd-ticket-actions">

    <attach-problem v-if="showProblem" :data="data" :actions="ticketActions"></attach-problem>

    <button id="detach-problem" @click="modalMethod('detach_modal')" v-if="ticketActions.show_detach_problem" 
    class="btn btn-default btn-sm">

      <i class="fa fa-unlink"></i> {{lang('detach_problem')}}

    </button>
    
    <button id="attach-assets" v-if="ticketActions.show_attach_assets" @click="modalMethod('attach_asset')" 
    class="btn btn-default btn-sm">

      <i class="fa fa-server"></i> {{lang('attach_assets')}}

    </button>

    <attach-change v-if="associateChange" :data="data" :actions="ticketActions"></attach-change>

    <transition name="modal">
      
      <component  v-if="showModal" v-bind:is="currentModalComponent" :onClose="onClose" :showModal="showModal" 
      from="modal" :problemId="problemId" :data="data" alert="timeline">
        
      </component>

    </transition>

  </div>

</template>

<script>

  import {mapGetters} from 'vuex';

  export default {

      name: 'sd-ticket-actions',

      description : 'Contains ticket actions on timline page, specific to Service Desk',

      props :{
       
        data: {type: String|Object, required: true}
      
      },

      data(){
      
        return {
      
          showModal : false,

          modals:['attach_asset','detach_modal'],
        
          currentModal : ''
      
        }
      
      },

      watch : {

        currentModal(newValue,oldValue){
          
          return newValue
        
        }
      
      },

      computed : {

        currentModalComponent(){
        
          return this.currentModal === 'attach_asset' ? 'attach-asset-modal' : this.currentModal === 'detach_modal' ? 'detach-modal' : ''
        
        },
        
        problemId(){

          if(JSON.parse(this.data).associated_problem){

            return JSON.parse(this.data).associated_problem.id;
            
          }
        },

        showProblem() {

          return (this.ticketActions.show_attach_new_problem || this.ticketActions.show_attach_existing_problem);
        },

        associateChange(){

          return this.ticketActions.attach_change_initiated || this.ticketActions.attach_change_initiating
        },

        ...mapGetters({ticketActions : 'getTicketActions'}),
      
      },

      methods : {
        
        modalMethod(modal){

          this.showModal = true;

          this.currentModal = modal;

        },

        onClose(){
          
            this.showModal = false;
      
            this.$store.dispatch('unsetValidationError');
        },
      },

      components : {

        'attach-problem' : require('./MiniComponents/AttachProblem.vue'),

        'detach-modal': require('../MiniComponents/DetachModal'),

        'attach-asset-modal': require('../Asset/AttachAsset'),
        
        'attach-change' : require('./Changes/AttachChange.vue'),
      }

  };

</script>

<style scoped>
  
  #attach-assets{
        margin-left: 1px !important;
  }

  #detach-problem{
        margin-left: 0px !important;
  }
</style>
