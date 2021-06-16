<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose">

		<div slot="title"><h4>{{lang(identifier)}}</h4></div>

		<div slot="fields" v-if="!loading" id="change_desc">

			<div class="row">

				<text-field v-if="identifier == 'solution'" :label="lang('solution_title')" :value="solution_title" type="text" name="solution_title"
					:onChange="onChange" classname="col-xs-12" :required="false">
						
				</text-field>

				<ck-editor :value="description" type="text" :onChange="onChange" name="description" :label="lang('description')"
					classname="col-sm-12" :required="true" :lang="'en'">
						
				</ck-editor>
				
				<div>
							
					<file-upload :label="lang('attachments')" :value="attachment" name="attachment" 
						:onChange="onFileSelected" classname="col-xs-12">
								
					</file-upload>				
				</div>
		  	</div>
		</div>

		<div v-if="loading" class="row" slot="fields" >

		  	<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

			 	<i class="fa fa-refresh"></i> {{lang('update')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from "axios"

	import { validateProblemPlanningSettings } from "../../../../validator/problemPlanningValidation.js";

	export default {

		name : 'problem-planning-modal',

		description : 'Problem Planning Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

			problemId : { type : String | Number, default : ''},

			identifier : { type : String , default : ''},

			updateChangeData : { type  : Function }
		},

		data(){

			return {

				attachment : '',

				description : '',

				isDisabled : false,

				loading : true,

				selectedFile : '',

				attachment_delete : false,

				solution_title : '',
			}
		},

		beforeMount(){

			this.getInitialValues();
		},

		methods : {

			getInitialValues() {

				axios.get('/service-desk/api/general-popup/' + this.problemId + '/sd_problem/' + this.identifier).then(res=>{

					this.loading = false;

					this.general_info = res.data.data.general_info;

					if(this.general_info){

						this.description = this.general_info.description

						this.attachment = this.general_info.attachment

						this.solution_title = this.general_info.title;
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

			isValid() {

				const { errors, isValid } = validateProblemPlanningSettings(this.$data);
				
				return isValid;
			},

			onSubmit() {

				if(this.isValid()){

					this.loading = true 

					this.isDisabled = true 

					var fd = new FormData();

					fd.append('identifier',this.identifier);

					fd.append('description', this.description);

					if(this.identifier == 'solution') {

						fd.append('title',this.solution_title)
					}
					
					if(this.selectedFile){
						
						fd.append('attachment', this.selectedFile)
					}

					if(this.attachment_delete){
						
						fd.append('attachment_delete', this.attachment_delete)
					}
				
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/general-popup/'+this.problemId +'/sd_problem', fd,config).then(res => {

						successHandler(res,'problem-view');
						
						this.loading = false

						this.isDisabled = false 
						
						this.onClose();

						window.eventHub.$emit('updateProblemAssociates');
						
					}).catch(err => {
						
						this.loading = false

						this.isDisabled = false 
						
						errorHandler(err,'problem-view')
					});
				}
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),

			'text-field':require('components/MiniComponent/FormField/TextField'),

			'file-upload': require('components/MiniComponent/FormField/fileUpload.vue'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css">
  	#change_desc{
		margin-left: 15px;
	 	margin-right: 15px;
		height: 300px;
	 	overflow-y: auto;
	 	overflow-x: hidden; 
  	}
</style>