<template>

  <div>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang('delete')}}</h4>
			</div>

			<div v-if="!loading" slot="fields">

				<h5 id="H5">{{lang('are_you_sure')}}</h5>
			</div>

			<div v-if="loading" class="row" slot="fields" >

        <loader :animation-duration="4000" :size="60"/>
			</div>

			<div slot="controls">

				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

          <i class="fa fa-trash" aria-hidden="true"></i> {{lang('delete')}}</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'change-updates-delte-modal',

		description : 'Change Updates Delete Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			changeId:{type:String|Number , default : ''},

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

			axios.delete('/service-desk/api/delete/general-popup/'+ this.changeId + '/sd_changes/' + this.identifier).then(res=>{

        successHandler(res,'changes-view');

        window.eventHub.$emit('ChangeAssociatesAction','details')
        
        window.eventHub.$emit('updateActivity');

        window.eventHub.$emit('cabActionPerformed');

				window.eventHub.$emit('changeUpdatePerformed')
        
        this.onClose();

				this.loading = false;

        this.isDisabled = true

        }).catch(err => {

          this.loading = false;

          this.isDisabled = true

        	errorHandler(err,'changes-view')

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