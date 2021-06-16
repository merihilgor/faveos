<template>

  <div class="pull-right">

  		<template v-if="actions">
  		
  			<div class="btn-group">

		      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

		       <i class="fa fa-refresh"> </i> {{lang('planning')}} <span class="caret"></span>
		      </button>

		      <ul class="dropdown-menu">

		        <li><a href="javascript:;" @click="planningMethod('root-cause')">{{lang('root_cause')}}</a></li>

		        <li><a href="javascript:;" @click="planningMethod('impact')">{{lang('impact')}}</a></li>

		        <li><a href="javascript:;" @click="planningMethod('symptoms')">{{lang('symptoms')}}</a></li>

		        <li><a href="javascript:;" @click="planningMethod('solution')">{{lang('solution')}}</a></li>
		      </ul>
	    	</div>

    		<div class="btn-group">

		      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

		        <i class="fa fa-link"> </i> {{lang('associate')}} <span class="caret"></span>
		      </button>

		      <ul class="dropdown-menu">

            <li><a href="javascript:;" @click="showAttach = true"> <i class="fa fa-server"></i> {{lang('attach_asset')}}</a></li>

            <li><a href="javascript:;" @click="showAttachTicket = true">
              <i class="fa fa-ticket"></i> {{lang('attach_ticket')}}</a></li>

            <li><a> <i class="fa fa-refresh"></i> {{lang('change')}}</a></li>

		        <template v-if="actions.problem_change_detach">

		          <li v-if="!Array.isArray(problem.change)">
		            
		            <a href="javascript:;" @click="showDetachModal = true"> 

                  <span class="margin"> <i class="fa fa-circle-o"> </i>  {{lang('detach')}} 
		              
		              <b v-tooltip="problem.change.subject">{{subString(problem.change.subject,10)}}</b></span>
		            </a>
		          </li>
		        </template>

		        <template v-if="actions.problem_change_attach">
		          
		          <li>

                <a href="javascript:;" @click="problemChange('attach_new_change')"> 

                   <span class="margin"> <i class="fa fa-circle-o"> </i>  {{lang('attach_new_change')}}</span></a>
              </li>

		          <li>
                  
                  <a href="javascript:;" @click="problemChange('attach_existing_change')"> 
                     <span class="margin"> <i class="fa fa-circle-o"> </i>  {{lang('attach_existing_change')}}</span>
                   </a>
              </li>
		        </template>
		      </ul>
		   </div>

    		<div class="btn-group">

		      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

		        <i class="fa fa-cog"> </i> {{lang('more')}} <span class="caret"></span>
		      </button>

		      <ul class="dropdown-menu" id="more_actions">

		        <li v-if="actions.problem_edit">

		          <a :href="basePath()+'/service-desk/problem/'+problem.id+'/edit'">
		            {{lang('edit')}}
		          </a>
		        </li>

		        <li v-if="actions.problem_delete">

		          <a href="javascript:;" @click="showDeleteModal = true">{{lang('delete')}}</a>
		        </li>

		        <li v-if="actions.problem_close">
		        
		          <a href="javascript:;" @click="showProblemStatus = true">{{lang('close')}}</a>
		        </li>
		      </ul>
    		</div>
  		</template>
   	
   	<template v-else>
   		
   		<loader :animation-duration="4000" :size="0"/>
   	</template>

    <transition  name="modal">

      <problem-planning-modal v-if="showPlanningModal" :onClose="onClose" :showModal="showPlanningModal" 
        :identifier="identifier" :problemId="problem.id" alertCompName="problem-view" 
        :updateChangeData="updateChangeDetails">

      </problem-planning-modal>
    </transition>

    <transition name="modal">

      <problem-assets  v-if="showAttach" :onClose="onClose" :showModal="showAttach" :problemId="problem.id"
        :updateChangeData="updateChangeDetails">

      </problem-assets>
    </transition>

    <transition  name="modal">

      <problem-tickets v-if="showAttachTicket" :onClose="onClose" :showModal="showAttachTicket" 
          :problemId="problem.id" alertCompName="problem-view" :updateChangeData="updateChangeDetails">

      </problem-tickets>
    </transition>

    <transition name="modal">

      <problem-change v-if="showChangeModal" :onClose="onClose" :showModal="showChangeModal" 
        :problemId="problem.id" :type="change_type" :updateChangeData="updateChangeDetails">

      </problem-change>
    </transition>

    <transition  name="modal">

      <problem-detach v-if="showDetachModal" :onClose="onClose" :showModal="showDetachModal" 
          :changeId="problem.change.id" :problemId="problem.id" alertCompName="problem-view" 
          :updateChangeData="updateChangeDetails">

      </problem-detach>
    </transition>

    <transition name="modal">

      <problem-status-modal v-if="showProblemStatus" :onClose="onClose" :showModal="showProblemStatus" 
        :problemId="problem.id" :updateChangeData="updateChangeDetails">

      </problem-status-modal>
    </transition>

    <transition name="modal">

     <delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
        alertComponentName="problem-view" :deleteUrl="'/service-desk/api/problem-delete/' + problem.id" 
        redirectUrl="/service-desk/problems">

     </delete-modal>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios'

  export default {

    name : 'problem-actions',

    description : 'Problem actions component',

    props : {

      problem : { type : Object, default : ()=> {}},

      updateChangeDetails : { type : Function }
    },

    data() {

      return {

        showPlanningModal : false,

        showAttach : false,

        showAttachTicket : false,

        showChangeModal : false,

        showDetachModal : false,

        showProblemStatus : false,

        showDeleteModal : false,

        identifier : '',

        change_type : '',

        actions : '',
      }
    },

    beforeMount(){

      this.getActions();
    },

    created() {

      window.eventHub.$on('updateProblemActions',this.getActions);
    },

    methods : {

      getActions(){

        this.actions = '';

        axios.get('/service-desk/api/problem-action/'+this.problem.id).then(res=>{

          this.actions = res.data.data.actions;

          window.eventHub.$emit('problemActionsList',this.actions);

        }).catch(error=>{

          this.actions = '';
        })
      },

      subString(value,length = 15){

        return getSubStringValue(value,length)
      },

      planningMethod(identifier){

        this.showPlanningModal = true;

        this.identifier = identifier;
      },

      problemChange(type){

        this.change_type = type;

        this.showChangeModal = true;
      },

      onClose(){

        this.showPlanningModal = false;

        this.showAttach = false;

        this.showAttachTicket = false;

        this.showChangeModal = false;

        this.showDeleteModal = false;

        this.showDetachModal = false;

        this.showProblemStatus = false;

        this.getActions();

		    this.$store.dispatch('unsetValidationError');
		  },
    },

    components : {

    	'loader':require('components/Client/Pages/ReusableComponents/Loader'),

      'problem-planning-modal' : require('./ProblemPlanningModal'),

      'problem-assets' : require('./ProblemAssets'),

      'problem-tickets' : require('./ProblemTickets'),

      'problem-change' : require('./ProblemChange'),

      'problem-detach' : require('./ProblemDetach'),

      'problem-status-modal' : require('./ProblemStatusModal'),
      
      'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),
    }
  };
</script>

<style scoped>

  #more_actions { right: 0 !important; left: unset !important; }

  .margin{
    margin-left: 10px;
  }
</style>