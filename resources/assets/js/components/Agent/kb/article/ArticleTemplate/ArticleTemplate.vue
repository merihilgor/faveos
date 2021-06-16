<template>

	<div>

		<div class="row" v-if="hasDataPopulated === false || loading === true">

			<custom-loader :duration="loadingSpeed"></custom-loader>

		</div>

		<alert componentName="template-create"/>

		<div class="box box-primary" v-if="hasDataPopulated === true">

			<div class="box-header with-border">
				
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 class="box-title">{{lang(title)}}</h2>
					</div>
	
					<div class="col-md-8">
	
						<div class="dropdown pull-right">
							
							<button v-if="buttonVisible" id="delete_template" class="btn btn-danger" type="button" @click="showModal = true">
							
								<span class="fa fa-trash">  </span> {{lang('delete')}}	
							</button>
						</div>
					</div>
				</div>
			</div>
					
			<div class="box-body">
		
				<div class="row">
			
					<text-field label="Name" :value="name" type="text" name="name" :onChange="onChange" classname="col-xs-6" :required="true">
						
					</text-field>
			
					<radio-button :options="radioOptions" :label="lang('status')" name="status" :value="status":onChange="onChange" classname="form-group col-xs-6" >
						
					</radio-button>
			
				</div> 
			
				<div class="row">
			
					<!-- <ck-editor-with-validation :description="description" :classname="classname" :show_error="show_err" :noDropdown="true"></ck-editor-with-validation> -->

					<ck-editor :value="description" type="text" :onChange="onChange" name="description" :label="lang('description')"
						classname="col-sm-12" :required="true" :lang="'en'">
						
					</ck-editor>
			
				</div>

					
				<button type="button" id="submit_btn" v-on:click="onSubmit()" :disabled="loading" class="btn btn-primary page-btn">
						
					<span :class="iconClass"></span>&nbsp;{{lang(btnName)}}
					
				</button>

				
			</div>
		
		</div>

		<transition  name="modal">

			<template-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :apiUrl="'/article/deletetemplate/'+template_id"
				alert="template-create" redirectUrl="/article/alltemplate/list">

			</template-modal>
		</transition>

	</div>

</template>

<script>

	import axios from "axios";
	
	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validateArticleTemplateSettings } from "helpers/validator/articleTemplateCreateRules";
	
	import {getIdFromUrl} from 'helpers/extraLogics';
	
	import { mapGetters } from 'vuex'

	export default {

		name : 'article-template',

		description : 'Article Template component',

		data(){
			return {

				name : '',

				status : 1,

				description : '',

				radioOptions:[{name:'active',value:1},{name:'inactive',value:0}],

				//ck editor
				classname : 'form-group',

				show_err : false,
				
				title :'create_new_article_template', 

				iconClass:'fa fa-save',

				btnName:'save',

				loading: false,//loader status
				
				loadingSpeed: 4000,
				
				hasDataPopulated: true,

				template_id :'',

				buttonVisible : false,

				showModal : false
			}
		},
		
		beforeMount() {

			const path = window.location.pathname;
			
			this.getValues(path);
		},

		methods : {

			getValues(path){

				const templateId = getIdFromUrl(path);
			
				if (path.indexOf("edit") >= 0) {
					
					this.title = 'edit_article_template';
							
					this.iconClass = 'fa fa-refresh'
					
					this.btnName = 'update'

					this.hasDataPopulated = false;

					this.buttonVisible = true;
					
					this.getInitialValues(templateId);
				 
				} else {

					this.loading = false;

					this.hasDataPopulated = true
				}
			},

			getInitialValues(id) {

				this.loading = true;			
				
				axios.get('/api/articletemplate/edit/'+id).then(res => {

					this.template_id  = id
					
					this.hasDataPopulated = true;
					
					this.loading = false;

					this.updateStatesWithData(res.data.message.template);
				
				}).catch(err => {
					
					errorHandler(err)
					
					this.hasDataPopulated = true;
					
					this.loading = false;
				
				});
			
			},
			
			updateStatesWithData(templateData) {
				
				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(templateData).map(key => {

					if (stateData.hasOwnProperty(key)) {
					
						self[key] = templateData[key];
					
					}
				
				});
			
			},
			
			isValid() {

				const { errors, isValid } = validateArticleTemplateSettings(this.$data);
			
				if (!isValid) {
			
					return false;
			
				}
			
					return true;
			
			},

			onChange(value, name) {
				this[name] = value;
	
			},
			
			getDescription(){
				return CKEDITOR.instances['reply_content'].getData();
			
			},

			onSubmit() {
			
				if (this.isValid()) {
			
					this.loadingSpeed = 8000;
			
					this.loading = true;
			
					const data = {};
			
					if(this.template_id != ''){

						data['id'] = this.template_id;
					
					}

					data['name'] = this.name;

					data['status'] = this.status;

					data['description'] = this.description;

					axios.post('/article/post/template', data).then(res => {

						this.loading = false;
						
						successHandler(res,'template-create');
						
						if(this.template_id === ''){
						
							this.redirect('/article/alltemplate/list')
						
						} else {

							this.getInitialValues(this.template_id)
						}
					
					}).catch(err => {
					
						this.loading = false;
					
						errorHandler(err,'template-create');
					
					});
				
				}
			
			},

			onClose(){
			
				this.$store.dispatch('unsetValidationError');
			
				this.showModal = false
			},

		},

		components:{
			
			"text-field": require("components/MiniComponent/FormField/TextField"),
			
			// "ck-editor-with-validation": require("components/MiniComponent/FormField/CkEditorWithValidation"),	

			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),

			'alert': require("components/MiniComponent/Alert"),
			
			"custom-loader": require("components/MiniComponent/Loader"),
			
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
			
			'template-modal': require('components/Agent/kb/KbDeleteModal')
		}
	
	};
</script>

<style scoped>
	.page-btn {
		margin: 20px 0 30px 0px;
	}
</style>