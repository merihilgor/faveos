<template>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{trans(type)}}</h4>
		</div>

		<div v-if="!loading" slot="fields">

			<div v-if="type == 'reject' || type == 'approve'" class="col-sm-12">
				
				<ckeditor :value="reason" type="text" :onChange="onChange" name="reason" 
					:label="type == 'approve' ? trans('reason_approve') : trans('reason_rejection')" 	
					classname="width55" :required="type != 'approve' ? true : false" :lang="'en'">
									
				</ckeditor>
			</div>

			<h5 id="terminate" v-if="type == 'terminate'">{{trans('terminate_description')}}</h5>

		</div>

		<div v-if="loading" class="row" slot="fields" >

		  <loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" :class="btnClass" :disabled="isDisabled">

			 	<i :class="iconClass" aria-hidden="true"></i> {{trans(type)}}
			 </button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'contract-approve',

		description : 'Contract Approve Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			contractId:{type:String|Number , default : ''},

			type : { type : String, default : '' },

			onClose:{type: Function},

			updateData : { type : Function, default : ()=>{} }
		},

		data(){

			return {

			 	isDisabled:false,

			 	containerStyle:{ width:'700px' },

			 	loading:false,

			 	reason : '',
		  	}
	 	},

	 	beforeMount() {

	 		this.isDisabled = this.type == 'reject' ? true : false;
	 	},

	 	computed : {

	 		btnClass(){

	 			return this.type == 'approve' ? 'btn btn-success' : 'btn btn-danger'
	 			
	 		},

	 		iconClass() {

	 			return this.type == 'approve' ? 'fa fa-check' : this.type == 'reject' ? 'fa fa-unlink' : 'fa fa-ban'
	 		}
	 	},

		methods:{

			onChange(value, name){
				
				this[name]= value;

				this.isDisabled = value ? false : true
			},
			
			onSubmit(){

				this.loading = true

				this.isDisabled = true

				const data = {};

				if(this.type == 'reject') {
					
					data['purpose_of_rejection'] = this.reason;
				}

				if(this.type == 'approve') {
					
					data['purpose_of_approval'] = this.reason;
				}

				axios.post('/service-desk/api/contract-'+this.type+'/'+this.contractId,data).then(res=>{
			
	 	 			successHandler(res,'contract-view');

	 	 			this.afterAction()

		  		}).catch(err => {

					errorHandler(err,'contract-view')

					this.afterAction()
				})
			},

			afterAction(){

				window.eventHub.$emit('updateContractAssociates');
		  	
	  			this.updateData();

	  			this.onClose();

				this.loading = false;

	  			this.isDisabled = true
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

		 	'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		 	'ckeditor': require('components/MiniComponent/FormField/CkEditorVue'),
		}
	};
</script>

<style scoped>

  #action-modal-title{
		margin-left:16px;
  	}

  	#terminate { padding-bottom: 10px; margin-left: 16px;padding-top: 10px;}
</style>