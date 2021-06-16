<template>

	<div>

		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="release-create-edit"/>

		<div :class="[{'box box-primary' : !from}]" v-if="hasDataPopulated">

			<div v-if="!from" class="box-header with-border">
			
				<div class="row">
					
					<div class="col-md-4">
					
						<h2 class="box-title">{{lang(title)}}</h2>
					</div>
					
					<div class="col-md-8">
					
						<div class="dropdown pull-right">
					
							<a v-if="release_id" id="view_release" class="btn btn-primary" target="_blank"
								:href="basePath()+'/service-desk/releases/'+release_id+'/show'">
								
								<span class="fa fa-eye"> </span> {{lang('view')}}
							</a>
						</div>
					</div>
				</div>
			</div>

			<div class="box-body">
				
				<div class="row">

					<text-field :label="lang('subject')" :value="subject" 
						type="text" 
						name="subject" 
						:onChange="onChange" 
						classname="col-xs-6" 
						:required="true"
						>
					</text-field>

					<text-field :label="lang('identifier')" :value="identifier" 
						type="text" 
						name="identifier" 
						:onChange="onChange" 
						classname="col-xs-6" 
						:required="false"
						>
					</text-field>
				</div>

				<div class="row">
					
					<date-time-field :label="lang('select_planned_date_range')"
						:value="planned_date" 
						type="datetime"
						name="planned_date"
			          :onChange="onChange" 
			          range 
			          :required="false" 
			          format="MMMM Do YYYY, h:mm a" 
			          classname="col-xs-6"
			          :clearable="true" 
			          :disabled="false" 
			          :editable="true"
			          :currentYearDate="false" 
			          :time-picker-options="timeOptions"
			          :confirm="true"
			          >
			      </date-time-field>

					<dynamic-select :label="lang('status')" 
						:multiple="false" 
						name="status" 
						:prePopulate="true"
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/release_statuses" 
						:value="status" 
						:onChange="onChange" 
						:required="true"
						:clearable="status ? true : false"
						>
					</dynamic-select>
				</div>

				<div class="row">
					
					<dynamic-select :label="lang('priority')" 
						:multiple="false" 
						name="priority" 
						:prePopulate="true"
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/release_priorities" 
						:value="priority" 
						:onChange="onChange" 
						:required="true"
						:clearable="priority ? true : false"
						>
					</dynamic-select>

					<dynamic-select :label="lang('release_type')" 
						:multiple="false" 
						name="releaseType" 
						:prePopulate="true"
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/release_types" 
						:value="releaseType" 
						:onChange="onChange"
						:required="true"
						:clearable="releaseType ? true : false"
						>
					</dynamic-select>
				</div>

				<div class="row">

					<dynamic-select :label="lang('location')" 
						:multiple="false" 
						name="location" 
						:prePopulate="true"
						classname="col-xs-6" 
						apiEndpoint="/api/dependency/locations" 
						:value="location" 
						:onChange="onChange"
						:clearable="location ? true : false"
						>
					</dynamic-select>

					<dynamic-select v-if="show_attach_assets" :label="lang('attach_assets')" 
						:multiple="true" 
						name="attach_assets" 
						:prePopulate="true"
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/assets" 
						:value="attach_assets" 
						:onChange="onChange"
						>
					</dynamic-select>
				</div>

				<div class="row">
					
					<ck-editor :value="description" type="text" :onChange="onChange" name="description" :label="lang('description')"
						classname="col-sm-12" :required="true" :lang="'en'">
						
					</ck-editor>
				</div>

				<div class="row">

					<file-upload :label="lang('attachments')" 
						:value="attachment" 
						name="attachment" 
						:onChange="onFileSelected" 
						classname="col-xs-6" 
						:id="release_id">
					</file-upload>
				</div>
			</div>

			<div v-if="!from" class="box-footer">

				<button id="submit_btn" class="btn btn-primary" @click="onSubmit()">
					
					<i :class="iconClass"></i> {{lang(btnName)}}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import  { getIdFromUrl } from 'helpers/extraLogics';

	import { validateReleaseSettings } from "../../../validator/releaseValidation.js";

	import { mapGetters } from 'vuex'

	import moment from 'moment'

	export default {

		name : 'release-create-edit',

		description  : 'Releease create edit page',

		props : {

			from : { type : String, default : ""},

			change_id : { type : String | Number, default : ""},

			onComplete : { type : Function },

			alertName : { type : String, default : 'release-create-edit'}
		},

		data(){

			return {

				title : 'create_release',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				release_id : '',

				subject : '',

				description : '',

				planned_date : '',

				planned_start_date : '',
				
				planned_end_date : '',

				status : '',

				location : '',

				priority : '',

				attach_assets : '',

				attachment : '',

				releaseType : '',

				show_attach_assets : true,

				timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },

				selectedFile : '',

				attachment_delete : false,

				identifier : ''
			}
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		watch : {

		},

		beforeMount(){

			this.getActions();

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods : { 

			getActions(){
				
				axios.get('/service-desk/get/permission/api').then(res=>{
					
					this.show_attach_assets = res.data.data.actions.attach_asset;

				}).catch(error=>{
					
					this.show_attach_assets = true;
				})	
			},

			getValues(path){

				const releaseId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_release'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(releaseId)

				} else {

					this.loading = false;

					this.hasDataPopulated = true;
				}
			},

			getInitialValues(id){

				this.selectedFile = '';
				
				this.loading = true
				
				axios.get('/service-desk/api/release/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					let releaseData = res.data.data.release;

					this.updateStatesWithData(releaseData)

					this.releaseType = releaseData.release_type;

					this.release_id = releaseData.id;
					
					if(releaseData.planned_start_date){
						
						this.planned_date =[this.formattedTime(releaseData.planned_start_date), this.formattedTime(releaseData.planned_end_date)];

					}

					this.attachment = releaseData.attachment[0];
				
				}).catch(error=>{

					this.loading = false;
				});
			},

			updateStatesWithData(data){

				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(data).map(key => {
					
					if (stateData.hasOwnProperty(key)) {
					
						self[key] = data[key];
					}
				});
			},

			isValid(){

				const { errors, isValid } = validateReleaseSettings(this.$data);
				
				if (!isValid) {
				
					return false;
				}
				
				return true;
			},

			onFileSelected(value,name,action){

				this.selectedFile = value;

				this.attachment_delete = action;
			},

			onChange(value, name) {

				this[name] = value;

				if(value === null){

					this[name] = '';
				}

				if(name === 'planned_date'){

					this.planned_start_date = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD+HH:mm:ss') : '';

					this.planned_end_date =  value[1] !== null ? moment(value[1]).format('YYYY-MM-DD+HH:mm:ss') : '';

        }
			},

			onSubmit(){

				if(this.isValid()){

					this.loading = true 

					var fd = new FormData();
					
					if(this.release_id != ''){
					
						fd.append('id',this.release_id);
					}

					if(this.from){

						fd.append('change_id',this.change_id);
					}
					
					fd.append('subject', this.subject)

					fd.append('identifier', this.identifier)

					fd.append('description', this.description)

					fd.append('status_id', this.status.id)

					fd.append('priority_id', this.priority.id)
					
					fd.append('release_type_id', this.releaseType.id)
					
					fd.append('location_id', this.location ? this.location.id : '')
					
					if(this.selectedFile){
						
						fd.append('attachment', this.selectedFile)
					}

					if(this.attachment_delete){
						
						fd.append('attachment_delete', this.attachment_delete)
					}
					
					if(this.planned_start_date){
						
						fd.append('planned_start_date', this.planned_start_date)
					}

					if(this.planned_end_date){

						fd.append('planned_end_date', this.planned_end_date)
					}

					if(this.attach_assets !== ''){
						
						for(var i in this.attach_assets){
						
							fd.append('asset_ids['+i+']', this.attach_assets[i].id);
						}
					}
				
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/release', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,this.alertName)
						
						if(!this.from){

							if(!this.release_id){
						
							this.redirect('/service-desk/releases')
						
							} else{

								this.getInitialValues(this.release_id)
							}
						} else {

							this.onComplete();
						}

					}).catch(err => {

						this.loading = false
						
						errorHandler(err,'release-create-edit')					
					});
				}
			},
		},

		components  : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),

			'file-upload': require('components/MiniComponent/FormField/fileUpload.vue'),

			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
		}
	};
</script>