<template>

  <div>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang('status')}}</h4>
			</div>

			<div v-if="!loading" slot="fields">

				<h5 id="H5">{{lang('are_you_sure')}}</h5>
			</div>

			<div v-if="loading" class="row" slot="fields" >

        <loader :animation-duration="4000" :size="60"/>
			</div>

			<div slot="controls">

				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

          <i class="fa fa-check"></i> {{lang('proceed')}}</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'change-status-modal',

		description : 'Change status Modal component',

		props:{

			showModal:{type:Boolean,default:false},

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

			axios.post('/service-desk/api/change-close/'+this.changeId).then(res=>{

        successHandler(res,'changes-view');

        window.eventHub.$emit('updateChangeData');       	
        
        window.eventHub.$emit('updateActivity');
        
        this.onClose();

				this.loading = false;

        this.isDisabled = true

        }).catch(err => {

          this.loading = false;

          this.isDisabled = true

        	errorHandler(err,'changes-view')
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