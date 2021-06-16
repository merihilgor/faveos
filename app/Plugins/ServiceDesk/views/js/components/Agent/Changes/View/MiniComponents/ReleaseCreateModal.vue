<template>

  <div>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang('release')}}</h4>
			</div>

			<div v-if="!loading" slot="fields" id="release_fields">

				<release from="modal" ref="releaseCreate" :change_id="changeId" :onComplete="onCompleted" alertName="changes-view">
					
				</release>
			</div>
		
			<div v-if="loading" class="row" slot="fields" >

        <loader :animation-duration="4000" :size="60"/>
			</div>

			<div slot="controls">

				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

          <i class="fa fa-save"></i> {{lang('save')}}</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'release-add-modal',

		description : 'Release add Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			changeId:{type:String|Number , default : ''},

			onClose:{type: Function},
		},

		data(){

      return {

		    isDisabled:false,

			  containerStyle:{ width:'800px' },

			  loading:false,

			}
    },

		methods:{

			onSubmit(){

        this.$refs.releaseCreate.onSubmit();
			},

			onCompleted(){

				window.eventHub.$emit('ChangeAssociatesAction','releases');

				window.eventHub.$emit('updateChangeData');

				 window.eventHub.$emit('updateActivity');
				 
				this.onClose();
			}
		},

	components:{

		'modal':require('components/Common/Modal.vue'),

    'loader':require('components/Client/Pages/ReusableComponents/Loader'),

    'release' : require('../../../../../components/Agent/Release/ReleaseCreateEdit.vue')
	}

};
</script>

<style type="text/css">
		
	#release_fields {
		margin-left: 15px;
    margin-right: 15px;
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
	}
</style>