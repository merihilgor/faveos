<template>
	
	<div>

		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>
		</div>

		<alert componentName="change"/>

		<div :class="[{'box box-primary' : !from}]" v-if="hasDataPopulated">

			<div v-if="!from" class="box-header with-border">
			
				<div class="row">
					
					<div class="col-md-4">
					
						<h2 class="box-title">{{lang(title)}}</h2>
					</div>
					
					<div class="col-md-8">
					
						<div class="dropdown pull-right">
					
							<a v-if="change_id" id="view_change" class="btn btn-primary" 
								:href="basePath()+'/service-desk/changes/'+change_id+'/show'">
								
								<span class="fa fa-eye"></span> {{lang('view')}}
							</a>
						</div>
					</div>
				</div>
			</div>

			<div class="box-body">
				
				<div class="row">

					<text-field :label="lang('subject')" :value="subject" type="text" name="subject" :onChange="onChange" 
						classname="col-xs-6" :required="true">
					
					</text-field>

					<text-field :label="lang('identifier')" :value="identifier" type="text" name="identifier" 
						:onChange="onChange" classname="col-xs-6" :required="false">

					</text-field>
				</div>

				<div class="row">
					
					<dynamic-select :label="lang('requester')" :multiple="false" name="requester" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/users?meta=true" :value="requester" 
						:onChange="onChange" :required="true" :clearable="requester ? true : false">

					</dynamic-select>

					<dynamic-select :label="lang('status')" :multiple="false" name="status" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/change_statuses" :value="status" :onChange="onChange" 
						:required="true" :clearable="status ? true : false">

					</dynamic-select>
				</div>

				<div class="row">
					
					<dynamic-select :label="lang('change_type')" :multiple="false" name="changeType" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/change_types" :value="changeType" 
						:onChange="onChange" :required="true" :clearable="changeType ? true : false">

					</dynamic-select>

					<dynamic-select :label="lang('priority')" :multiple="false" name="priority" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/change_priorities" :value="priority" 
						:onChange="onChange" :clearable="priority ? true : false">

					</dynamic-select>
				</div>

				<div class="row">
					
					<dynamic-select :label="lang('team')" :multiple="false" name="team" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/teams" :value="team" :onChange="onChange"
						:clearable="team ? true : false">
					
					</dynamic-select>

					<dynamic-select :label="lang('location')" :multiple="false" name="location" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/locations" :value="location" :onChange="onChange"
						:clearable="location ? true : false">

					</dynamic-select>
				</div>

				<div class="row">

					<dynamic-select :label="lang('impact')" :multiple="false" name="impact" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/impacts" :value="impact" 
						:onChange="onChange" :clearable="impact ? true : false">
					
					</dynamic-select>

					<dynamic-select :label="lang('department')" :multiple="false" name="department" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/departments" :value="department" :onChange="onChange"
						:clearable="department ? true : false">

					</dynamic-select>
				</div>

				<div class="row">
					
					<dynamic-select v-if="show_attach_assets" :label="lang('attach_assets')" :multiple="true" name="assets" 
						:prePopulate="false" classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/assets" :value="assets" 
						:onChange="onChange">

					</dynamic-select>
				</div>

				<div class="row">
					
					<ck-editor :value="description" type="text" :onChange="onChange" name="description" :label="lang('description')"
						classname="col-sm-12" :required="true" :lang="'en'">
						
					</ck-editor>
				</div>

				<div class="row">

					<file-upload :label="lang('attachments')" :value="attachment" name="attachment" :onChange="onFileSelected" 
						classname="col-xs-6" :id="change_id">
					
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

	import { validateChangeSettings } from "../../../validator/changeValidation.js";

	import { mapGetters } from 'vuex'

	export default {

		props : {

			from : { type : String, default : ''},

			onComplete : { type : Function },

			alertName : { type : String, default : 'change'},

			ticket_id : { type : String | Number, default : ''},
			
			problem_id : { type : String | Number, default : ''},
		},

		data(){

			return {

				title : 'create_change',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				change_id : '',

				subject : '',

				requester : '',

				status : '',

				assets : '',

				priority : '',

				changeType : '',

				location : '',

				impact : '',

				department : '',

				team : '',

				description : '',

				attachment : '',

				show_attach_assets : true,

				selectedFile : '',

				attachment_delete : false,

				identifier : ''
			}
		},

		beforeMount(){

			this.getActions();

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getActions(){
				
				axios.get('/service-desk/get/permission/api').then(res=>{
					
					this.show_attach_assets = res.data.data.actions.attach_asset;

				}).catch(error=>{
					
					this.show_attach_assets = true;
				})	
			},

			getValues(path){

				const changeId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_change'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(changeId)

				} else {

					this.loading = false;

					this.hasDataPopulated = true
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/change/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.updateStatesWithData(res.data.data.change)

					this.change_id = res.data.data.change.id

					this.changeType = res.data.data.change.change_type;

					this.impact = res.data.data.change.impact_type;
				
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

			isValid() {

				const { errors, isValid } = validateChangeSettings(this.$data);
				
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
			},

			onSubmit(){

				if(this.isValid()){

					this.loading = true 

					var fd = new FormData();
					
					if(this.change_id != ''){
					
						fd.append('id',this.change_id);
					}

					if(this.from){
						
						if(this.from == 'problem_modal') {

							fd.append('problem_id',this.problem_id);
						} else {

							fd.append('ticket_id',this.ticket_id);
						}
					}
					
					fd.append('subject', this.subject)

					fd.append('requester_id', this.requester.id)

					fd.append('status_id', this.status.id)

					if(this.assets !== ''){
						
						for(var i in this.assets){
						
							fd.append('asset_ids['+i+']', this.assets[i].id);
						}
					}

					fd.append('priority_id', this.priority ? this.priority.id : '')

					fd.append('change_type_id', this.changeType.id)

					fd.append('location_id', this.location ? this.location.id : '')

					fd.append('impact_id', this.impact ? this.impact.id : '')

					fd.append('department_id', this.department ? this.department.id : '')

					fd.append('team_id', this.team ? this.team.id : '')

					fd.append('identifier', this.identifier)
					
					fd.append('description', '<div style="display : block;word-break: break-all;">'+this.description+'</div>')
					
					if(this.selectedFile){
						
						fd.append('attachment', this.selectedFile)
					}

					if(this.attachment_delete){
						
						fd.append('attachment_delete', this.attachment_delete)
					}
				
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/change', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,this.alertName)
						
						if(!this.from){
							
							if(!this.change_id){
							
								this.redirect('/service-desk/changes')
							
							} else {

								this.getInitialValues(this.change_id)
							}

						} else {
							
							this.onComplete();
						}						
						
					}).catch(err => {
						
						this.loading = false
						
						errorHandler(err,'change')
					});
				}
			}
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),

			'file-upload': require('components/MiniComponent/FormField/fileUpload.vue'),
		}
	};
</script>

<style lang="css" scoped>
	#status {
		width: 50% !important;
		text-align: inherit !important;
	}
</style>