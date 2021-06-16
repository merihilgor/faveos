<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{trans(type)}}</h4>
		</div>

		<div slot="fields" v-if="!loading" class="row">

			<div class="col-sm-12">
				
				<date-time-field :label="trans('contract_start_date')"
					:value="contract_start_date" 
					type="datetime"
					name="contract_start_date"
					:onChange="onChange" 
					:required="true" 
					format="MMMM Do YYYY, h:mm a" 
					classname="col-xs-6"
					:clearable="true" 
					:disabled="dateDisabled" 
					:editable="true"
					:currentYearDate="false" 
					:time-picker-options="timeOptions"
					>
				</date-time-field>

				<date-time-field :label="trans('contract_end_date')"
					:value="contract_end_date" 
					type="datetime"
					name="contract_end_date"
					:onChange="onChange" 
					:required="true" 
					format="MMMM Do YYYY, h:mm a" 
					classname="col-xs-6"
					:clearable="true" 
					:disabled="false" 
					:editable="true"
					:currentYearDate="false" 
					:time-picker-options="timeOptions"
					:notBefore="contract_start_date"
					>
				</date-time-field>
		  	</div>

		  	<div class="col-sm-12">
		  		
		  		<dynamic-select :label="trans('approver')" 
					:multiple="false" 
					name="approver" 
					:prePopulate="false"
					classname="col-xs-6" 
					apiEndpoint="/api/dependency/users?meta=true" 
					:value="approver" 
					:onChange="onChange" 
					:required="true"
					:clearable="approver ? true : false"
					:strlength="18"
					>
				</dynamic-select>

		  		<number-field :label="trans('cost')"
					:value="cost"  
					name="cost" 
					classname="col-xs-6"
					:onChange="onChange" 
					type="number"
					:required="true"
					>
				</number-field>
		  	</div>
		</div>

		<div v-if="loading" class="row" slot="fields" >

		  	<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

			 	<i :class="type == 'extend' ? 'fa fa-expand' : 'fa fa-refresh'"></i> {{trans(type)}}
			 </button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from "axios"

	import moment from 'moment'

	import { validateExtendContractSettings } from "../../../../../../validator/extendContractValidation.js";

	import { mapGetters } from 'vuex'

	export default {

		name : 'contract-extend-modal',

		description : 'Contract Extend Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

			contract : { type : Object, default : ()=> {} },

			updateData : { type : Function, default : ()=>{} },

			type : { type : String, default : '' }
		},

		data(){

			return {

				isDisabled : false,

				loading : false,

				contract_start_date : '',
				
				contract_end_date : '',

				timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },

				approver : '',

				cost : '',

				containerStyle : { width : '800px' },

				dateDisabled : false,
			}
		},

		beforeMount() {

			this.dateDisabled = this.type == 'extend' ? true : false;

			if(this.dateDisabled) {

				this.contract_start_date = this.formattedTime(this.contract.contract_end_date);
			}
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		methods : {

			onChange(value, name) {

				this[name] = value;
			},

			isValid(){
	
				const { errors, isValid } = validateExtendContractSettings(this.$data);
				
				return isValid;
			},

			onSubmit() {

				if(this.isValid()){
			
					this.loading = true 

					this.isDisabled = true 
		
					var fd = new FormData();
					
					fd.append('cost', this.cost)
					
					fd.append('approver_id', this.approver.id);

					fd.append('contract_start_date', moment(this.contract_start_date).format('YYYY-MM-DD HH:mm:ss'));
					
					fd.append('contract_end_date', moment(this.contract_end_date).format('YYYY-MM-DD HH:mm:ss'))
					
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }
					
					axios.post('/service-desk/api/contract-'+this.type+'/'+this.contract.id, fd,config).then(res => {
						
						window.eventHub.$emit('updateContractAssociates');
						
						this.loading = false;

						this.isDisabled = false;
						
						successHandler(res,'contract-view');

						this.updateData();

						this.onClose();
						
					}).catch(err => {

						this.loading = false;

						this.isDisabled = false;
						
						errorHandler(err,'contract-view');

						this.onClose();					
					});
				}
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
		
			'number-field':require('components/MiniComponent/FormField/NumberField'),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}
	};
</script>

<style type="text/css">

</style>