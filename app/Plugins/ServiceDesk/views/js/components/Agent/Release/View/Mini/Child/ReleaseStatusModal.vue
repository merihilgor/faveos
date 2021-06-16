<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
		<div slot="title">

			<h4>{{trans('status')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">

			<h5 id="H5">{{trans('are_you_sure')}}</h5>
		</div>
			
		<div v-if="loading" class="row" slot="fields" >

			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>
						
		<div slot="controls">

			<button id="submit_btn" type="button" @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">

				<i class="fa fa-check" aria-hidden="true"></i> {{trans('proceed')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'release-status-modal',

		description : 'Release status Modal component',

		props:{
			
			showModal:{type:Boolean,default:false},

			releaseId : { type : String | Number, default : '' },

			updateReleaseData : { type : Function },
	
			onClose:{type: Function},
		},

		data:()=>({
			
			isDisabled:false,
			
			containerStyle:{ width:'500px' },
	
			loading:false
		}),

		methods:{

			onSubmit(){
			
				this.loading = true
			
				this.isDisabled = true

				axios.post('/service-desk/api/complete-release/'+this.releaseId).then(res=>{

					window.eventHub.$emit('updateReleaseAssociates');
					
					this.loading = false
					
					this.onClose();

					successHandler(res,'release-view');

					this.updateReleaseData();
				
				}).catch(err => {

					this.loading = false
						
					this.onClose();

					errorHandler(err,'release-view')
				})
			}
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
	
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>
	
	#H5{margin-left:16px;}
</style>