<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose">

		<div slot="title">

			<h4>{{trans(identifier)}}</h4>
		</div>

		<div slot="fields" v-if="!loading" id="release_desc">

			<div class="row">

				<ck-editor :value="description" type="text" :onChange="onChange" name="description" :label="trans('description')"
					classname="col-sm-12" :required="true" :lang="'en'">
					
				</ck-editor>
       
	       	<div>
						
					<file-upload :label="trans('attachments')" :value="attachment" name="attachment" 
						:onChange="onFileSelected" classname="col-xs-6">
					
					</file-upload>			
				</div>
		   </div>
		</div>

		<div v-if="loading" class="row" slot="fields" >

     		<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

       		<i class="fa fa-refresh"></i> {{trans('update')}}
       	</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from "axios"

	export default {

		name : 'release-planning',

		description : 'Release Planning Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

	      releaseId : { type : String | Number, default : ''},

	      identifier : { type : String , default : ''},
		},

		data(){

			return {

				attachment : '',

				description : '',

				isDisabled : false,

				loading : true,

				selectedFile : '',

				attachment_delete : false
			}
		},

		beforeMount(){

			this.getInitialValues();
		},

		methods : {

			getInitialValues() {

				axios.get('/service-desk/api/general-popup/' + this.releaseId + '/sd_releases/' + this.identifier).then(res=>{

					this.loading = false;

					this.general_info = res.data.data.general_info;

					if(this.general_info){

						this.description = this.general_info.description

						this.attachment = this.general_info.attachment
					}
				}).catch(error=>{

					this.loading = false;
				})
			},

			onFileSelected(value,name,action){

				this.selectedFile = value;

				this.attachment_delete = action;
			},

			onChange(value, name) {

				this[name] = value;

			},

			onSubmit() {

				if(this.description != ''){

					this.loading = true 

					this.isDisabled = true 

					var fd = new FormData();

					fd.append('identifier',this.identifier);

					fd.append('description', this.description)
					
					if(this.selectedFile){
						
						fd.append('attachment', this.selectedFile)
					}

					if(this.attachment_delete){
						
						fd.append('attachment_delete', this.attachment_delete)
					}
				
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/general-popup/'+this.releaseId +'/sd_releases', fd,config).then(res => {

						this.loading = false

						this.isDisabled = false 
						
						window.eventHub.$emit('updateReleaseAssociates');

						this.onClose();

						successHandler(res,'release-view')
						
					}).catch(err => {
						
						this.loading = false;

						this.isDisabled = false; 

						this.onClose();
						
						errorHandler(err,'release-view')
					});
				} else {
					
					this.$store.dispatch('setValidationError',{description: "This field is required."})
				}
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

	      'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),

	      'file-upload': require('components/MiniComponent/FormField/fileUpload.vue'),

	      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css">
  #release_desc{
    margin-left: 15px;
    margin-right: 15px;
    height: 300px;
    overflow-y: auto;
    overflow-x: hidden; 
  }
</style>