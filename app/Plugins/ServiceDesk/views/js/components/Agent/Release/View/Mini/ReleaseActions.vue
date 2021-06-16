<template>

  <div class="pull-right">

		<template v-if="actions">
		
			<div class="btn-group">

				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

				 <i class="fa fa-refresh"> </i> {{trans('planning')}} <span class="caret"></span>
				</button>

				<ul class="dropdown-menu">

				  <li><a href="javascript:;" @click="planningMethod('build-plan')">{{trans('build-plan')}}</a></li>

				  <li><a href="javascript:;" @click="planningMethod('test-plan')">{{trans('test-plan')}}</a></li>
				</ul>
			</div>

			<div class="btn-group">

				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

				  <i class="fa fa-link"> </i> {{trans('associate')}} <span class="caret"></span>
				</button>

				<ul class="dropdown-menu">

					<li v-if="actions.release_asset_attach">

						<a href="javascript:;" @click="showAttach = true"> <i class="fa fa-server"></i> {{trans('attach_asset')}}</a>
					</li>

					<li v-if="actions.release_change_attach">

						<a href="javascript:;" @click="showChangeModal = true">

						<i class="fa fa-refresh"></i> {{trans('attach_change')}}</a>
					</li>
				</ul>
			</div>

			<div class="btn-group">

				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

				  <i class="fa fa-cog"> </i> {{trans('more')}} <span class="caret"></span>
				</button>

				<ul class="dropdown-menu" id="more_actions">

					<li v-if="actions.release_edit">

						<a :href="basePath()+'/service-desk/releases/'+release.id+'/edit'">  {{trans('edit')}} </a>
					</li>

					<li v-if="actions.release_delete">

						<a href="javascript:;" @click="showDeleteModal = true">{{trans('delete')}}</a>
					</li>

					<li v-if="actions.release_completed">
				  
						<a href="javascript:;" @click="showReleaseStatus = true">{{trans('mark_completed')}}</a>
					</li>
				</ul>
			</div>
		</template>
		
		<template v-else>
			
			<loader :animation-duration="4000" :size="0"/>
		</template>

	 <transition  name="modal">

		<release-planning-modal v-if="showPlanningModal" :onClose="onClose" :showModal="showPlanningModal" 
			:identifier="identifier" :releaseId="release.id" alertCompName="release-view">

		</release-planning-modal>
	 </transition>

	 <transition name="modal">

		<release-assets  v-if="showAttach" :onClose="onClose" :showModal="showAttach" :releaseId="release.id"
		  :updateReleaseData="updateReleaseDetails">

		</release-assets>
	 </transition>

	 <transition name="modal">

		<release-change v-if="showChangeModal" :onClose="onClose" :showModal="showChangeModal" 
		  :releaseId="release.id" :updateReleaseData="updateReleaseDetails">

		</release-change>
	 </transition>

	 <transition name="modal">

		<release-status-modal v-if="showReleaseStatus" :onClose="onClose" :showModal="showReleaseStatus" 
			:releaseId="release.id" :updateReleaseData="updateReleaseDetails">

		</release-status-modal>
	 </transition>

	 <transition name="modal">

	  <delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
		  alertComponentName="release-view" :deleteUrl="'/service-desk/api/release/' + release.id" 
		  redirectUrl="/service-desk/releases">

	  </delete-modal>
	 </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios'

  export default {

	 name : 'release-actions',

	 description : 'Release actions component',

	 props : {

		release : { type : Object, default : ()=> {}},

		updateReleaseDetails : { type : Function }
	 },

	 data() {

		return {

		  showPlanningModal : false,

		  showAttach : false,

		  showChangeModal : false,

		  showReleaseStatus : false,

		  showDeleteModal : false,

		  identifier : '',

		  actions : '',
		}
	 },

	 beforeMount(){

		this.getActions();
	 },

	 created() {

		window.eventHub.$on('updateReleaseActions',this.getActions);

		window.eventHub.$on('updateReleaseAssociates',this.getActions);
	 },

	 methods : {

		getActions(){

		  this.actions = '';

		  axios.get('/service-desk/api/release-action/'+this.release.id).then(res=>{

			 this.actions = res.data.data.actions;

			 window.eventHub.$emit('updateAssociatesTableActions',this.actions);
			 
			 window.eventHub.$emit('updateAssociatesTab',this.actions.check_planning);

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

		onClose(){

			this.showPlanningModal = false;

			this.showAttach = false;

			 this.showChangeModal = false;

			 this.showDeleteModal = false;

			 this.showReleaseStatus = false;

			this.$store.dispatch('unsetValidationError');
		 },
	 },

	 components : {

		'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		'release-planning-modal' : require('./Child/ReleasePlanningModal'),

		'release-assets' : require('./Child/ReleaseAssets'),

		'release-change' : require('./Child/ReleaseChange'),

		'release-status-modal' : require('./Child/ReleaseStatusModal'),
		
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