<template>

  <div>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang('release')}}</h4>
			</div>

			<div v-if="!loading" slot="fields">

				<div class="row">

					<div class="col-xs-12">
						<dynamic-select :label="lang('release')" :multiple="false" name="release_id" :prePopulate="false"
							classname="col-xs-12" apiEndpoint="/service-desk/api/dependency/releases" :value="release_id" 
							:onChange="onChange" :required="true" :clearable="release_id ? true : false">

						</dynamic-select>
					</div>
				</div>
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

		name : 'release-add-modal',

		description : 'Release add Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			changeId:{type:String|Number , default : ''},

			onClose:{type: Function},
		},

		data(){

      return {

		    isDisabled : true,

			  containerStyle:{ width:'500px' },

			  loading:false,

			  release_id : ''
		  }
    },

		methods:{

			onChange(value,name){

				this[name] = value;

				this.isDisabled = value ? false : true;
			},

			onSubmit(){

				this.loading = true

				this.isDisabled = true

				axios.post('/service-desk/api/change/attach-release/'+this.changeId+'/'+this.release_id.id).then(res=>{

	        successHandler(res,'changes-view');

	        window.eventHub.$emit('ChangeAssociatesAction','releases');

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

	    "dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}

	};
</script>

<style type="text/css">

</style>