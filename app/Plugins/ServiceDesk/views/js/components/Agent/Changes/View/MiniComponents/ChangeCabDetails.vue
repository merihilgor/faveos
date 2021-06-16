<template>

	<div>
	
		<loader v-if="loading"></loader>

		<template v-if="cabData.length > 0">
			
			<div v-if="!loading">

				<div class="box-container" v-for="(cab, cabIndex) in cabData" :id="'cab-element-'+cabIndex" 
					:style="{'border-color': getIconStyle(cab.status,'color')}">

					<div class="box-header with-border" :style="{'background-color': getIconStyle(cab.status,'color')}">
						
						<h3 class="box-title">
						
							<span>{{cab.name}}</span>
							
							<span :title="cab.status" :class="['cab-status', getIconStyle(cab.status)]"></span>
						</h3>
					</div>

					<div class="levels">
						
						<div class="box-body cab-level" v-for="(level,levelIndex) in cab.approval_levels" :id="'cab-level-'+levelIndex" 
							:style="{opacity : getOpacity(level.is_active)}">
							
							<div class="box-container margin-0 height-100-percent" :style="{'border-color': getIconStyle(level.status,'color')}">
								
								<div class="box-header with-border" :style="{'background-color': getIconStyle(level.status,'color')}">
									
									<span class="box-title">{{level.name}}&nbsp;&nbsp;</span>

									<span :title="level.status" :class="['pull-right', getIconStyle(level.status)]"></span>

								</div>

								<div class="box-body">
									
									<div class="no-wrap flex" v-for="(user,userIndex) in level.approve_users" :id="'level-user-'+userIndex">
										
										<span class="approver-name">{{user.name}}&nbsp;&nbsp;&nbsp;&nbsp; </span>
										
										<span :title="user.status" :class="['approver-status', getIconStyle(user.status)]"></span>
									</div>

									<div class="no-wrap flex" v-for="(userType,userTypeIndex) in level.approve_user_types" 
										:id="'level-user-type-'+userTypeIndex">
										
										<span class="approver-name">{{lang(userType.name)}}&nbsp;&nbsp;&nbsp;&nbsp;</span>

										<span :title="userType.status" :class="['approver-status', getIconStyle(userType.status)]"></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div v-if="!loading" id="cab-actions">
				
				<template v-if="actions.allowed_cab_action">

					<button class="btn btn-success" id="approve-button"
						@click="onCabAction('approve')" name="button">
						
						<span class="glyphicon glyphicon-ok"></span>&nbsp;{{lang('approve')}}
						
					</button>

					<button class="btn btn-danger" id="deny-button"
						@click="onCabAction('deny')" name="button">

						<span class="glyphicon glyphicon-remove"></span>&nbsp;{{lang('deny')}}
					</button>
				</template> 

				<button v-if="actions.remove_cab" class="btn btn-danger" id="remove-button"
					@click="onCabAction('remove')" name="button">
					
					<span class="glyphicon glyphicon-trash"></span>&nbsp;{{lang('remove')}}
				</button>
			</div>


			<modal v-if="showModal" :showModal="true" :onClose="() => showModal = false">
				
				<div slot="title">

					<h4 class="line-height-0">{{lang(modalTitle)}}</h4>
				</div>

				<div class="row margin-auto" slot="fields">
					
					<div class="margin-horizontal-10"><alert/></div>
					
					<loader v-if="loading"></loader>

					<p v-if="!loading" class="padding-left-15">{{lang(modalQuestion)}}</p>
					
					<text-field v-if="!loading && action !== 'remove'" id="textarea" :label="lang('comment')" :value="comment" type="textarea" 
						name="comment" :onChange="onChange" classname="col-xs-12" :required="true">
							
					</text-field>
				</div>

				<div slot="controls">
					
					<button id="cab-action-confirm" type="button" @click = "onCabActionConfirm()" class="btn btn-primary">
						
						<i class="glyphicon glyphicon-ok" aria-hidden="true"></i>&nbsp;{{lang('confirm')}}
					</button>
				</div>
			</modal>
		</template>

		<template v-if="!loading && cabData.length === 0">
			
			<h4 class="text-center">{{lang('no_data_found')}}</h4>
		</template>
	</div>
</template>

