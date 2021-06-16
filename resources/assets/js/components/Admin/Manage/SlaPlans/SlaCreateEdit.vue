<template>
	
	<div>

		<alert componentName="sla"></alert>

		<div class="box box-primary">

			<div class="box-body">
				
				<div class="row" v-if="!hasDataPopulated || loading">

					<loader :animation-duration="4000" :size="60"/>
				</div>

				<template v-if="hasDataPopulated">

					<div class="row">
						
						<div class="box-container">
							
							<div class="box-header with-border with-switch">
								
								<h3 for="select targets" id="title" class="box-title">{{lang(title)}}</h3>

								<status-switch name="status" :value="status" :onChange="onChange" classname="pull-right" :bold="true">
							
								</status-switch>
							</div>
							
							<div class="box-body">
	              
	              <div class="row">
	                 
	                <text-field :label="lang('name')" :value="name" type="text" name="name" :onChange="onChange" classname="col-xs-6" :required="true">
									</text-field> 	
	              </div>

	              <div class="row">

	              <div class="box-container">
							
									<div class="box-header with-border">
										
										<h3 for="select targets" id="title" class="box-title">{{lang('sla_targets')}}</h3>
									</div>
									
									<div class="box-body">

			             	<template v-if="targetArr.length > 0">

			             		<table class="table" id="target_table">
                				
                				<tbody>

                					<tr>
                  					
                  					<th style="width:10%">{{lang('priority')}}</th>
                  					
                  					<th>{{lang('respond_Within')}}<label style="color: rgb(220, 53, 69);">*</label></th>
                  					
                  					<th>{{lang('resolve_within')}}<label style="color: rgb(220, 53, 69);">*</label></th>

                  					<th style="width:25%">{{lang('operational_hours')}}</th>

                  					<th>{{lang('in_app_notification')}}</th>
                  					
                  					<th>{{lang('email_esc')}}</th>
                					</tr>
                					
                					<tr v-for="(target,index) in targetArr">
                  					
                  					<td>{{target.name}}</td>
                  					
                  					<td>
                  						
                  						<div id="d_flex">
																<number-field :label="lang('respond_Within')"
																	:value="target.respond_count"  
																	:name="'respond_count-'+index"
																	:onChange="onChange"
																	classname="w_70" 
																	type="number" :labelStyle="labelStyle"
																	:required="true"
																	placeholder="n"
																>
																</number-field>&nbsp;&nbsp;

																<static-select :label="lang('option')" :elements="selectOptions" 
																	:name="'respond_option-'+index"  
																	:onChange="onChange"
																	:value="target.respond_option" 
																	:labelStyle="labelStyle"
																	:hideEmptySelect="true">

																</static-select>
															</div>
                  					</td>
                  					
                  					<td>
                  						
                  						<div id="d_flex">
														
																<number-field :label="lang('resolve_within')"
																	:value="target.resolve_count"  
																	:name="'resolve_count-'+index" 
																	:onChange="onChange" 
																	classname="w_70" :labelStyle="labelStyle"
																	type="number"
																	:required="true"
																	placeholder="n"
																>
																</number-field>&nbsp;&nbsp;

																<static-select :label="lang('option')" :elements="selectOptions" 
																	:name="'resolve_option-'+index"  
																	:onChange="onChange" 
																	:value="target.resolve_option" 
																	:labelStyle="labelStyle"
																	:hideEmptySelect="true">

																</static-select>
															</div>
                  					</td>

                  					<td>
                  						<dynamic-select :label="lang('operational_hours')" :multiple="false" 
																:name="'business_hour-'+index" 
																:labelStyle="labelStyle"
																apiEndpoint="/api/dependency/business-hours" 
																:value="target.business_hour" :onChange="onChange" :strlength="17"
																:required="false" :clearable="target.business_hour ? true : false">
															</dynamic-select> 
                  					</td>

                  					<td>
                  						
                  						<status-switch :name="'in_app-'+index" :value="target.in_app" :onChange="onChange" 
                  							:bold="true">
									
															</status-switch>
                  					</td>
                  					
                  					<td>
                  						
                  						<status-switch :name="'email_esc-'+index" :value="target.email_esc" :onChange="onChange" 
                  							:bold="true">
									
															</status-switch>
                  					</td>
               						</tr>
               					</tbody>
               				</table>
			              </template>
			            </div>
			          </div></div>
	            </div>
						</div>
					</div>

					<div class="row">
						
						<rule-menu v-if="!default_sla" :category="'sla'" :obj="obj" :ruleList="ruleList" :customForm="ticketCustomFields" 
							:editRuleValues="editRuleValues">
									
						</rule-menu>

						<template>
		
							<div id="alert_div">
							
								<alert componentName="reminder"/>
							</div>
							
							<template>

								<sla-reminders :getUpdateArray="approchesArray" :approach="approach"></sla-reminders>	

								<sla-escalations :getUpdateArray="violatedArray" :violated="violated"></sla-escalations>

							</template>
						</template>
					</div>

					<div class="row">
						
						<text-field :label="lang('admin_notes')" :value="internal_notes" type="textarea" name="internal_notes" :onChange="onChange" 
							classname="col-xs-12" :rows="10">
						</text-field> 
					</div>	
				</template>
				
			</div>

			<div class="box-footer">

				<button class="btn btn-primary" @click="validateForm()">
					
					<i :class="iconClass"></i> {{lang(btnName)}}
				</button>	
			</div>

			<div class="row" v-if="pageLoad">

				<custom-loader :duration="4000"></custom-loader>
			</div>
		</div>
	</div>
