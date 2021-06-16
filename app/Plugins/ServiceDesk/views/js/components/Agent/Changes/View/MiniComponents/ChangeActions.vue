<template>

  <div class="pull-right">

    <div class="btn-group">

      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

       <i class="fa fa-refresh"> </i> {{lang('planning')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu">

        <li><a href="javascript:;" @click="updateMethod('reason')">Reason for Change</a></li>

        <li><a href="javascript:;" @click="updateMethod('impact')">Impact</a></li>

        <li><a href="javascript:;" @click="updateMethod('rollout-plan')">Rollout Plan</a></li>

        <li><a href="javascript:;" @click="updateMethod('backout-plan')">Backout Plan</a></li>
      </ul>
    </div>

    <div class="btn-group">

      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

       <i class="fa fa-ticket"> </i> {{lang('associate')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu">

        <li>
          <a href="javascript:;" @click="associateMethod('initiating')">{{lang('tickets_initiating_this_change')}}
          </a>
        </li>

        <li>
          <a href="javascript:;" @click="associateMethod('initiated')">{{lang('tickets_caused_due_to_this_change')}}
          </a>
        </li>        
      </ul>
    </div>

    <button v-if="actions.allowed_enforce_cab" class="btn btn-default" @click="showApplyCab = true">
      <i class="fa fa-plus"> </i> {{lang('apply_cab')}}
    </button>

    <button class="btn btn-default" @click="showAttach = true">
     <i class="fa fa-server"> </i> {{lang('attach_asset')}}
    </button>

    <div class="btn-group" v-if="actions">

      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

        <i class="fa fa-link"> </i> {{lang('release')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu">

        <template v-if="actions.change_release_detach">

          <li v-if="change.attach_releases.length > 0">
            <a href="javascript:;" @click="showReleaseDetach = true"> {{lang('detach')}} 
              
              <b :title="change.attach_releases[0].subject">{{subString(change.attach_releases[0].subject,10)}}</b>
            </a>
          </li>
        </template>

        <template v-if="actions.change_release_attach">
          
          <li><a href="javascript:;" @click="showReleaseCreate = true"> {{lang('new_release')}}</a></li>

          <li><a href="javascript:;" @click="showReleaseExists = true"> {{lang('existing_release')}}</a></li>
        </template>
      </ul>
    </div>

    <div class="btn-group" v-if="actions">

      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

        <i class="fa fa-cog"> </i> {{lang('more')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu" id="more_actions">

        <li v-if="actions.change_edit">

          <a :href="basePath()+'/service-desk/changes/'+change.id+'/edit'">
            {{lang('edit')}}
          </a>
        </li>

        <li v-if="actions.change_delete">

          <a href="javascript:;" @click="showDeleteModal = true">{{lang('delete')}}</a>
        </li>

        <li v-if="actions.change_close">
        
          <a href="javascript:;" @click="showChangeStatus = true">{{lang('close')}}</a>
        </li>
      </ul>
    </div>

    <transition name="modal">

		 <delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
        alertComponentName="changes-view" :deleteUrl="'/service-desk/api/change/' + change.id" 
        redirectUrl="/service-desk/changes">

     </delete-modal>
		</transition>

    <transition name="modal">

     <release-create-modal v-if="showReleaseCreate" :onClose="onClose" :showModal="showReleaseCreate"
     :changeId="change.id">

     </release-create-modal>
    </transition>

    <transition name="modal">

     <release-add-modal v-if="showReleaseExists" :onClose="onClose" :showModal="showReleaseExists" 
     :changeId="change.id">

     </release-add-modal>
    </transition>

     <transition name="modal">

       <cab-add-modal v-if="showApplyCab" :onClose="onClose" :showModal="showApplyCab" 
       :changeId="change.id">

       </cab-add-modal>
      </transition>

     <transition name="modal">

     <change-status-modal v-if="showChangeStatus" :onClose="onClose" :showModal="showChangeStatus" 
     :changeId="change.id">

     </change-status-modal>
    </transition>

    <transition  name="modal">

      <change-release-detach v-if="showReleaseDetach" :onClose="onClose" :showModal="showReleaseDetach" 
        :releaseId="change.attach_releases[0].id" :changeId="change.id" alertCompName="changes-view">

      </change-release-detach>
    </transition>

     <transition  name="modal">

      <change-update-modal v-if="showChangeUpdate" :onClose="onClose" :showModal="showChangeUpdate" 
        :identifier="identifier" :changeId="change.id" alertCompName="changes-view">

      </change-update-modal>
    </transition>

    <transition  name="modal">

      <change-associate-modal v-if="showAssociateModal" :onClose="onClose" :showModal="showAssociateModal" 
        :associate="associate" :changeId="change.id" alertCompName="changes-view">

      </change-associate-modal>
    </transition>

    <transition name="modal">

      <change-assets  v-if="showAttach" :onClose="onClose" :showModal="showAttach" :changeId="change.id">

      </change-assets>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios'

  export default {

    name : 'changes-actions',

    description : 'Changes actions component',

    props : {

      change : { type : Object, default : ()=> {}}
    },

    data() {

      return {

        showDeleteModal : false,

        showReleaseCreate : false,

        showReleaseExists : false,

        showReleaseDetach : false,

        showChangeStatus : false,

        showChangeUpdate : false,

        showApplyCab : false,

        showAttach : false,

        identifier : '',

        actions : '',

        showAssociateModal : false,

        associate : ''
      }
    },

    beforeMount(){

      this.getActions();
    },

    created() {

      window.eventHub.$on('cabActionPerformed', this.getActions);
    },

    methods : {

      getActions(){

        axios.get('/service-desk/api/change-action/'+this.change.id).then(res=>{

          this.actions = res.data.data.actions;

        }).catch(error=>{

          this.actions = '';
        })
      },

      subString(value,length = 15){

        return getSubStringValue(value,length)
      },

      updateMethod(identifier){

        this.showChangeUpdate = true;

        this.identifier = identifier;
      },

      associateMethod(associate){

        this.showAssociateModal = true;

        this.associate = associate;
      },

      onClose(){

        this.showDeleteModal = false;

        this.showReleaseCreate = false;
		    
        this.showReleaseExists = false;
        
        this.showReleaseDetach = false;

        this.showChangeStatus = false;

        this.showChangeUpdate = false;

        this.showApplyCab = false;

        this.showAttach = false;

        this.showAssociateModal = false;

        this.getActions();

		    this.$store.dispatch('unsetValidationError');
		  },
    },

    components : {

      'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),

      'release-add-modal': require('./ReleaseAddModal'),
  
      'release-create-modal': require('./ReleaseCreateModal'),

      'change-release-detach': require('./ChangeDetach'),

      'change-status-modal' : require('./ChangeStatusClose'),

      'change-update-modal' : require('./ChangeUpdateModal'),

      'change-associate-modal' : require('./ChangeAssociateModal'),

      'cab-add-modal' : require('./ChangeCabModal'),

      'change-assets' : require('./ChangeAssets'),
    }
  };
</script>

<style scoped>

  #more_actions {
    right: 0 !important;
    left: unset !important;
  }
</style>