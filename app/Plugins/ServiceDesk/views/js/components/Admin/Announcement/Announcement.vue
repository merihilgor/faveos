<template>

	<div>

		<div class="row" v-if="loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="announcement"/>
	
		<div class="box box-primary">
			
			<div class="box-header with-border">
				
				<h2 class="box-title">{{lang('announcement')}}</h2>
			</div>

			<div class="box-body">
				
				<div class="row">
					
					<text-field :label="lang('subject')" :value="subject" 
						type="text" 
						name="subject" 
						:onChange="onChange" 
						classname="col-xs-5" 
						:required="true">
					</text-field>

					<radio-button :options="radioOptions" :label="lang('announcement_to')" 
						name="to" 
						:value="to"
						:onChange="onChange" 
						classname="form-group col-xs-3">		
					</radio-button>

					<template v-if="showField">
						
						<dynamic-select v-if="to === 'department'" :label="lang('department')" :multiple="false" 
							name="department" :prePopulate="false"
							classname="col-xs-4" 
							apiEndpoint="/api/dependency/departments" 
							:value="department" 
							:required="true"
							:clearable="department ? true : false"
							strlength="30" 
							:onChange="onChange">
						</dynamic-select>

						<dynamic-select v-if="to === 'organization'" :label="lang('organization')" :multiple="false" 
							name="organization" :prePopulate="false"
							classname="col-xs-4" 
							apiEndpoint="/api/dependency/organizations" 
							:value="organization"
							:required="true"
							:clearable="organization ? true : false"
							strlength="30" 
							:onChange="onChange">
						</dynamic-select>

					</template>
				</div>

				<div class="row">

					
				</div>

				<div class="row">
						
					<div :class="['col-md-12 form-group',{'has-error':show_err}]">

						<label for="Reply Content">{{ lang('description') }}</label><span class="text-red"> *</span>
							
						<ck-editor :value="description" name="announcement_content" :noDropdown="true"></ck-editor>
							
						<span v-if="show_err" class="help-block">This field is required</span>
					</div>
				</div>
			</div>

			<div class="box-footer">

				<button id="submit_btn" class="btn btn-primary" @click="onSubmit()">
					
					<i class="fa fa-paper-plane"></i> {{lang('send')}}
				</button>
			</div>	
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'
	
	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import { validateAnnouncementSettings } from "../../../validator/announcementValidationSettings.js";

	export default {

		name : 'announcement',

		description : 'Announcement page',

		data(){

			return {

				to : 'department',

				subject : '',

				description : '',

				organization : '',

				department : '',

				loading : false,

				radioOptions:[{name:'department',value:'department'},{name:'organization',value:'organization'}],
				
				classname : 'form-group',//ckeditor class
				
				show_err : false,

				showField : true
			}
		},

		methods : {

			isValid() {

				const { errors, isValid } = validateAnnouncementSettings(this.$data);
				
				if (!isValid) {
				
					return false;
				}
				return true;
			},

			onChange(value, name) {
			
				this[name] = value;

				if(name === 'to'){

					this.department = '';

					this.organization = '';

					this.showField = false;

					setTimeout(()=>{

						this.showField = true;
					},1)
				}
			},
			
			getDescription(){
			
				return CKEDITOR.instances['announcement_content'].getData();
			},

			onSubmit(){

				this.description = this.getDescription()
				
				this.classname = this.description === '' ? 'form-group has-error'  : 'form-group'
				
				this.show_err = this.description === '' ? true  : false
				
				if(this.isValid() && this.description != ''){
				
					this.loading = true 
				
					var fd = new FormData();
					
					fd.append('option', this.to + '_id')
				
					fd.append('subject', this.subject)
				
					if(this.organization){

						fd.append('organization_id', this.organization.id)
					}

					if(this.department){
					
						fd.append('department_id', this.department.id)
					}

					fd.append('announcement', this.description)
		
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/announcement', fd,config).then(res => {
					
						this.loading = false;

						this.subject = '';

						this.description = '';
						
						this.to = 'department';

						this.department = '';
						
						this.organization = '';

						CKEDITOR.instances['announcement_content'].setData('');
						
						successHandler(res,'announcement')

					}).catch(err => {

						this.loading = false
						
						errorHandler(err,'announcement')
					});
				}
			}
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),
			
			'custom-loader' : require('components/MiniComponent/Loader'),
			
			"text-field": require("components/MiniComponent/FormField/TextField"),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
			
			'ck-editor': require('components/MiniComponent/FormField/CkEditor.vue'),
			
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
		}
	};
</script>