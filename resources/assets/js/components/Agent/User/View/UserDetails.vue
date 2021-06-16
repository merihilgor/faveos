<template>
	
	<div>

		<div class="box box-primary">
			
			<template v-if="loading || !data">
				
				<div id="load" class="row">

					<loader :animation-duration="4000" :size="40"/>
				</div>
			</template>
			
			<template v-if="data && !loading">
		
				<div class="box-body " style="opacity: 1;">
					
					<div>

						<center>

							<faveo-image-element id="user_img" :source-url="data.profile_pic" :classes="['img-circle']" alternative-text="User Image" :img-width="100" :img-height="100"/>
								
							<h4 :title="data.full_name">{{subString(data.full_name)}}</h4>
														
							<span :class="{'success':data.email_verified == 1,'danger':data.email_verified != 1}" 
								class="fa fa-envelope" v-tooltip="data.email_verified == 1 ? lang('user_email_is_verified') : lang('user_has_not_verified_email')" 
								@click="userModal('email_verified')">
								
							</span>
								
							<span :class="{'success':data.mobile_verified == 1,'danger':data.mobile_verified != 1}" 
								class="fa fa-mobile" v-tooltip="data.mobile_verified == 1 ? lang('user_mobile_is_verified') : lang('user_has_not_verified_mobile')"  @click="userModal('mobile_verified')">
									
							</span>

							<span :class="{'success':data.is_2fa_enabled == 1,'danger':data.is_2fa_enabled != 1}"
									:id="'2fa_status_user__' + data.id" class="fa fa-shield" v-tooltip="data.is_2fa_enabled == 1 ? lang('user_enabled_2fa') : lang('user_not_enabled_2fa')">
							</span>

						</center>
					</div>
				</div>

				<div class="box-footer">
						
					<b>{{lang('user_name')}}</b>
						
					<a class="pull-right" v-tooltip="data.user_name">{{subString(data.user_name)}}</a>
				</div>

				<div class="box-footer">
						
					<b>{{lang('email')}}</b>
						
					<a class="pull-right" v-tooltip="data.email"> {{subString(data.email)}}</a>
				</div>

				<div class="box-footer">
						
					<b>{{lang('role')}}</b>
						
					<a class="pull-right text-capitalize text-green" v-tooltip="data.role"> {{subString(data.role)}}</a>
				</div>

				<div class="box-footer">
						
					<b>{{lang('work_phone')}}</b>
						
					<a class="pull-right" v-tooltip="data.phone_number"> 
						{{!data.phone_number || data.phone_number === 'Not available' ? '---' : subString(data.phone_number)}}
					</a>
				</div>

				<div class="box-footer">
						
					<b>{{lang('mobile')}}</b>
						
					<a class="pull-right" v-tooltip="data.mobile"> 
						{{!data.mobile || data.mobile === 'Not available' ? '---' : subString(data.mobile)}}
					</a>
				</div>

				<div v-if="data.country_code" class="box-footer">
						
					<b>{{lang('country_code')}}</b>
						
					<a class="pull-right" v-tooltip="data.country_code"> {{data.country_code ? subString(data.country_code) : '---'}}</a>
				</div>

				<div class="box-footer" v-if="data.role !== 'user'">
						
					<b>{{lang('agent_time_zone')}}</b>
						
					<a class="pull-right" v-tooltip="data.timezone ? data.timezone.name :''"> 
					{{data.timezone ? subString(data.timezone.name) : '---'}}</a>
				</div>

				<div class="box-footer" v-if="data.role !== 'user'">
						
					<b>{{lang('location')}}</b>
						
					<a class="pull-right" v-tooltip="data.location ? data.location.title : ''"> 
					{{data.location ? subString(data.location.title) : '---'}}</a>
				</div>

				<div v-if="data.role === 'user'" class="box-footer">

					<div id="refresh-org">

						<b>{{lang('organization')}}</b>
						
						<template v-if="data.organizations.length === 0 && !data.processing_account_disabling">
							
							<a class="pull-right" @click="orgClick('assign')" id="cursor">

								<i class="fa fa-hand-o-right"> </i> {{lang('assign')}} 
							</a>
							
							<org-create-modal :userId="data.id"></org-create-modal>

						</template>

						<template v-else>
							
							<ol v-for="org in data.organizations">

								<a :href="basePath()+'/organizations/'+org.id" v-tooltip="org.name">
									
									<span style="color:green;">{{subString(org.name)}}</span>
								</a>

								<a class="pull-right" v-tooltip="lang('remove')" id="cursor" @click="orgClick('remove',org.id)">
									
									<i class="fa fa-times" style="color:red;"> </i>
								</a>
							</ol>
						</template>
					</div>
				</div>

				<div v-if="data.OrganizationDepartmentStatus && data.role == 'user'" class="box-footer">

					<b>{{lang('organization_department')}}</b>
						
					<template v-for="dept in data.organization_dept">
							
						<a class="pull-right" v-tooltip="dept.name"> {{subString(dept.name,8)}}</a>
					</template>
				</div>

				<div v-if="data.role !== 'user'" class="box-footer">

					<div id="refresh-org">

						<b>{{lang('departments')}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						
						<a v-if="data.departments.length === 0" class="pull-right">---</a>

						<template v-else>
							
							<a v-for="(dept,index) in data.departments" :href="basePath()+'/department/'+dept.id" v-tooltip="dept.name">
								
								{{subString(dept.name)}}<span v-if="index != Object.keys(data.departments).length - 1">, </span>
							</a>
						</template>
					</div>
				</div>

				<div v-if="data.role !== 'user'" class="box-footer">
					
					<div id="refresh-team">

						<b>{{lang('teams')}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						
						<a v-if="data.teams.length === 0" class="pull-right">---</a>

						<template v-else>
							
							<a v-for="(team,index) in data.teams" :href="basePath()+'/assign-teams/'+team.id" v-tooltip="team.name">
								
								{{subString(team.name)}}<span v-if="index != Object.keys(data.teams).length - 1">, </span>
							</a>
						</template>
					</div>
				</div>

				<div v-if="data.role !== 'user'" class="box-footer">
					
					<div id="refresh-team">

						<b>{{lang('type')}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						
						<a v-if="data.types.length === 0" class="pull-right">---</a>

						<template v-else>
							
							<a v-for="(type,index) in data.types" v-tooltip="type.name">
								
								{{subString(type.name)}}<span v-if="index != Object.keys(data.types).length - 1">, </span>
							</a>
						</template>
					</div>
				</div>

				<div v-if="data.role === 'user'" class="box-footer">
						
					<b>{{lang('address')}}</b>
						
					<p v-tooltip="data.internal_note"> {{data.internal_note}}</p>
				</div>

				<div v-for="value in data.custom_field_values" class="box-footer">
						
					<b>{{value.label}}</b>
						
					<a v-if="Array.isArray(value.value)" v-tooltip="value.value.toString()">
						&nbsp;{{value.value.toString()}}
					</a>

					<a v-else class="pull-right" v-tooltip="value.value"> {{subString(value.value,15)}}</a>

				</div>

			</template>
			</div>

			<transition  name="modal">
				
				<org-modal v-if="showOrgModal" :onClose="onClose" :showModal="showOrgModal" :title="orgModalTitle" 
					:orgId="orgId" :userId="data.id" :deptCondition="data.OrganizationDepartmentStatus">
					
				</org-modal>
			</transition>

			<transition  name="modal">
				
				<user-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :title="modalTitle" 
					:userData="data">
					
				</user-modal>
			</transition>
		</div>
</template>

<script>

	import { getSubStringValue } from 'helpers/extraLogics';

	import { mapGetters } from 'vuex'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'user-details',

		description : 'User details  page',

		props : { 

			data : { type : Object | String , default : ''}
		},

		data() {

			return {
		
				loading : false,

				showOrgModal : false,

				orgModalTitle : '',

				orgId  : '',

				modalTitle : '',

				showModal : false,

				ban : this.data.ban,

				obj : {}
			}
		},

		methods :{

			subString(name, length = 15){

				return getSubStringValue(name,length)
			},

			onClose(){
				
				this.showModal = false;

				this.showOrgModal = false;
				
				this.$store.dispatch('unsetValidationError');
			},

			userModal(title){

				this.showModal = !this.data[title];

				this.modalTitle = title; 
			},


			commonMethod(api,value){

				if(api === '/settings/user/status'){

					this.obj['settings_status'] = value;
				}

				this.obj['user_id'] = this.data.id;

				axios.post(api,this.obj).then(res=>{

					this.loading = false;

					window.eventHub.$emit('refreshUserData');

					successHandler(res,'user-view')

				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'user-view');
				})
			},

			orgClick(name,id){

				this.orgId = id;

				this.showOrgModal = true;

				this.orgModalTitle = name;
			},
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'org-modal': require('./MiniComponents/OrganizationModal'),

			'user-modal': require('./MiniComponents/UserStatusModal'),

			'org-create-modal': require('./MiniComponents/OrganizationCreateModal'),
			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>
	
	#user_img{
		border:3px solid #CBCBDA;
		padding:3px;
	}
	#load{
		margin-top:30px;margin-bottom:30px;
	}

	.success{
		color : #017701 !important;
		cursor: pointer;
		padding: 3px;
	}
	.danger{
		color : red !important;
		padding: 3px;
		cursor: pointer;
	}
	#cursor{
		cursor: pointer;
	}
</style>