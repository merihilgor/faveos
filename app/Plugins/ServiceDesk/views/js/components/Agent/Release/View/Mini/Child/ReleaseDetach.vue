<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{trans('detach')}}</h4>
			
		</div>
			
		<div v-if="!loading" slot="fields">
			
			<h5 id="release-detach-title">{{trans('are_you_sure')}}</h5>
		</div> 
			
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>
						
		<div slot="controls">
			
			<button type="button" id="submit_btn" @click="onSubmit()" class="btn btn-danger danger-btn" 
				:disabled="isDisabled">

				<i class="fa fa-unlink"></i> {{trans('detach')}}
			</button>
		</div>
	</modal>
</template>

<script>
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'problem-ticket-detach-modal',

		description : 'Problem Ticket Detach Modal component',

		props:{
			
			showModal : { type : Boolean, default : false },

			releaseId : { type : String | Number , default : ''},

			category : { type : String , default : ''},

			fieldId : { type : String | Number , default : ''},

			onClose : { type : Function }
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle : { width : '500px' },

				loading : false
			}
		},

		methods:{

			onSubmit(){
				
				this.loading = true
				
				this.isDisabled = true

				axios.delete('/service-desk/api/release/detach-'+this.category+'/'+this.releaseId+'/'+this.fieldId).then(res=>{
					
					successHandler(res,'release-view');
					
	        		window.eventHub.$emit('updateReleaseAssociates');
	        
	        		this.onClose()
					
					this.loading = false;
					
					this.isDisabled = true;
			
				}).catch(err => {
				
					this.loading = false;
					
					this.isDisabled = false;

					errorHandler(err,'release-view')
				})
			}
		},

		components:{
		
			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>

	#release-detach-title{
		margin-left:16px; 
	}

	.danger-btn{
    background-color: #c9302c !important;
    border-color: #ac2925 !important;
	}
</style>