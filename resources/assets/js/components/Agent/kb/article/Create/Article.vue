<template>
	<div>
		
		<div class="row" v-if="hasDataPopulated === false || loading === true">
		
			<custom-loader :duration="loadingSpeed"></custom-loader>
		</div>
		
		<alert componentName="article-create"/>

		<div class="box box-primary" v-if="hasDataPopulated === true">
			
			<div class="box-header with-border">

				<div class="row">
			
					<div class="col-md-4">
		
						<h2 class="box-title">{{lang(title)}}</h2>
					</div>
			
					<div class="col-md-8">
			
						<div class="dropdown pull-right">
			
							<a v-if="buttonVisible" id="view_article" class="btn btn-primary" 
								:href="type === 1 ? basePath()+'/show/'+link :'javascript:;'" 
								:target="type === 0 ? '' : '_blank'" :disabled="type === 0">
								
								<span class="fa fa-eye"></span> {{lang('view')}}
							</a>

							<button v-if="buttonVisible" id="delete_article" 
								class="btn btn-danger" type="button" @click="modalMethod('delete_article')">
								
								<span class="fa fa-trash">  </span> {{lang('delete')}}
							</button>
							
							<template v-if="!buttonVisible">
								
								<button id="add_category" class="btn btn-primary" type="button" @click="modalMethod('new_category')">
									
									<span class="fa fa-plus">  </span> {{lang('add_category')}}
								</button>

								<button id="add_tag" class="btn btn-primary" type="button" @click="modalMethod('new_tag')">
									
									<span class="fa fa-plus">  </span> {{lang('add_tag')}}
								</button>
							</template>
						</div>
					</div>
				</div>
			</div>

			<div class="box-container">	
			
				<div class="box-header with-border">
			
					<h3 class="box-title">{{lang('article_info')}}</h3>
				</div>
			
				<div class="box-body">
			
					<div class="row">
			
						<text-field label="Name" :value="name" type="text" 
							name="name" :onChange="onChange" 
							classname="col-xs-6" :required="true">
						</text-field>
						
						<perma-link :label="lang('perma_link')" :value="slug" 
							name="slug" :onChange="onChange" 
							classname="col-xs-6" :required="true" :link="link">
						</perma-link>
					</div>
					
					<div class="row">
					
						<dynamic-select :label="lang('category')" 
							:multiple="true" name="category" 
							classname="col-xs-4" apiEndpoint="/api/dependency/categories" 
							:value="category" :onChange="onChange" :required="true" strlength="30">
						</dynamic-select>

						<dynamic-select :label="lang('author')" 
							:multiple="false" name="author" 
							classname="col-xs-4" apiEndpoint="/api/dependency/agents?meta=true" 
							:value="author" :onChange="onChange" :required="true" :clearable="author ? true : false" strlength="30">
						</dynamic-select>

						<dynamic-select :label="lang('tags')" 
							:multiple="true" name="tag_id" 
							classname="col-xs-4" apiEndpoint="/api/dependency/tags" 
							:value="tag_id" :onChange="onChange" :required="false" strlength="30" :hint="lang('relevant_tags_search')">
						</dynamic-select>
					</div>

				<div class="row">

					<add-media :description="description" :classname="classname" :show_error="show_err"  page_name="kb" 
						name="description" chunkApi="/chunk/upload/public" filesApi="/media/files/public" :onInput="onChange">
					
						<button slot="template" class="btn btn-default" @click="() => showTemplateModal = true"><i class="fa fa-align-left" aria-hidden="true"></i> {{lang('select_template')}}</button>
					</add-media>
				</div>
			</div>
		</div>

		<div class="box-container">
			
			<div class="box-header with-border">
			
				<h3 class="box-title">{{lang('publish')}}</h3>
			</div>
			
			<div class="box-body">
			
				<div class="row">
			
					<static-select :label="lang('visibility')"  
						:elements="visibility_options" name="visible_to" :value="visible_to" 
						classname="col-xs-4" :onChange="onChange" :required="true">
					</static-select>

					<date-time-field :label="lang('publish_immediately')" 
						:value="publish_time" type="datetime" name="publish_time" 
						:onChange="onChange" :time-picker-options="timeOptions" 
						:required="true" format="MMMM Do YYYY HH:mm" classname="col-xs-4" 
						:clearable="false" :disabled="false">
					</date-time-field>

					<radio-button :options="radioOptions" :label="lang('status')" 
						name="type" :value="type"
						:onChange="onChange" classname="form-group col-xs-4" >
					</radio-button>
				</div>
			</div>
		</div>

		<div class="box-container">
			
			<div class="box-header with-border">
			
				<h3 class="box-title">{{lang('seo')}}</h3>
			</div>
			
			<div class="box-body">
			
				<div class="row">
			
					<text-field :label="lang('seo_title')" :value="seo_title" 
						type="text" name="seo_title" :onChange="onChange" 
						classname="col-xs-12" :required="false">
					</text-field>

					<div class="progress sm col-md-12" id="prog"><div :class="seoClass" :style="seoStyle"></div></div>
				</div>
				
				<div class="row">
					
					<text-field :label="lang('description')" :value="meta_description" 
						type="textarea" name="meta_description" :onChange="onChange" 
						classname="col-xs-12" :required="false">
					</text-field>

					<div class="progress sm col-md-12" id="prog"><div :class="seoDesClass" :style="seoDesStyle"></div></div>
				</div>
			</div>
		</div>

		<button type="button" v-on:click="onSubmit()" :disabled="loading" class="btn btn-primary page-btn">
			
			<span :class="iconClass"></span>&nbsp;{{lang(btnName)}}
		</button>
		
		<transition  name="modal">
			
			<article-modal :category="category" v-if="showModal" 
				:newCategory="newCategory" :onClose="onClose" :newTag="newTag"
				:showModal="showModal" :title="modal_title" :id="link">
			</article-modal>
		</transition>

		<transition name="modal">
			<template-modal v-if="showTemplateModal" :onClose="onTemplateModalClose" :showModal="showTemplateModal" :modal-title="lang('select_template')"></template-modal>
		</transition>
	</div>
