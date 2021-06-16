<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{trans('detach')}}</h4>
			
		</div>
			
		<div v-if="!loading" slot="fields">
			
			<h5 class="asset_detach_modal_title">{{trans('are_you_sure')}}</h5>
		</div> 
			
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>
						
		<div slot="controls">
			
			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-danger detach_btn" :disabled="isDisabled">

				<i class="fa fa-unlink"></i> {{trans('detach')}}
			</button>
		</div>
	</modal>
</template>

<script>
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'asset-associate-detach',

		description : 'Asset Associate Detach Modal component',

		props:{
			
			showModal : { type : Boolean, default : false },

			assetId : { type : String | Number , default : ''},

			associateId : { type : String | Number , default : ''},

			category : { type : String , default : ''},

			onClose : { type : Function },

			compName : { type : String , default : ''},
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

				let type = '';

				if(this.category == 'ticket') {

					type = 'tickets';
				
				} else if (this.category == 'problem') {
					
					type = 'sd_problem'
				
				} else {
					
					type = 'sd_'+this.category+'s'
				}
				
				const params = { type : type, type_ids : this.associateId};

				axios.delete('/service-desk/api/detach-asset-services/'+this.assetId, { data : params}).then(res=>{
					
					successHandler(res,'asset-view');

					window.eventHub.$emit('updateAssetActivityLog','activity');
	        
	        		this.onClose()
					
					this.loading = false;
					
					this.isDisabled = true;
			
				}).catch(err => {
				
					this.loading = false;
					
					this.isDisabled = false;

					this.onClose()

					errorHandler(err,'asset-view')
				})
			}
		},

		components:{
		
			'modal':require('components/Common/Modal.vue'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css" scoped>

	.asset_detach_modal_title{
		margin-left:16px; 
	}
	
	.detach_btn {
    background-color: #c9302c !important;
    border-color: #ac2925 !important;
	}
</style>