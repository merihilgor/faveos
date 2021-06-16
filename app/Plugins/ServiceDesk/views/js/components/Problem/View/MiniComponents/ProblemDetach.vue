<template>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{lang('detach')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">

			<h5 id="H5">{{lang('are_you_sure')}}</h5>
		</div>

		<div v-if="loading" class="row" slot="fields" >

		  <loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

			 	<i class="fa fa-unlink" aria-hidden="true"></i> {{lang('detach')}}
			 </button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'problem-detach-modal',

		description : 'Problem Detach Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			assetId:{type:String|Number , default : ''},

			changeId:{type:String|Number , default : ''},

			compName:{type:String , default : ''},

			alertCompName : {type:String , default : 'problemAssociates'},

			problemId:{type:String|Number , default : ''},

			onClose:{type: Function},

			updateChangeData : { type  : Function }
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

			let endPoint = this.changeId ? '/service-desk/api/problem-detach-change/'+this.problemId+'/'+this.changeId
																		: '/service-desk/api/problem-detach-asset/'+this.problemId+'/'+this.assetId

			axios.delete(endPoint).then(res=>{
				
		 	 	successHandler(res,'problem-view');

		  		if(this.changeId){

		  			 this.updateChangeData();

		  		} else {

			  		window.eventHub.$emit(this.compName+'refreshData');
		  		}

		  		window.eventHub.$emit('updateProblemAssociates');
		  
		  		this.onClose();

				this.loading = false;

		  		this.isDisabled = true

		  	}).catch(err => {

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