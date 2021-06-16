<template>

  <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{trans('delete')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">

			<h5 id="H5">{{trans('are_you_sure')}}</h5>
		</div>

		<div v-if="loading" class="row" slot="fields" >

     		<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

       	<i class="fa fa-trash" aria-hidden="true"></i> {{trans('delete')}}</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'release-planning-delete-modal',

		description : 'Problem Planning Delete Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			releaseId:{type:String|Number , default : ''},

			onClose:{type: Function},

			identifier : { type : String , default : ''}
		},

		data(){

      return {

		    isDisabled:false,

			  containerStyle:{ width:'500px' },

			  loading:false
		  }
    },

		methods:{

		onSubmit(){

			this.loading = true

			this.isDisabled = true

			axios.delete('/service-desk/api/delete/general-popup/'+ this.releaseId + '/sd_releases/' + this.identifier).then(res=>{

				window.eventHub.$emit('updateReleaseAssociates');
        
        		this.onClose();

        		successHandler(res,'release-view');
        		
				this.loading = false;

        		this.isDisabled = true

        	}).catch(err => {

	         this.loading = false;

	         this.isDisabled = true

	        	errorHandler(err,'release-view')

	        	this.onClose();
			})
		},
	},

	components:{

		'modal':require('components/Common/Modal.vue'),

    	'loader':require('components/Client/Pages/ReusableComponents/Loader'),
	}
};
</script>

<style type="text/css">

  #H5{
  	margin-left:16px;
  }
</style>