</template>

<script>
	
	import { boolean, getValueFromNestedArray, extractOnlyId, getIdFromUrl, lang } from "helpers/extraLogics.js";

	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { assignLabel } from "helpers/assignCustomFieldLabel";

	import { validateSlaSettings } from "helpers/validator/slaRules.js"

	import axios from 'axios'

	export default {

		name : 'sla-create-edit',

		description  : 'SLA Create and Edit page',

		data(){

			return {

				title : 'create_new_sla_plan',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : true,

				pageLoad : false,

				sla_id :'',

				name : '',

				status : true,

				internal_notes : '',

				selectOptions: [{id: 'minute', name: 'Min(s)'}, {id: 'hour', name: 'Hour(s)'},{id: 'day', name: 'Day(s)'}],
				
				labelStyle : { display : 'none' },

				submitData: {},

				count: 0,

				ruleList: [
					{
						id: null,
						
						field: "",
						
						relation: "equal",
						
						category: "ticket",
						
						value: "",
						
						rules: []
					}
				],

				obj: {

					id: null,

					matcher: "any",

				},

				/**
				 * variable to store the customields
				 */
				ticketCustomFields: [],

				/**
				 *state for storing the edit data
				 */
				editData: {},

				editRuleValues: [],

				/**
				 *editform is used to check if the edit api has been called or not
				 */
				editformcall: false,

				/**
				 * Reminders
				 */
				approach : [],

				violated : [],

				approach_response : [],

				approach_resolution : [],

				violated_response : [],

				violated_resolution : [],

				priorities : [],

				target_status : true,

				targetArr : [],

				default_sla : false,
			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		created() {
			// setting form mode to workflow-lisetner
    	this.$store.dispatch('setFormMode', 'workflow-listener');
		},

		watch: {
		
			editformcall(newvalue) {
				
				this.editformcall = newvalue;
			}
		},

		methods : {

			getPriorities() {

				axios.get('/api/dependency/priorities', { params: {limit: 'all' }}).then(res=>{

					this.priorities = res.data.data.priorities;

					for(var i in this.priorities){

						this.targetArr.push(
							{ 
								id : null,
								ticket_sla_id : null,
								p_id : this.priorities[i].id,
								name : this.priorities[i].name, 
								business_hour : '', 
								respond_count : 4, respond_option : 'hour',
								resolve_count : 10, resolve_option : 'hour',
								email_esc : true,
								in_app : true
							}
						)
					}

				}).catch(err=>{

					this.targetArr = [];
					
					errorHandler(err, "sla");
				})
			},

			getValues(path){

				this.sla_id = getIdFromUrl(path);

				this.getFormData();

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_sla_plan'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'
				
				} else {

					this.getPriorities();
				}
			},

			approchesArray(response,resolution){

				this.approach_response = response;

				this.approach_resolution = resolution;

				this.approach = [...this.approach_response, ...this.approach_resolution];
			},

			violatedArray(response,resolution){

				this.violated_response = response;

				this.violated_resolution = resolution;

				this.violated = [...this.violated_response, ...this.violated_resolution];
			},

			finalArray(array){

				var final = [];

				for(var i in array){

					var option = array[i].option;

					var delta = array[i].reminder_delta;

					var agents = array[i].reminder_receivers.agents;

					var agent_types = array[i].reminder_receivers.agent_types;

					final.push({
						
						id : array[i].id,
						
						reminder_delta : 'diff::'+delta + '~' + option,
						
						type : array[i].type === 'responded' ? 'response' : array[i].type === 'resolved' ? 'resolution' : array[i].type,
						
						reminder_receivers : {
							
							agent_types : agent_types.map(a => a.id),
							
							agents : agents.map(a => a.id)
						}
					})
				}

				return final
			},

			onChange(value,name){

				this[name] = value;

				let nameArray = name.split('-')

				let index = nameArray[nameArray.length - 1]

				if(name.includes('business_hour-')){
					
					this.targetArr[index].business_hour = value ? value : '';
				}

				if(name.includes('respond_count-')){
					
					this.targetArr[index].respond_count = value
				}

				if(name.includes('respond_option-')){
					
					this.targetArr[index].respond_option = value
				}

				if(name.includes('resolve_count-')){
					
					this.targetArr[index].resolve_count = value
				}

				if(name.includes('resolve_option-')){

					this.targetArr[index].resolve_option = value
				}

				if(name.includes('email_esc-')){
					
					this.targetArr[index].email_esc = value
				}

				if(name.includes('in_app-')){
					
					this.targetArr[index].in_app = value
				}
			},

			validateForm() {

				this.childrenCount = 0;
				
				this.validationArray = [];

				$.each(this.$children, this.nestedChildValidation);
			},

			nestedChildValidation(key, value) {

				this.childrenCount++;
				
				value.$validator.validateAll().then(result => {
					
					if (result) {
					
						this.validationArray.push(result);
					}
				});
				
				if (value.$children.length != 0) {
					
					$.each(value.$children, this.nestedChildValidation);
				
				} else {

					setTimeout(() => {

						if (this.isValid() && this.validationArray.length == this.childrenCount) {
					
							this.submitForm();
					
						} else {
					
							let x = document.getElementsByClassName("field-danger")[0];
					
							if(x !== undefined){
					
								x.scrollIntoView({behavior: "smooth",block: 'end'});
					
							}
						}
					}, 50);
				}
			},

			/**
			 * The respective function helps in setting the data format which is required by the backend,
			 * @param {Array} parameter
			 * @param {String} type
			 * @return {void}
			 *
			 */
			nodefunction(parameter, type) {

				return parameter.filter( param => {
					
					if(!boolean(param.field)){ // if field is empty, we don't append that to array
						
						return false;
					}
					
					return param;

				}).map(param => {
					
					if (param[type].length > 0) {
						
						return {
							
							field: param.field,
							
							value: param.value,
							
							category: param.category ? param.category : null,
							
							relation: param.relation ? param.relation : null,
							
							id: param.id ? param.id : null,
							
							[type]: _.flattenDepth(
							
							param[type].map(param1 => {
							
								if (param1 != undefined && Array.isArray(param1.node)) {
							
									return param1.node.map(res => {

										return {
								
											field: res.unique,
							
											value: res.value,
							
											// giving this same as parent relation
							
											relation: param.relation,
							
											id: this.editformcall == true ? getValueFromNestedArray(param1.editdata,res.unique,"id"): null,
							
											[type]: _.compact(
							
												_.flattenDepth(this.filternode(res.options, type, param1.editdata, param.relation),1)
											)
										};
									});
								}
							}),
							1
						)
					};
				} else {
					
					return param;
					}
				});
			},

			/**
			 * The Filternode function helps to check if there are any nested nodes in custom fields,
			 * if there are nestednode then the given function would transform the data that in the
			 * backend format as required
			 * @param {Array} data;
			 * @param {String} type;
			 * @param {Array} y; // these are editActionvalue || editRulevalue
			 * @param {String} relation   relation of the parent ('equal','not_equal' etc)
			 */
			filternode(data, type, y, relation) {

				if (boolean(data) && data.length > 0) {
				
					return data.map(value => {
				
						if (value.nodes.length > 0) {
				
							return value.nodes.map(res => {
				
								return {
				
									field: res.unique,
				
									value: res.value,
				
									relation : relation,
				
									id:
										this.editformcall === true
											? getValueFromNestedArray(y, res.unique, "id")
											: null,
									[type]: _.compact(
										_.flattenDepth(this.filternode(res.options, type), 1)
									)
								};
							});
						}
					});
				} else {
					return data;
				}
			},

			async getFormData(){
				
				this.loading = true;
				
				this.hasDataPopulated = false;

				// call edit form API only when userForm and ticketForm is resolved
				Promise.all([
		
					this.getTicketFormData(),
			
					this.getUserFormData(),

				]).then(res => {

					this.loading = false;

					this.hasDataPopulated = true;

					if(this.sla_id){

						this.editform(this.sla_id);
					}
				});
			},

			getTicketFormData() {

				return axios.get("/api/get-form-data?category=ticket&mode=workflow-listener").then(res => {

					let formFields = res.data.data.form_fields;

					formFields = this.getFormFieldsForSLA(formFields);

					assignLabel(formFields, this.currentLanguage, 'agent');

					this.$store.dispatch('setTicketFormFields', formFields)

				}).catch(error => {
					errorHandler(error, 'sla');
				});
			},

			/**
			 * Gets form field for SLA
			 * @param {Array} baseFormFields
			 * return Array
			 */
			getFormFieldsForSLA(baseFormFields){

				// add tags and labels to the array
				let tagField = {"title": "Tags", "display_for_agent": true, "unique":'tag_ids', "label":"Tags", "type": "select",
					"required_for_agent": false, "default":1, "labels_for_form_field":[{"id":null, "label":lang("tags"), "language":"en", "api_info": "url:=/api/dependency/tags"}]};

				let labelField = {"title": "Labels", "display_for_agent": true, "unique":'label_ids', "label":"Labels", "type": "select",
					"required_for_agent": false, "default":1, "labels_for_form_field":[{"id":null, "label":lang("labels"), "language":"en", "api_info": "url:=/api/dependency/labels"}]};

				baseFormFields.push(tagField);
				baseFormFields.push(labelField);

				return baseFormFields;
			},

			getUserFormData() {

				return axios.get("/api/get-form-data?category=user&mode=workflow-listener").then(res => {

					assignLabel(res.data.data.form_fields, this.currentLanguage, 'agent');

					this.$store.dispatch('setUserFormFields', res.data.data.form_fields)

				}).catch(error=>{

					errorHandler(error, 'sla');
				});
			},

			getTarget(meta){

				var arr = [];

				for(var i in meta){

					var obj = {};

					obj['id'] = meta[i].id;

					obj['p_id'] = meta[i].priority_id;

					obj['ticket_sla_id'] = meta[i].ticket_sla_id;

					obj['name'] = meta[i].priority.name;

					obj['business_hour'] = meta[i].business_hour;
					
					obj['email_esc'] = meta[i].send_email_notification;

					obj['in_app'] = meta[i].send_app_notification;

					var resolve = meta[i].resolve_within.split('~');

					var res_count = resolve[resolve.length -2].split('::');

					obj['resolve_option'] = resolve[resolve.length -1];

					obj['resolve_count'] = res_count[res_count.length -1];

					var respond = meta[i].respond_within.split('~');

					var resp_count = respond[respond.length -2].split('::');

					obj['respond_option'] = respond[respond.length -1];

					obj['respond_count'] = resp_count[resp_count.length -1];

					arr.push(obj)
				}

				return arr;
			},

			slaMeta(){

				let metaArr = [];

				for(var i in this.targetArr) {

					var obj = {};

					obj['id'] = this.targetArr[i].id;
					
					obj['priority_id'] = this.targetArr[i].p_id;

					obj['ticket_sla_id'] = this.targetArr[i].ticket_sla_id;

					obj['business_hour_id'] = this.targetArr[i].business_hour.id;
					
					obj['send_email_notification'] = this.targetArr[i].email_esc;

					obj['send_app_notification'] = this.targetArr[i].in_app;

					obj['respond_within'] = 'diff::'+this.targetArr[i].respond_count + '~' + this.targetArr[i].respond_option;
					
					obj['resolve_within'] = 'diff::'+this.targetArr[i].resolve_count + '~' + this.targetArr[i].resolve_option;

					metaArr.push(obj)
				}

				return metaArr;
			},

			/**
			 * Submit method of the form
			 */
			submitForm() {

				this.count++;
				
				if (this.count == 1) {

					this.obj['name'] = this.name;

					this.obj['status'] = this.status;
					
					this.obj['internal_notes'] = this.internal_notes;

					this.obj['reminders'] = {
						'approaching' : this.finalArray(this.approach), 
						'violated' : this.finalArray(this.violated)
					}

					this.submitData = Object.assign({}, this.obj);
				
					this.submitData["rules"] = this.nodefunction(this.ruleList, "rules");

					this.submitData["sla_meta"] = this.slaMeta();
				
					this.pageLoad = true;
				
					axios.post("/api/post-enforcer", {type: 'sla', data: this.submitData}).then(res => {
						
						this.pageLoad = false;

						successHandler(res, "sla");
					
						if(!this.sla_id){
							
							this.redirect('/sla');

						} else {

							this.editform(this.sla_id)
						}
					}).catch(err => {

						errorHandler(err, "sla");
				
					}).finally(res => {
				
						this.pageLoad = false;
				
						this.count = 0;
					});
				}
			},

			editform(id) {
			
				this.ruleList = [];

				this.editformcall = true;

				this.loading = true;
				
				this.hasDataPopulated = false;

				axios.get("/api/get-enforcer/sla/"+ id).then(res => {
					
					this.editData = res.data.data.sla;
					
					this.name = this.editData.name;

					this.status = boolean(this.editData.status);

					this.default_sla = boolean(this.editData.is_default);
					
					this.internal_notes = this.editData.internal_notes;
					
					this.obj.id = this.editData.id;

					this.obj.matcher = this.editData.matcher;
					
					this.targetArr = this.getTarget(this.editData.sla_meta);

					this.editRuleValues = _.cloneDeep(this.editData.rules);
					
					this.ruleList = this.editData.rules;

					this.approach = this.editData.reminders.approaching;

					this.violated = this.editData.reminders.violated;
					
					this.loading = false;
					
					this.hasDataPopulated = true;

				}).catch(err => {
					
					this.loading = false;
					
					this.hasDataPopulated = true;
				});
			},

			isValid() {

				const { errors, isValid } = validateSlaSettings(this.$data);
				
				if (!isValid) {
				
					return false;
				
				}
					return true;
			},
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'loader' : require('components/Client/Pages/ReusableComponents/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			'status-switch':require('components/MiniComponent/FormField/Switch'),

			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),

			'static-select':require('components/MiniComponent/FormField/StaticSelect'),
			
			'number-field':require('components/MiniComponent/FormField/NumberField'),

			"rule-menu": require("components/Admin/Workflow/RuleMenu.vue"),

			'sla-escalations' : require('./SlaEscalations.vue'),

			'sla-reminders' : require('./SlaReminders.vue'),
		}
	};
</script>

<style scoped>
	
	#title{
		margin-left: -8px;
	}

	.label_align1 {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: 500 !important; padding-top: 6px;
	}

	.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}

	.box-container {
    margin: 20px 15px;
    border: 1px solid #ddd;
	}

	#alert_div{
		margin-left: 15px;
    margin-right: 15px;
	}

	#d_flex {
		display: flex;
		/*padding: 0px;*/
	}

	.w_70{
		width: 70px !important;
	}

	.p_name{
		text-align: left;
		word-break: break-all;
	}

	#target_table{
		font-size: inherit !important;
	}
</style>

<style>
	.form-group.has-error .form-control {
    border-color: #a94442 !important;
	}
</style>