<template>
	<div>

		<!--loader-->
		<div class="row" v-if="hasDataPopulated === false || loading === true">

			<custom-loader :duration="4000"></custom-loader>
		
		</div>

		<!-- alert message which only gets mounted when vuex has non empty alert values -->
		<alert componentName="problem"/>

		<div :class="box_class" v-if="hasDataPopulated === true" :style="styleObj">

			<div v-if="box_class !== 'box'" class="box-header with-border">
			
				<div class="row">
					<div class="col-md-4">
						<h2 class="box-title">{{lang(title)}}</h2>
					</div>
					<div class="col-md-8">
						<div class="dropdown pull-right">
							<a v-if="problem_id !== ''" id="view_problem" class="btn btn-primary" :href="base+'/service-desk/problem/'+problem_id+'/show'" target="_blank">
								<span class="fa fa-eye">  </span> {{lang('view')}}
							</a>
						</div>
					</div>
				</div>


			</div>

			<div class="box-body">
				
				<div class="row">

					<text-field :label="lang('subject')" :value="subject" type="text" name="subject" :onChange="onChange" classname="col-xs-6" :required="true">
					</text-field>

					<text-field :label="lang('identifier')" :value="identifier" type="text" name="identifier" :onChange="onChange"
					 	classname="col-xs-6">
					</text-field>	
				</div>

				<div class="row">

					<dynamic-select :label="lang('requester')" :multiple="false" name="from" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/users?meta=true" :value="from" :onChange="onChange" :required="true">
					</dynamic-select>
					
					<dynamic-select :label="lang('department')" :multiple="false" name="department_id" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/departments" :value="department_id" :onChange="onChange" 
						:required="true">
					</dynamic-select>
				</div>

				<div class="row">
					
					<dynamic-select :label="lang('impact')" :multiple="false" name="impact_id" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/impacts" :value="impact_id" :onChange="onChange" :required="true">
					</dynamic-select>

					<dynamic-select :label="lang('status')" :multiple="false" name="status_type_id" :prePopulate="false"
						classname="col-xs-6 status_p" apiEndpoint="/api/dependency/statuses" :value="status_type_id" :onChange="onChange" 
						:required="true">
					</dynamic-select>
				</div>

				<div class="row">
					
					<dynamic-select :label="lang('location')" :multiple="false" name="location_id" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/locations" :value="location_id" :onChange="onChange">
					</dynamic-select>

					<dynamic-select :label="lang('priority')" :multiple="false" name="priority_id" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/priorities" :value="priority_id" :onChange="onChange" 
						:required="true">
					</dynamic-select>
				</div>

				<div class="row">

					<dynamic-select :label="lang('assigned-to')" :multiple="false" name="assigned_id" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/agents?meta=true" :value="assigned_id" :onChange="onChange">
					</dynamic-select>

					<dynamic-select v-if="show_attach_assets" :label="lang('attach_assets')" :multiple="true" name="asset_ids" 
						:prePopulate="false" classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/assets" :value="asset_ids" 
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
						classname="col-xs-6" :id="problem_id">
					
					</file-upload>

				</div>

			</div>

			<div v-if="box_class !== 'box'" class="box-footer">

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

	import { validateProblemSettings } from "../../validator/problemValidation.js";

	import { mapGetters } from 'vuex'

	export default {

		props : {
			
			box_class : { type : String, default : 'box box-primary'},

			styleObj  : { type : Object, default : ()=>{} },

			title_val : { type : String, default : ''},

			closePopup : { type : Function },

			alert : { type : String, default : 'problem'}

		},

		data(){
			return {

				base : '',

				title : 'create_problem',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				problem_id : '',

				subject : '',

				identifier : '',

				description : '',

				from : '',

				department_id : '',

				impact_id : '',

				status_type_id : '',

				location_id : '',

				priority_id : '',

				assigned_id : '',

				asset_ids : '',

				attachment : '',

				deleteUrl : '',

				apiUrl : '',

				show_attach_assets : true,

				selectedFile : '',

				attachment_delete : false
			}
		},

		computed:{
			...mapGetters(['getStoredTicketId','getUserData'])
		},

		watch : {
			getUserData(newValue,oldValue){
				this.base = newValue.system.url;
				return newValue
			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);

		},

		created(){
			
			if(this.getUserData.system){
				this.base = this.getUserData.system.url
			}

		},

		mounted(){

			this.getActions()
		},

		methods :{

			getActions(){
					axios.get('/service-desk/get/permission/api').then(res=>{
						this.show_attach_assets = res.data.data.actions.attach_asset
					}).catch(error=>{
						
					})
					
			},

			getValues(path){

				const problemId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_problem'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(problemId)

				} else {
					this.loading = false;

					this.hasDataPopulated = true

					this.subject = this.box_class === 'box' ? this.title_val : ''
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/problem/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.updateStatesWithData(res.data.data);

					this.asset_ids = res.data.data.assets;

					this.problem_id = res.data.data.id

					this.from = res.data.data.requester_id;
				
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

				const { errors, isValid } = validateProblemSettings(this.$data);
				
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
					this[name] = ''
				}
	
			},

			onSubmit(){

				this.apiUrl = this.box_class === 'box' ? '/service-desk/api/problem?ticket_id=' + this.getStoredTicketId : '/service-desk/api/problem/'

				if(this.isValid()){

					this.loading = true 

					var fd = new FormData();
					
					if(this.problem_id != ''){
					
						fd.append('id',this.problem_id);
					
					}
					
					fd.append('subject', this.subject)

					fd.append('identifier', this.identifier)

					fd.append('description', this.description)

					fd.append('requester_id', this.from.id)

					fd.append('department_id', this.department_id.id)

					fd.append('impact_id', this.impact_id.id)

					fd.append('status_type_id', this.status_type_id.id)

					fd.append('priority_id', this.priority_id.id)
					
					if(this.asset_ids !== ''){
						for(var i in this.asset_ids){
							fd.append('asset_ids['+i+']', this.asset_ids[i].id);
						}
					}

					fd.append('location_id', this.location_id ? this.location_id.id : '')
					
					fd.append('assigned_id', this.assigned_id ? this.assigned_id.id : '')
					
					if(this.selectedFile){
						
						fd.append('attachment', this.selectedFile)
					}

					if(this.attachment_delete){
						
						fd.append('attachment_delete', this.attachment_delete)
					}
				
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post(this.apiUrl, fd,config).then(res => {

						this.loading = false
						
						successHandler(res,this.alert)
						
						if(this.problem_id === '' && this.box_class !== 'box'){
						
							this.redirect('/service-desk/problems')
						
						} else {

							this.getInitialValues(this.problem_id)
						}

						if(this.box_class === 'box'){
							
							this.actionMethod();	

						} else{
							window.scrollTo(0, 0)
						}

					}).catch(err => {

						window.scrollTo(0, 0)
						
						this.loading = false
						
						errorHandler(err,this.alert)
					
					});

				}

			},

			actionMethod(){

				this.closePopup();

				window.eventHub.$emit('actionDone');
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

<style scoped>
	.status_p{
		width: 50% !important;
    text-align: unset !important;
	}
</style>