<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{trans('contract_expiry_reminder')}}</h4>
		</div>

		<div slot="fields" v-if="!loading" class="row">

			<div class="col-sm-12">
				
				<number-field :label="lang('notify_before')"
					:value="notifyBefore"  
					name="notifyBefore" 
					classname="col-xs-6"
					:onChange="onChange" 
					type="number"
					:required="true"
					:hint="lang('notify_in_days')"
					>
				</number-field>

				<dynamic-select :label="lang('notify_to')" 
					:multiple="true" 
					name="notify_to" 
					:prePopulate="false"
					classname="col-xs-6" 
					apiEndpoint="/api/dependency/users?meta=true" 
					:value="notify_to" 
					:onChange="onChange" 
					:required="true"
					:taggable="true"
					:hint="lang('notify_to_contracts')"
					:strlength="18"
					>
				</dynamic-select>
		  	</div>
		</div>

		<div v-if="loading" class="row" slot="fields" >

		  	<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

			 	<i class="fa fa-save"></i> {{trans('save')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { validateContractReminderSettings } from "../../../../../../validator/contractReminderValidation.js";

	import axios from "axios"

	export default {

		name : 'contract-reminder-modal',

		description : 'Contract Reminder Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

			contract : { type : Object, default : ()=> {} },

			updateData : { type : Function, default : ()=>{} }
		},

		data(){

			return {

				isDisabled : false,

				loading : false,

				notify_to : this.contract.notify_to ? this.contract.notify_to : '',

				notifyBefore  : this.contract.notify_before ? this.contract.notify_before : '',

				containerStyle : { width : '800px' },
			}
		},

		methods : {

			onChange(value, name) {

				this[name] = value;
			},

			isValid(){
	
				const { errors, isValid } = validateContractReminderSettings(this.$data);
				
				return isValid;
			},

			onSubmit() {

				if(this.isValid()) {

					this.loading = true 

					this.isDisabled = true 
		
					var fd = new FormData();
					
					if(this.notifyBefore) {
							
						fd.append('notify_before', this.notifyBefore)
					}
					
					if(this.notify_to != '' && this.notify_to.length > 0){
						
						for(var i in this.notify_to){
							
							if(this.notify_to[i].id && typeof(this.notify_to[i].id) === 'number'){
								
								fd.append('notify_agent_ids['+i+']', this.notify_to[i].id);
							
							} else {
							
								fd.append('email_ids['+i+']', this.notify_to[i].name);
							}
						}
					}

					const config = { headers: { 'Content-Type': 'multipart/form-data' } }
					
					axios.post('/service-desk/api/contract-expiry-reminder/'+this.contract.id, fd,config).then(res => {
					
						this.loading = false;

						this.isDisabled = false;
						
						successHandler(res,'contract-view');

						window.eventHub.$emit('updateContractAssociates');

						this.updateData();

						this.onClose();
						
					}).catch(err => {

						this.loading = false;

						this.isDisabled = false;
						
						errorHandler(err,'contract-view');

					});
				}
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		
			'number-field':require('components/MiniComponent/FormField/NumberField'),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}
	};
</script>