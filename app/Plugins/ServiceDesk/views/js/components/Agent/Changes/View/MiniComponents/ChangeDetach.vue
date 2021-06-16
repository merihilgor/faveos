<template>

  <div>

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

          <i class="fa fa-unlink" aria-hidden="true"></i> {{lang('detach')}}</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'detach-modal',

		description : 'Detach Modal component',

		props:{

			showModal:{type:Boolean,default:false},

      assetId:{type:String|Number , default : ''},

      releaseId:{type:String|Number , default : ''},

      compName:{type:String , default : ''},

      alertCompName : {type:String , default : 'changesAssociates'},

			changeId:{type:String|Number , default : ''},

			onClose:{type: Function},
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

			let endPoint = this.releaseId ? '/service-desk/api/change/detach-release/'+this.changeId
																		: '/service-desk/api/change/detach-asset/'+this.changeId+'/'+this.assetId
			axios.delete(endPoint).then(res=>{

        successHandler(res,'changes-view');

        if(this.releaseId){

					window.eventHub.$emit('updateChangeData');  

					window.eventHub.$emit('ChangeAssociatesAction',this.releaseId ? 'releases' : 'assets');

					window.eventHub.$emit('updateActivity');     	
        } else {

	        window.eventHub.$emit(this.compName+'refreshData');
        }
        
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