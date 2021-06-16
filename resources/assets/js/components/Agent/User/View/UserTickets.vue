<template>
	
	<div id="agent-tickets-table">
		
		<div class="row">
			
			<div class="col-md-12">
				
				<alert componentName="UserTickets"/>
			</div>
		</div>

		<div class="box box-primary">
			
			<div class="box-header">
				
				<div v-if="role" class="row">
					
					<div class="pull-right" v-if="!disabled_process">

						<template v-if="getUserData && getUserData.user_data.id != id ">
							
							<button v-if="role !== 'user'" type="button" class="btn btn-sm btn-default" @click="changeRole('user')"
							id="user_role">

								<i class="fa fa-male">&nbsp;&nbsp;</i>{{lang('change_role_to_user')}}
							</button>

							<button v-if="role !== 'agent'" type="button" class="btn btn-sm btn-default" @click="changeRole('agent')"
								id="agent_role">

								<i class="fa fa-user">&nbsp;&nbsp;</i>{{lang('change_role_to_agent')}}
							</button>
							 
							<button v-if="role !== 'admin' && userRole === 'admin'" type="button" class="btn btn-sm btn-default" @click="changeRole('admin')"
								id="admin_role">

								<i class="fa fa-user-secret">&nbsp;&nbsp;</i>{{lang('change_role_to_admin')}}
							</button>

						</template>

						<a :href="basePath()+'/user/'+id+'/edit'" class="btn btn-sm btn-default">

							<i class="fa fa-edit">&nbsp;&nbsp;</i>{{lang('edit')}}
						</a>       

						<button type="button" class="btn btn-sm btn-default" @click="showPasswordModal = true" id="show_pass">

							<i class="fa fa-lock">&nbsp;&nbsp;</i>{{lang('change_password')}}
						</button>

						<template v-if="getUserData && getUserData.user_data.id != id ">

							<button type="button" class="btn btn-sm btn-default"  @click="showDeactivateModal = true" id="deactive_pop">

								<i class="fa fa-trash">&nbsp;&nbsp;</i>{{lang('deactivate')}}
							</button>
						</template>

						<button v-if="two_factor" type="button" class="btn btn-sm btn-default" @click="showDisable2FA = true">

							<i class="fa fa-ban">&nbsp;&nbsp;</i>{{lang('disable_2fa')}}
						</button>
					</div>
				</div>
			</div>

			<div class="box-body">

				<div class="row">

					<div v-if="hasDataPopulated" class="nav-tabs-custom">
					
					<ul class="nav nav-tabs">
						
						<li v-for="section in tabs" :class="{ active: category === section.id }">
						
							<a id="tickets_tab" href="javascript:void(0)" data-toggle="tab" @click="tickets(section)">
						
								<strong>{{lang(section.title)}}</strong>
						
								<span class="badge">{{section.count}}</span>
						
							</a>
						</li>
					</ul>

					<div class="tab-content">

						<div class="active tab-pane" id="activity">
							
							<button type="button" @click="mergeTicket()" class="btn btn-sm btn-default" id="merge_ticket">
								<i class="fa fa-code-fork"> </i> {{lang('merge')}}
							</button>
						
							<div class="btn-group" id="status_btn">
							
								<button type="button" id="status" class="btn btn-sm btn-default dropdown-toggle" 
									@click="getStatusList()" data-toggle="dropdown">
									<i class="fa fa-exchange"> </i> {{lang('change_status')}} <span class="caret"></span>
								</button>
								
								<ul class="dropdown-menu" v-if="ticketId.length > 0">

									<li  v-if="loading" class="row">
											<loader :animation-duration="4000" color="#1d78ff" :size="30"/>
									</li>
								
									<li v-else v-for="status in statusList" :key="status.id">

										<a v-if="statusList.length > 0" id="change_status" @click="getStatus(status)" style="cursor:pointer">
											<i :class="status.icon" :style="{color:status.icon_color}"> </i>{{status.name}}
										</a>

										<a v-else class="text-center text-black">{{lang('no-records')}}</a>
									</li>
							
								</ul>

							</div>

							<button v-if="ticketId.length > 0" type="button" @click="showAssignModal = true" class="btn btn-sm btn-default" id="assign_ticket">
								<i class="fa fa-hand-o-right"> </i> {{lang('assign')}}
							</button>
							<div id="my_tic">
								
								<agent-tickets-table :category="category" :id="id" :tickets="ticketsData" :key="category" 
									:apiUrl="apiUrl" :componentTitle="componentTitle" scroll_to="agent-tickets-table">
									
								</agent-tickets-table>
							</div>
						</div>				
					</div>
				</div>

				<div v-if="!hasDataPopulated && loading" class="row">

					<loader :animation-duration="4000" :size="60"/>
				</div>

				<transition  name="modal">

					<change-status-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :statusIds="statusIds" :ticketIds="ticketId" :status="change_status" :componentTitle="componentTitle">
						
					</change-status-modal>
				</transition>

				<transition  name="modal">
					<merge-ticket-modal v-if="showMergeModal" :onClose="onClose" :showModal="showMergeModal" :parent_tickets="parent_tickets" :ticketIds="ticketId" :componentTitle="componentTitle">

					</merge-ticket-modal>
				</transition>

				<transition  name="modal">
					<assign-ticket-modal v-if="showAssignModal" :onClose="onClose" :showModal="showAssignModal"
						:ticketIds="ticketId" :componentTitle="componentTitle">

					</assign-ticket-modal>
				</transition>

				<transition  name="modal">
				
					<user-role-modal v-if="showRoleModal" :onClose="onClose" :showModal="showRoleModal" 
						:role_to="role_to" :role="role" :userId="id" :dept="dept" :user="user">
						
					</user-role-modal>
				</transition>

				<transition  name="modal">
				
					<password-change-modal v-if="showPasswordModal" :onClose="onClose" :showModal="showPasswordModal" 
						:userId="id">
						
					</password-change-modal>
				</transition>

				<transition  name="modal">
				
					<user-deactivate-modal v-if="showDeactivateModal" :onClose="onClose" :showModal="showDeactivateModal" 
						:userId="id" :role="role" :user="user">
						
					</user-deactivate-modal>
				</transition>

				<transition name="modal">
				
					<remove-modal v-if="showDisable2FA" :onClose="onClose" :showModal="showDisable2FA" alertName="user-view" :id="id">
						
					</remove-modal>
				</transition>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import {arrayUnique} from 'helpers/extraLogics';

	import { mapGetters } from 'vuex'

	export default {

		name : 'agent-tickets',

		description : 'Agent panel tickets page',

		props : { 

			dept : { type : Array, default : ()=>[]},

			role : { type : String, default : ''},

			id : { type : String|Number, default : ''},

			user : { type : Object, default : ()=>{}},

			two_factor : { type : Boolean | Number, default : false},

			disabled_process : { type : Boolean | Number, default : false}
		},

		data() {

			return {

				currentTabId : 1,

				category:'',

				status_type : '',

				tabs:[],
				
				loading:true,

				hasDataPopulated : false,
				
				ticketId:[],

				showModal : false,

				statusList : [],

				showModal : false,

				showMergeModal : false,

				showAssignModal : false,

				showRoleModal : false,

				showPasswordModal : false,

				showDeactivateModal : false,

				parent_tickets : [],

				change_status : '',

				role_to : '',

				componentTitle : 'UserTickets',

				apiUrl : '',

				showDisable2FA : false,

				ticketTabData : {}

			}
		},

		beforeMount(){
			
			this.category = 'open';

			this.getCount();


		},

		computed : {

			userRole(){
				return sessionStorage.getItem('user_role');
			},

			...mapGetters(['getUserData'])
		},

		methods :{

			getStatusList(){
				
				if(this.ticketId.length > 0){

					this.loading = true;
				
					let params = {meta: true, supplements:arrayUnique(this.ticketId),limit : 'all'}
	        
					axios.get('/api/dependency/statuses',{params}).then(res =>{
				
						this.loading  = false
				
						this.statusList=res.data.data.statuses;
				
					}).catch(err=>{
				
						this.loading  = false
					})
				} else {

					alert('Please select tickets.')
				}
			},

			getStatus(status){
				
				if(this.ticketId.length > 0){
				
					this.showModal = true
				
					this.change_status = status
				
					this.statusIds = this.tic_statuses
				
				}else {
					
					alert('Please select tickets.');

					this.showModal = false
				}
			},

			mergeTicket(){
			
				if(this.ticketId.length > 1){
			
					this.showMergeModal = true
			
					axios.get('/api/agent/tickets/get-merge-tickets',{ params : {'ticket-ids' : this.ticketId} }).then(res=>{
			
						this.parent_tickets = res.data.data
			
						for (var i in this.parent_tickets) {
			
							this.parent_tickets[i].id = this.parent_tickets[i]['ticket_id'];

							this.parent_tickets[i].subject = this.parent_tickets[i]['name'];
							
							this.parent_tickets[i].name = this.parent_tickets[i]['title'];
			
							delete this.parent_tickets[i].ticket_id;
			
							delete this.parent_tickets[i].title;
						}
					}) 
			
					this.parent_tickets = [];
				}else {
			
					this.showMergeModal = false
			
					alert('Please select 2 or more than 2 ticket.')
				}
			},

			tickets(category){
				this.category = category.id
				this.apiUrl = category.url
			},


			changeRole(role){

				this.role_to = role;

				this.showRoleModal = true;
			},

			onClose(){
				
				this.showModal = false;
				
				this.showMergeModal = false;
				
				this.showAssignModal = false;

				this.showRoleModal = false;

				this.showPasswordModal = false;

				this.showDeactivateModal = false;

				this.showDisable2FA = false;
				
				this.getCount();
				
				this.$store.dispatch('unsetValidationError');
			},

			ticketsData(data){
		
				this.ticketId = data
			},

			getCount(){

				let params;
			
				let endPoint = 'api/agent/ticket-count?';

        		let path = window.location.pathname.split('/');

				if(path[path.length-2] === 'user'){
					params = 'owner-ids[]='+this.id;
					endPoint += params;
				}
					  
				else {
					params = 'assignee-ids[]='+this.id;
					endPoint += params;
				}

				axios.get(endPoint).then(res=>{
					
					this.hasDataPopulated = true;
					
					this.loading = false;

					this.tabs = [];
				
					for (var key in res.data.data) {
					
						if (res.data.data.hasOwnProperty(key)) {

							let tabsBody = { id : key, title : key, count : res.data.data[key]}
							
							switch(tabsBody.title) {
								case 'open':
									tabsBody['url'] = 'api/agent/ticket-list?category=inbox&'+params;
									break;
								case 'closed':
									tabsBody['url'] = 'api/agent/ticket-list?category=closed&'+params;
									break;
								case 'deleted':
									tabsBody['url'] = 'api/agent/ticket-list?category=deleted&'+params;
									break;
								case 'unapproved':
									tabsBody['url'] = 'api/agent/ticket-list?category=unapproved&'+params;
									break;			
							}
							
							this.tabs.push(tabsBody)
						}
					}

					this.tickets(this.tabs[0])

				}).catch(error=>{

					this.hasDataPopulated = true;

					this.loading = false;
				})
			},

			updateStatus(status){

				this.showModal = true
				
				this.change_status = status;
			},
		},

		components : {
			
			'agent-tickets-table' : require('components/Agent/TicketsComponents/TicketsTable'),

			'change-status-modal': require('components/Agent/TicketsComponents/ChangeStatusModal'),

			'merge-ticket-modal': require('components/Agent/TicketsComponents/MergeTicketModal'),

			'assign-ticket-modal': require('components/Agent/TicketsComponents/AssignTicketModal'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			"alert": require("components/MiniComponent/Alert"),

			'user-role-modal': require('./MiniComponents/UserRoleModal'),

			'password-change-modal': require('./MiniComponents/PasswordChangeModal'),

			'user-deactivate-modal': require('./MiniComponents/UserDeactivateModal'),

			'remove-modal' : require('components/Agent/Profile/RemoveVerification'),
		}
	};
</script>

<style scoped>
	.tab-content {
		margin-top: 15px !important;
	}
	.badge{
		border-radius: 3px !important;
		background: #3c8dbc;
	}

	#tickets_tab {
		color : black !important;
	}

	#assign_ticket{
		margin-left: -1px;
	}
</style>