</div>
</template>

<script>

	import axios from "axios";
	
	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validateArticleSettings } from "helpers/validator/articleCreateRules";
	
	import {getIdFromUrl} from 'helpers/extraLogics';
	
	import { mapGetters } from 'vuex'

	import moment from 'moment'
	
	export default {
	
		name: "article-page",
	
		description: "article create and edit page",
	
		data: () => ({
	
			showModal: false,//modal status
	
			name: "",//typed article name
	
			link: "", //article link
	
			category: [], //selected category
	
			author: '', //selected author
	
			template: '', //selected author
	
			loading: false,//loader status
	
			loadingSpeed: 4000,
	
			hasDataPopulated: false,
	
			slug:'',//article slug
	
			radioOptions:[{name:'published',value:1},{name:'draft',value:0}],//article status
	
			type : 1,//article status
	
			visibility_options:[{ id:"all_users",name:"All Users" },{ id:"logged_in_users",name:"Logged in Users" },{ id:"agents",name:"Agents" },{ id:"logged_in_users_and_agents",name:"Logged in Users And Agent" }],
	
			visible_to: "",
	
			buttonVisible : false,//buttons config for create and edit page
	
			publish_time: new Date(),
	
			timeOptions : { start: '00:00', step: '00:30', end: '23:30' },//date picker time options
	
			seo_title :'',
	
			meta_description :'',
	

			description : '',
	

			seoDesStyle : { width : "0%"},//progress bar width
	
			seoDesClass : '',//progress bar class
	
			seoStyle : { width : "0%"},//progress bar width
	
			seoClass : '',//progress bar class
	
			classname : 'form-group',//ckeditor class
	
			progStart : 'progress-bar progress-bar-primary progress-bar-striped',//progress bar class
	
			progWarn : 'progress-bar progress-bar-warning progress-bar-striped',//progress bar class
	
			progSuc : 'progress-bar progress-bar-success progress-bar-striped',//progress bar class
	
			progDan : 'progress-bar progress-bar-danger progress-bar-striped',//progress bar class
	
			show_err : false,//ckeditor error class
	
			title :'create_new_article', iconClass:'fa fa-save',btnName:'save',//page title and btn classes for create and edit page
			article_id :'',
	
			modal_title :'',

			tag_id : [],

			showTemplateModal: false
		}),
	
		computed:{
	
					...mapGetters(['getUserData'])
		},
	
		watch:{
	
			getUserData(newValue,oldValue){
	
				const location = window.location.pathname.split('/');      	 
	
				if(location[location.length-1] !== 'edit') {
	
					this.author = {
	
						id : newValue.user_data.id, 
	
						name: newValue.user_data.first_name + ' ' + newValue.user_data.last_name, 
				
						email : newValue.user_data.email, 
						
						profile_pic : newValue.user_data.profile_pic 
					};
				}

				return newValue
			}
		},

		beforeMount() {
			
			const path = window.location.pathname;

			this.getValues(path);
		},

		created() {
			window.eventHub.$on('applyTemplate', this.applyTemplate);
		},
		
		methods: {

			getValues(path){

				const articleId = getIdFromUrl(path);
			
				if (path.indexOf("edit") >= 0) {
			
					this.title = 'edit_article';
			
					this.iconClass = 'fa fa-refresh'
			
					this.btnName = 'update'
			
					this.buttonVisible = true
			
					this.hasDataPopulated = false;
			
					this.getInitialValues(articleId);
				} else {

					this.loading = false;

					this.hasDataPopulated = true
				}
			},
		
			getInitialValues(articleId) {
		
				this.loading = true;			
		
				axios.get('/api/edit/article/'+articleId).then(res => {
					
					this.loading = false;

					this.hasDataPopulated = true;

					this.article_id  = articleId
		
					this.updateStatesWithData(res.data.message);

					this.tag_id = res.data.message.article.tags;
		
				}).catch(err => {			
					
					this.loading = false;

					errorHandler(err)
				});
			},
		
			updateStatesWithData(articleData) {
			
				var articleDetails = articleData.article
		
				this.link = articleDetails.slug
		
				this.seoTitleProgress(articleDetails.seo_title)
		
				this.metaProgress(articleDetails.meta_description);
		
				const self = this;
		
				const stateData = this.$data;

				Object.keys(articleData.article).map(key => {
		
					if (stateData.hasOwnProperty(key)) {
		
						self[key] = articleData.article[key];
					}
				});

				this.seo_title = articleDetails.seo_title === '' ? articleDetails.name : articleDetails.seo_title

				this.meta_description = articleDetails.meta_description === '' ? articleDetails.description : articleDetails.meta_description
			},
		
			isValid() {
		
				const { errors, isValid } = validateArticleSettings(this.$data);
		
				if (!isValid) {
		
					return false;
				}
					return true;
			},
			
			modalMethod(name){
			
				this.showModal = true
			
				this.modal_title = name
			
				this.$store.dispatch('unsetValidationError');
			},	
			
			onSubmit() {
			
				this.classname = this.description === '' ? 'form-group has-error'  : 'form-group'
			
				this.show_err = this.description === '' ? true  : false
			
				if (this.isValid() && this.description !== '') {

					this.loadingSpeed = 8000;
			
					this.loading = true;
			
					var fd = new FormData();
			
					if(this.article_id != ''){
			
						fd.append('articleid', this.article_id);
					}
			
					fd.append('name', this.name);
			
					fd.append('slug', this.link);
			
					fd.append('description', this.description);
			
					fd.append('category_id',this.category.map(a => a.id));

					if(this.tag_id.length > 0){
						
						fd.append('tag_id',this.tag_id.map(a => a.id));								
					}
			
					fd.append('author', this.author.id);
			
					if(this.template != '' && this.template != null ){
			
						fd.append('template', this.template.id);
					} else {
						
						fd.append('template', '');
					}				
			
					fd.append('hour', moment(this.publish_time).hour());
			
					fd.append('minute',moment(this.publish_time).minute());
			
					fd.append('year', moment(this.publish_time).year());
			
					fd.append('day', moment(this.publish_time).format("D"));
			
					fd.append('month', (moment(this.publish_time).month())+1);
			
					fd.append('visible_to', this.visible_to);
			
					fd.append('type', this.type);
			
					fd.append('seo_title', this.seo_title);
			
					fd.append('meta_description', this.meta_description);
			
					const config = { headers: { 'Content-Type': 'multipart/form-data' } };
			
					axios.post('/article', fd,config).then(res => {
			
						this.loading = false;
			
						successHandler(res,'article-create');
			
						if(this.article_id === ''){
							
							this.redirect('/article')
						} else {
			
							this.getInitialValues(this.article_id)
						}
					}).catch(err => {
			
						this.loading = false;
			
						errorHandler(err,'article-create');
					});
				}
			},
			
			newCategory(value){
			
				this.category.push(value);
			},

			newTag(value){
				
				this.tag_id.push(value)
			},
			
			onChange(value, name) {
				
				this[name] = value;
				
				/*
				* Updating article link based on name(Text field) and slug(Text field)
				* By default article link is Article Name... also given option to update the link in Slug field
				* Description : link should not contain any spaces or special characters for that i am using Regex pattern
				*/
				if(name === 'name' || name==="slug"){
			
					var regex = value.replace(/[^\w\s]/gi, '').toLowerCase();
			
					var regex1 = regex.replace(/_+/g,'')
			
					this.link= regex1.replace(/\s+/g,"-");
			
					this.slug = value
				}
			
				if(name === 'seo_title'){ this.seoTitleProgress(value) }
			
				if(name === 'meta_description'){ this.metaProgress(value) }

				if(name === 'author' ){ this[name] = value === null ? '' : value }
			},

			templateData(data){

				this.description = data;
			},
			
			seoTitleProgress(value){
			
				this.seoStyle.width = value.length/0.6+'%'
				
				// SEO title length
				var len = value.length;
				
				/**
				 * progDan(danger (red)), progSuc(success (green)), progWarn(warning (yellow)) => Progress bar classes
				 *
				 * Based on SEO title length this will show Progrees bar
				 * If SEO title length is more than 60 progress bar in red color
				 * If SEO title length is more than 25 and less than or equal to 50 progress bar in green color
				 * If SEO title length is more than 50 and less than or equal to 60 progress bar in yellow color
				 */
				this.seoClass = len > 60 ? this.progDan : 
												len > 25 && len <= 50 ? this.progSuc : 
												len > 50 && len <= 60 ? this.progWarn :  this.progStart;
			},
			
			metaProgress(value){
			
				this.seoDesStyle.width = value.length/1.6+'%'
				
				// SEO description length
				var len = value.length;
				
				/**
				 * progDan(danger (red)), progSuc(success (green)), progWarn(warning (yellow)) => Progress bar classes
				 *
				 * Based on SEO description length this will show Progrees bar
				 * If SEO description length is more than 160 progress bar in red color
				 * If SEO description length is more than 80 and less than or equal to 120 progress bar in green color
				 * If SEO description length is more than 120 and less than or equal to 160 progress bar in yellow color
				 */
				this.seoDesClass = len > 160 ? this.progDan : 
														len > 80 && len <= 120 ? this.progSuc : 
														len > 120 && len <= 160 ? this.progWarn :  this.progStart;
			},
			
			onClose(){
			
				this.$store.dispatch('unsetValidationError');
			
				this.showModal = false
			
			},

			/**
			 * Apply template description to the ckeditor content
			 * data.operation = {
			 * 'append': 'Append the data.data to the ckeditor content',
			 * 'overwrite': 'Overwrite the content of ckeditor with the data.data
			 * }
			 */
			applyTemplate(data) {
				if(data.operation === 'append') {
					this.description += data.data;
				} else if(data.operation === 'overwrite') {
					const isConfirmed = confirm('You are going to overwrite the description. Are you sure?');
					if(!isConfirmed) {
						return;
					}
					this.description = data.data;
				}
				this.onTemplateModalClose();
			},

			onTemplateModalClose() {
				this.showTemplateModal = false;
			}
		},
		components: {
			
			"text-field": require("components/MiniComponent/FormField/TextField"),
			
			"perma-link": require("components/MiniComponent/FormField/PermaLink"),
			
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
			
			"add-media": require("components/MiniComponent/FormField/AddMedia"),
			
			'alert': require("components/MiniComponent/Alert"),
			
			"custom-loader": require("components/MiniComponent/Loader"),
			
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
			
			'static-select':require('components/MiniComponent/FormField/StaticSelect'),
			
			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
			
			'article-modal': require('components/Agent/kb/article/Create/ArticleModal'),

			'template-modal': require('components/Agent/kb/article/Create/TemplateModal')
		}
	};	
</script>
<style scoped>
.page-btn {
	margin: 20px 0 30px 15px;
}
#prog{
	padding: 0px;margin-left: 15px;width: 97%;
}
.box.box-primary{
	padding : 0px !important;
}
</style>