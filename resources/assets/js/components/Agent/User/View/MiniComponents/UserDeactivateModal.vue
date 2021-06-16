<template>
	
	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
			
			<div slot="alert">

				<alert componentName="user-deactivate"/>
			</div>

			<div slot="title">

				<h4>{{lang('deactivate')}}</h4>
			</div>
			
			<div id="deactive" slot="fields">

				<template v-if="!loading">

					<div slot="fields">

						<b id="bold">{{lang('what_should_happen_to_tickets_requested_by')}} {{user.user_name ? user.user_name : user.email }}?</b><br>

						<div class="row">
							
							<div class="form-group col-md-12">

								<radio-button :options="reqOptions" :label="lang('Options')" name="req_condition" :value="req_condition"
									:onChange="onChange" classname="form-group col-xs-12" :labelStyle="labelStyle">
										
								</radio-button>

								<dynamic-select v-if="req_condition == 'change_owner'"  :multiple="false" name="req_assign" 
									:label="lang('select_owner')" classname="col-xs-12" :value="req_assign" :onChange="onChange"
									apiEndpoint="/api/dependency/users?meta=true" :required="true" :clearable="req_assign ? true : false"
									strlength="50" :labelStyle="labelStyle">

								</dynamic-select>
							</div>
						</div>
					</div>

					<div v-if="role != 'user'" slot="fields">

						<b id="bold">{{lang('what_should_happen_to_tickets_assigned_to')}} {{user.user_name ? user.user_name : user.email }}?</b><br>

						<div class="row">
							
							<div class="form-group col-md-12">

								<radio-button :options="radioOptions" :label="lang('Options')" name="condition" :value="condition"
									:onChange="onChange" classname="form-group col-xs-12" :labelStyle="labelStyle">
										
								</radio-button>

								<dynamic-select v-if="condition == 'change_assignee'" :label="lang('assign')" :multiple="false" 
									:labelStyle="labelStyle" name="assign"  classname="col-xs-12" :apiEndpoint="userEndpoint" strlength="50"
									:value="assign" :onChange="onChange":required="true" :clearable="assign ? true : false">

								</dynamic-select>
							</div>
						</div>
					</div>
				</template>

				<div v-if="loading" class="row" slot="fields" >
				
					<loader :animation-duration="4000" :size="60"/>
				</div>			
			</div>
						
			<div slot="controls">
				
				<button type="button" @click="onSubmit" class="btn btn-danger" :disabled="isDisabled">

					<i class="fa fa-trash"></i> {{lang('deactivate')}}
				</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'
	
	import { validateDeactivateSettings } from "helpers/validator/deactivateRules.js"
	
	import axios from 'axios'
	
	export default {
		
		name : 'deactivate-modal',
	
		description : 'Deactivate Modal component',
	
		props:{
	
			showModal:{type:Boolean,default:false},
	
			userId : { type : String | Number, default : '' },
	
			onClose:{type: Function},
	
			role : { type : String , default : '' },

			user : { type : Object, default : ()=>{}}
		},
	
		data(){
	
			return {
	
				isDisabled:false,
	
				containerStyle : { width:'650px' },

				radioOptions:[
				
					{ name : 'unassign_all_open_tickets' , value : 'surrender' },
				
					{ name : 'change_assignee' , value : 'change_assignee'}
				],
				
				loading:false,

				condition : 'surrender',
				
				assign : '',
				
				labelStyle : { display:'none' },
								
				data : {},

				req_condition : 'close',

				req_assign : '',
				
				reqOptions:[
				
					{ name : 'close_all_open_tickets' , value : 'close' },
				
					{ name : 'change_requester' , value : 'change_owner'}
				],

				userEndpoint : this.role === 'user' ? '/api/dependency/users?meta=true&supplements=user-only' 
												: '/api/dependency/users?meta=true&supplements=agent-admin-only',
			}
		},

		methods:{
		
			onChange(value,name){
		
				this[name]=value;

				this.req_assign = this.req_condition == 'close' ? '' : this.req_assign;

				this.assign = this.condition == 'surrender' ? '' : this.assign;
			},
			
			isValid(){
				
				const {errors, isValid} = validateDeactivateSettings(this.$data);
			
				if(!isValid){
					
					return false
				}
				
				return true
			},
			
			onSubmit(){
				
				if(this.isValid()){
					
					this.loading = true
			
					this.isDisabled = true

					this.data['action_on_owned_tickets'] = this.req_condition;
					
					if(this.req_assign){

						this.data['set_owner_to'] = this.req_assign.id;
					}
					
					this.data['action_on_assigned_tickets'] = this.condition;

					if(this.assign){

						this.data['set_assignee_to'] = this.assign.id;
					}

					axios.post('/api/account/deactivate/'+this.userId,this.data).then(res=>{
					
						window.eventHub.$emit('refreshUserData','deactivate');
						
						successHandler(res,'user-view')
					
						this.loading = false;
			
						this.isDisabled = false;
						
						this.onClose();
			
					}).catch(err => {
			
						this.loading = false;
					
						this.isDisabled = false;
			
						errorHandler(err,'user-deactivate');
		
					})
				}
			}
		},
		
		components:{
		
			'modal':require('components/Common/Modal.vue'),
		
			'alert' : require('components/MiniComponent/Alert'),
		
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
			
			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),
		}
	};
</script>

<style>
	
	#H5{
		margin-left:16px; 
	}

	.note {
		margin-left:16px; 
		margin-right:16px; 
		font-style: italic;
	}
 
	#deactive{
		max-height: 250px;
		min-height: 50px;
		overflow-x: hidden;
		overflow-y: auto;
	}
	#bold{
		line-height: 2;
		margin-left: 16px;
		font-size: 16px;
	}
</style>