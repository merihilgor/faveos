<template>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{trans('detach')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">

			<h5 id="H5">{{trans('are_you_sure')}}</h5>
		</div>

		<div v-if="loading" class="row" slot="fields" >

		  <loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

			 	<i class="fa fa-unlink" aria-hidden="true"></i> {{trans('detach')}}
			 </button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'contract-asset-detach',

		description : 'Contract Asset Detach Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			assetId:{type:String|Number , default : ''},

			compName:{type:String , default : ''},

			alertCompName : {type:String , default : 'contractAssociates'},

			contractId:{type:String|Number , default : ''},

			onClose:{type: Function}
		},

		data(){

			return {

			 	isDisabled:false,

			 	containerStyle:{ width:'500px' },

			 	loading:false,
		  	}
	 	},

		methods:{

			onSubmit(){

				this.loading = true

				this.isDisabled = true

				axios.delete('/service-desk/api/contract-detach-asset/'+this.contractId+'/'+this.assetId).then(res=>{
			
	 	 		successHandler(res,this.alertCompName);

	 	 		window.eventHub.$emit('updateContractAssociates');
		  
		  		this.onClose();

				this.loading = false;

		  		this.isDisabled = true

		  	}).catch(err => {

		  		this.onClose();
		  		
			 	this.loading = false;

			 	this.isDisabled = true

				errorHandler(err,this.alertCompName)
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