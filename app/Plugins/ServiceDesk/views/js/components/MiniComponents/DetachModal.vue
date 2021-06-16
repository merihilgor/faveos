<template>
	<div> 
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
			
			<div slot="title" id="alert_top">

				<alert componentName="problem-modal"/>
			
			</div>

			<div slot="title">

				<h4>{{lang('detach')}}</h4>
			
			</div>
			
			<div v-if="!loading" slot="fields">
				<h5 id="H5" :class="{margin: lang_locale == 'ar'}">{{lang('are_you_sure')}}</h5>
			</div> 
			
			<div v-if="loading" class="row" slot="fields" >
				<loader :animation-duration="4000" color="#1d78ff" :size="size"/>
			</div>
						
			<div slot="controls">
				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled"><i class="fa fa-unlink" aria-hidden="true"></i> {{lang('detach')}}</button>
			</div>

		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {
		
		name : 'delete-modal',

		description : 'Delete Modal component',

		props:{
			
			/**
			 * status of the modal popup
			 * @type {Object}
			 */
			showModal:{type:Boolean,default:false},

			/**
			 * @type {Number}
			 */
			assetId:{type:Number , default : 0},

			/**
			 * The function which will be called as soon as user click on the close button        
			 * @type {Function}
			*/
			onClose:{type: Function},

			from : { type: String, default : ''},

			problemId : { type : Number|String , default : '' },

			data : { type : Object|String, default : ''},

			alert : { type : String, default : '' }

		},

		data:()=>({
			
			
			/**
			 * buttons disabled state
			 * @type {Boolean}
			 */
			isDisabled:false,

			/**
			 * width of the modal container
			 * @type {Object}
			 */
			containerStyle:{
				width:'500px'
			},

			/**
			 * initial state of loader
			 * @type {Boolean}
			 */
			loading:false,

			/**
			 * size of the loader
			 * @type {Number}
			 */
			size: 60,

			/**
			 * for rtl support
			 * @type {String}
			*/
			lang_locale:'',

			ticketId : 0,

			apiUrl : ''

		}),

		computed:{

			alertName() {

				return JSON.parse(this.data).alertName ? JSON.parse(this.data).alertName : 'problem-modal'
			},
          	...mapGetters(['getStoredTicketId'])
       	},

		created(){
		// getting locale from localStorage
			this.lang_locale = localStorage.getItem('LANGUAGE');
			this.ticketId = this.getStoredTicketId;
		},

		methods:{
		/**
		 * api calls happens here
		 * @return {Void} 
		 */
		onSubmit(){

		//for delete
			this.apiUrl = this.from !== 'modal' ? '/service-desk/api/detach-asset/'+ this.ticketId + '/' + this.assetId : '/service-desk/api/problem-detach-ticket/'+ this.problemId + '/' + this.ticketId 
			this.loading = true
			this.isDisabled = true

			axios.delete(this.apiUrl).then(res=>{
				
				if(this.from !== 'modal'){

					if(this.alert == 'timeline'){

						successHandler(res,this.alert);

						window.eventHub.$emit('AssetListrefreshData');

					} else {

						successHandler(res,'dataTableModal');
						
						window.eventHub.$emit('refreshData');
					}

					this.onClose();
				}else{
					successHandler(res,this.alertName);    
					this.actionMethod();				}
					this.loading = false;
					this.isDisabled = true
				}).catch(err => {
					errorHandler(err,'dataTableModal')

					this.onClose();
				})
			},

			actionMethod(){
				
				window.eventHub.$emit('actionDone');

				this.onClose()   
			}
	},

	components:{
		'modal':require('components/Common/Modal.vue'),
		'alert' : require('components/MiniComponent/Alert'),
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
	}

};
</script>

<style type="text/css">
.has-feedback .form-control {
	padding-right: 0px !important;
}
#H5{
	margin-left:16px; 
	/*margin-bottom:18px !important;*/
}
.fulfilling-bouncing-circle-spinner{
	margin: auto !important;
}
.margin {
	margin-right: 16px !important;margin-left: 0px !important;
}
 #alert_top{
	margin-top:20px;
	font-size: 14px !important;
}
</style>