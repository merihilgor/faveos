<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose">

		<div slot="title">
	
			<h4>{{lang('delete')}}</h4>
		</div>

		<div slot="fields">
				
			<div class="row">
	
				<h5 id="H5" :class="{margin: lang_locale == 'ar'}">{{lang('are_you_sure_you_want_to_delete')}}</h5>
			</div>
		</div>
			
		<div slot="fields" class="row" v-if="loading === true">
	        
	    <custom-loader :duration="4000"></custom-loader>
	  </div>
						
		<div slot="controls">
			
			<button type="button" id="submit_btn"  @click = "onSubmit" class="btn btn-danger" :disabled="isDisabled">
			
				<i class="fa fa-trash"></i> {{lang('delete')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

import {errorHandler, successHandler} from 'helpers/responseHandler'

import axios from 'axios'

	export default {

		name:'kb-delete-modal',

		description:'Kb delete modal Component',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			apiUrl : { type : String , default : ''},

			alert : { type : String , default : ''},
			
			redirectUrl : { type : String , default : ''},
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			"custom-loader": require("components/MiniComponent/Loader")
		},

		data(){
			
			return {

				isDisabled:false,

				loading:false,

				lang_locale:'',
			} 
		},

		methods:{

			onSubmit(){
			
				this.loading = true
			
				this.isDisabled = true
			
				axios.delete(this.apiUrl).then(res=>{
						
						this.loading = false;
					
						this.isDisabled = true

						successHandler(res,this.alert);
				
						this.onClose()
				
						this.redirect(this.redirectUrl)			
			
				}).catch(err => {
					
					this.loading = false

					errorHandler(err)
				})
		},

	}
};
</script>

<style type="text/css">
#H5{
	margin-left: 30px;
    margin-bottom: 28px !important;
}

.fulfilling-bouncing-circle-spinner{
	margin:auto !important;
}
.margin {
	margin-right: 16px !important;
	margin-left: 0px !important;
}
.spin{
	left:0% !important;
	right: 43% !important;
 }
 .center-of-page{
 	left : 8% !important;
 }
</style>