<script>

	import axios from 'axios';
	
	import Modal from 'components/Common/Modal';
	
	import {errorHandler, successHandler} from 'helpers/responseHandler';
	
	import {validateCabActionsSettings} from "../../../../../validator/cabActionsRules";
	
	import { lang } from 'helpers/extraLogics';

	export default {

		name:'change-cab',

		description: 'Change cab details component',

		props:{

			changeId: {type: Number|String, required:true},
		},

		data(){
			
			return {

				loading: false,

				cabData:[],

				comment:'',

				showModal:false,

				action: '',

				actions : '',
			}
		},

		beforeMount(){
			
			this.getDataFromServer();
		},
		
		created(){
			
			window.eventHub.$on('cabApplied', this.getDataFromServer);
		},

		methods:{

			getActions(){

        axios.get('/service-desk/api/change-action/'+this.changeId).then(res=>{

          this.actions = res.data.data.actions;

        }).catch(error=>{

          this.actions = '';
        })
      },

			getDataFromServer(){
				
				this.loading = true;

				this.cabData = [];

				this.getActions();
				
				axios.get('/service-desk/api/change-approval-status/'+this.changeId).then(res => {
					
					this.cabData = res.data.data

					this.loading = false;

				}).catch(err => {
					
					this.loading = false;

					errorHandler(err);
				})
			},

	 		isValid(){
				
				const {errors, isValid} = validateCabActionsSettings(this.$data)
				
				if(!isValid){
					
					return false
				}
				
				return true
			},

			onChange(value, name){
				
				this[name]= value;
			},

			onCabAction(type){
				
				this.showModal = true
				
				this.action = type;
			},

			getIconStyle(status, type="icon"){
				
				switch (status) {
					
					case 'PENDING':
						
						return type == "icon" ? 'glyphicon glyphicon-time text-warning' : '#eee';

					case 'APPROVED':
						
						return type == "icon" ? 'glyphicon glyphicon-check text-success' : '#cbe0d3';

					case 'DENIED':
						
						return type == "icon" ? 'glyphicon glyphicon-exclamation-sign text-danger' : '#f6ddd8';

					default:
						
						return null;
				}
			},

			getOpacity(isActive){
				
				if(isActive == 1){
					
					return 1;
				}
				
				return 0.5;
			},

			onCabActionConfirm(){
				
				if(this.action !== 'remove'){

					if(this.isValid()){
						
						this.loading = true;
						
						axios.post('/service-desk/api/approval-action-without-hash/'+this.changeId, { action_type: this.action, comment: this.comment })
						.then(res => {
							
							this.loading = false;

							successHandler(res);

							setTimeout(()=>{
							
								this.initialActions();

							}, 2000)
							
						}).catch(err => {
							
							this.loading = false;
							
							errorHandler(err);
						})
					}
				} else {

					this.loading = true;

					axios.delete('/service-desk/api/remove-cab-approval/'+this.changeId).then(res=>{
						
						this.loading = false;
						
						successHandler(res);
						
						setTimeout(()=>{
							
							this.initialActions();
							
						}, 2000)
					}).catch(err=>{
						
						this.loading = false;

						errorHandler(err);
					})
				}
			},

			initialActions(){

				this.showModal = false;
								
				this.action = '';
								
				this.comment = '';

				this.getDataFromServer();

				window.eventHub.$emit('cabActionPerformed');

				window.eventHub.$emit('updateActivity');

				window.eventHub.$emit('updateChangeData');
			}
		},

		computed:{

			modalTitle(){

				return this.action == 'approve' ? 'approve_cab' : this.action == 'remove' ? 'remove_cab' : 'deny_cab';
			},

			modalQuestion(){

				let message = `${lang('are_you_sure_you_want_to')} ${this.action}?`;

				return this.action == 'remove' ? message : `${message} ${lang('mention_a_reason')}`;
			},
		},

		components : {
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			'modal': Modal,
			
			'alert': require("components/MiniComponent/Alert"),
			
			'text-field': require('components/MiniComponent/FormField/TextField'),
		}
	};
</script>

<style scoped>
	.cab-level{
		margin: 0px;
		width: fit-content;
	}

	.levels {
		display: flex;
		overflow: auto;
	}

	.approver-status {
		margin-left: auto;
		order: 2;
	}

	.approver-name{
		width: max-content;
	}

	.cab-status{
		right: 12px;
		position: absolute;
		margin-top: 8px;
	}

	.box-header>.fa, .box-header>.glyphicon, .box-header>.ion, .box-header .box-title{
			margin-right:0;
	}

	#cab-actions {
		margin: 10px;
	}
	.padding-left-15{
		padding-left: 15px !important;
	}
</style>