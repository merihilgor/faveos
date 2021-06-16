<template>

	<div>

		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="contract-create-edit"/>

		<faveo-box v-if="hasDataPopulated" :title="lang(title)">

			<a slot="headerMenu" v-if="contract_id" id="view_contract" class="btn btn-primary pull-right" target="_blank"
				:href="basePath()+'/service-desk/contracts/'+contract_id+'/show'">

				<span class="fa fa-eye"> </span> {{lang('view')}}
			</a>
			
			<div>

				<div class="row">

					<text-field :label="lang('name')" :value="name" 
						type="text" 
						name="name" 
						:onChange="onChange" 
						classname="col-xs-6" 
						:required="true"
						>
					</text-field>

					<dynamic-select :label="lang('approver')" 
						:multiple="false" 
						name="approver" 
						classname="col-xs-6" 
						apiEndpoint="/api/dependency/users" 
						:value="approver" 
						:onChange="onChange" 
						:required="false"
						>
					</dynamic-select>					
				</div>

				<div class="row">

					<dynamic-select :label="lang('contract_type')" 
						:multiple="false" 
						name="contractType" 
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/contract_types" 
						:value="contractType" 
						:onChange="onChange" 
						:required="true"
						:showNewButton="true"
						:onButtonClick="getField"
						>
					</dynamic-select>

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

					<number-field :label="lang('cost')"
						:value="cost"  
						name="cost" 
						classname="col-xs-6"
						:onChange="onChange" 
						type="number"
						:required="true"
						>
					 </number-field>

					<dynamic-select :label="lang('contract_status')" 
						:multiple="false" 
						name="status" 
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/contract_statuses" 
						:value="status" 
						:onChange="onChange" 
						:required="true"
						:disabled="statusDisabled"
						>
					</dynamic-select>
				</div>

				<div class="row">

					<dynamic-select v-if="!vendor" :label="lang('user')" 
						:multiple="false" 
						name="user" 
						classname="col-xs-6" 
						apiEndpoint="/api/dependency/users" 
						:value="user" 
						:onChange="onChange" 
						:required="false"
						:hint="lang('user_organization_tooltip')"
						>
					</dynamic-select>

					<dynamic-select v-if="!user" :label="lang('vendor')" 
						:multiple="false" 
						name="vendor" 
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/vendors" 
						:value="vendor" 
						:onChange="onChange" 
						:required="false"
						:showNewButton="true"
						:onButtonClick="getField"
						>
					</dynamic-select>

			 		<dynamic-select v-if="showOrganization" :label="lang('organization')" 
						:multiple="false" 
						name="organization" 
						classname="col-xs-6" 
						:apiEndpoint="'/service-desk/api/user-organization/'+user.id" 
						:value="organization" 
						:onChange="onChange" 
						:required="false"
						>
					</dynamic-select>
				</div>

				<div class="row">

					<ck-editor :value="contractDescription" type="text" :onChange="onChange" name="contractDescription" 
						:label="lang('description')" classname="col-sm-12" :required="true" :lang="'en'">
						
					</ck-editor>
				</div>

				<div class="row">

					<dynamic-select :label="lang('license_type')" 
						:multiple="false" 
						name="licenseType" 
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/license_types" 
						:value="licenseType" 
						:onChange="onChange" 
						:required="false"
						>
					</dynamic-select>

					<number-field :label="lang('license_count')"
						:value="licenseCount"  
						name="licenseCount" 
						classname="col-xs-6"
						:onChange="onChange" 
						type="number"
						:required="false"
						>
					</number-field>
				</div>

				<div class="row">

					<date-time-field :label="lang('select_date_range')"
						:value="contractDate" 
						type="datetime"
						name="contractDate"
						:onChange="onChange" 
						range 
						:required="true" 
						format="MMMM Do YYYY, h:mm a" 
						classname="col-xs-6"
						:clearable="true" 
						:disabled="false" 
						:editable="true"
						:currentYearDate="false" 
						:time-picker-options="timeOptions"
						>
					</date-time-field>

					<number-field :label="lang('notify_before')"
						:value="notifyBefore"  
						name="notifyBefore" 
						classname="col-xs-6"
						:onChange="onChange" 
						type="number"
						:required="false"
						:hint="lang('notify_in_days')"
						>
					</number-field>
				</div>

				<div class="row">

					<dynamic-select :label="lang('notify_to')" 
						:multiple="true" 
						name="notify_to" 
						classname="col-xs-6" 
						apiEndpoint="/api/dependency/users" 
						:value="notify_to" 
						:onChange="onChange" 
						:required="false"
						:taggable="true"
						:hint="lang('notify_to_contracts')"
						>
					</dynamic-select>

					<dynamic-select v-if="show_attach_assets" :label="lang('attach_assets')" 
						:multiple="true" 
						name="asset_ids" 
						classname="col-xs-6" 
						apiEndpoint="/service-desk/api/dependency/assets" 
						:value="asset_ids" 
						:onChange="onChange"
						>
					</dynamic-select>
				</div>

				<div class="row">

					<file-upload :label="lang('attachments')" 
						:value="attachment" 
						name="attachment" 
						:onChange="onFileSelected" 
						classname="col-xs-6" 
						:id="contract_id">
					</file-upload>
				</div>
			</div>

			<button slot="actions" id="submit_btn" class="btn btn-primary" @click="onSubmit()">

					<i :class="iconClass"></i> {{lang(btnName)}}
			</button>
		</faveo-box>

		<transition name="modal">

			<add-new-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :name="modalName" 
				:createdValue="createdValue">
					
			</add-new-modal>
		</transition>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { mapGetters } from 'vuex'
	
	import { successHandler, errorHandler } from 'helpers/responseHandler';
	
	import  { getIdFromUrl } from 'helpers/extraLogics';
	
	import { validateContractSettings } from "../../../validator/contractValidation.js";
	
	import moment from 'moment'
	
	export default {
	
		name : 'contract-create-edit',
	
		description  : 'Contract create edit page',
	
		props : {
	
			onButtonClick : { type : Function},
		},
	
		data(){
	
			return {
	
				title : 'create_contract',
	
				iconClass : 'fa fa-save',
	
				btnName : 'save',
	
				hasDataPopulated : false,
	
				loading : false,
	
				// essentials
				contract_id : '',
	
				name : '',
	
				contractDescription : '',

				status : '',
	
				contractType : '',
	
				identifier : '',
	
				cost : '',
	
				approver : '',
	
				licenseType : '',
	
				licenseCount : '',
	
				vendor : '',
	
				contractDate : '',

				contract_start_date : '',
				
				contract_end_date : '',
	
				notifyBefore  : '',
	
				asset_ids : '',
	
				attachment : '',
				
				notify_to : '',
	
				show_attach_assets : true,
	
				timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
				
				modalName : '',
				
				showModal : false,
				
				selectedFile : '',
				
				attachment_delete : false,
				
				statusDisabled : false,
				
				organization : '',
				
				user : '',

				showOrganization : false,
			}
		},
	
		beforeMount(){
	
			this.getActions();
	
			const path = window.location.pathname
			
			this.getValues(path);
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},
	
		methods : { 
			
			checkOrganization(user) {

				axios.get('/service-desk/api/user-organization/'+user.id).then(res=>{

					this.showOrganization=res.data.data.data.length;
				}).catch(err=>{

					this.showOrganization=false;
				})
			},

			getField(name){
				
				this.modalName = name;
				
				this.showModal = true;
			},
	
			getActions(){
				
				axios.get('/service-desk/get/permission/api').then(res=>{
					
					this.show_attach_assets = res.data.data.actions.attach_asset;
	
				}).catch(error=>{
					
					this.show_attach_assets = true;
	
				})	
			},
	
			getValues(path){
	
				const contractId = getIdFromUrl(path)
	
				if(path.indexOf('edit') >= 0){
	
					this.title = 'edit_contract'
	
					this.iconClass = 'fa fa-refresh'
	
					this.btnName = 'update'
	
					this.hasDataPopulated = false
	
					this.getInitialValues(contractId)
	
				} else {

					this.loading = false;
	
					this.hasDataPopulated = true;
	
				}
	
			},
	
			getInitialValues(id){
				
				this.attachment_delete = false;
		
				this.selectedFile = '';
				
				this.loading = true
				
				axios.get('/service-desk/api/contract/'+id).then(res=>{
	
					this.loading = false;
	
					this.hasDataPopulated = true
	
					let contractData = res.data.data.contract;
	
					this.updateStatesWithData(contractData)
				
				}).catch(error=>{
	
					this.loading = false;
		
					errorHandler(error,'contract-create-edit')
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
				
				this.contractType = data.contractType;
				
				this.name = data.name;
				
				this.user = data.user ? data.user[0] : '';

				if(this.user) {

					this.checkOrganization(this.user)
				}
				
				this.organization = data.organization ? data.organization[0] : '';
	
				this.contractDate =[this.formattedTime(String(data.contract_start_date)), this.formattedTime(String(data.contract_end_date))];
				
				this.onChange(this.contractDate,'contractDate');

				this.contractDescription = data.description;
				
				this.licenseType = data.licence;
				
				this.licenseCount = data.licensce_count ? data.licensce_count : '';
				
				this.notifyBefore = data.notify_before ? data.notify_before : '';
				
				this.asset_ids = data.attach_assets;
				
				this.notify_to = data.notify_to;
				
				this.contract_id = data.id;
				
				this.attachment = data.attachment ? data.attachment[0] : '';

				this.statusDisabled = data.approver ? true : false;
			},
	
			isValid(){
	
				const { errors, isValid } = validateContractSettings(this.$data);
				
				return isValid;
			},
		
			createdValue(value,name) {
				
				let field = name === 'vendors' ? 'vendor' : 'contractType';
				
				this[field] = value;
			},
			
			onFileSelected(value,name,action){
			
				this.selectedFile = value;
			
				this.attachment_delete = action;
			},
			
			onChange(value, name) {

				this[name] = value;
							
				if(name === 'approver'){
			
					if(value){
			
						this.status = { id : 1, name : 'Draft' }
			
						this.statusDisabled = true;
					
					} else {
						
						this.status = '';

						this.statusDisabled = false;
					}
				}
	
				if(name === 'contractDate'){
	
					this.contract_start_date = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD HH:mm:ss') : '';
	
					this.contract_end_date =  value[1] !== null ? moment(value[1]).format('YYYY-MM-DD HH:mm:ss') : '';

					this.contractDate = this.contract_start_date ? [this.contract_start_date,this.contract_end_date] : '';
		 	 	}
		 	
		 	 	if(name === 'user') {
		 	
		 	 		if(!value) { 
		 	 			this.organization = '' 
		 	 			this.showOrganization = false;
		 	 		}
		 	 		else { this.checkOrganization(value) }
		 	 	}

		 	 	if(!value) { this[name] = '' }
			},
		
			onSubmit(){
						
				if(this.isValid()){
			
					this.loading = true 
		
					var fd = new FormData();
					
					if(this.contract_id != ''){
					
						fd.append('id',this.contract_id);
					}
					
					fd.append('name', this.name)
					
					fd.append('cost', this.cost)
					
					fd.append('license_type_id', this.licenseType ? this.licenseType.id : '')
					
					if(this.licenseCount){
					
						fd.append('licensce_count', this.licenseCount)
					}
					
					fd.append('description', this.contractDescription)
					
					fd.append('contract_type_id', this.contractType.id)
					
					fd.append('approver_id', this.approver ? this.approver.id : '')
					
					fd.append('vendor_id', this.vendor ? this.vendor.id : '')
					
					fd.append('contract_start_date',this.contract_start_date);
					
					fd.append('contract_end_date',this.contract_end_date);
					
					if(this.notifyBefore) {

						fd.append('notify_before', this.notifyBefore)
					}					
					
					fd.append('identifier', this.identifier)
					
					if(this.notify_to != ''){
						
						for(var i in this.notify_to){
							
							if(this.notify_to[i].id && typeof(this.notify_to[i].id) === 'number'){
					
								fd.append('notify_agent_ids['+i+']', this.notify_to[i].id);
					
							} else {
					
								fd.append('email_ids['+i+']', this.notify_to[i].name);
							}
						}
					}
					if(this.asset_ids != ''){
						
						for(var i in this.asset_ids){
						
							fd.append('asset_ids['+i+']', this.asset_ids[i].id);
						}
					}
					
					fd.append('status_id', this.status.id)
					
					fd.append('user_ids', this.user ? this.user.id : '')
					
					fd.append('organization_ids', this.organization ? this.organization.id : '')
					
					if(this.selectedFile){
						
						fd.append('attachment', this.selectedFile)
					}
					
					if(this.attachment_delete){
						
						fd.append('attachment_delete', this.attachment_delete)
					}
					
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }
					
					axios.post('/service-desk/api/contract', fd,config).then(res => {
					
						this.loading = false
						
						successHandler(res,'contract-create-edit')
						
						if(!this.contract_id){
						
							this.redirect('/service-desk/contracts')
						
						} else {
						
							this.getInitialValues(this.contract_id)
						}
					}).catch(err => {
					
						this.loading = false
						
						errorHandler(err,'contract-create-edit')					
					});
				}
			},
			
			onClose() {
			
				this.showModal = false;
			
				this.$store.dispatch('unsetValidationError');
			}
		},
		components  : {
		
			'alert' : require('components/MiniComponent/Alert'),
		
			'custom-loader' : require('components/MiniComponent/Loader'),
		
			"text-field": require("components/MiniComponent/FormField/TextField"),
		
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		
			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),
		
			'file-upload': require('components/MiniComponent/FormField/fileUpload.vue'),
		
			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
		
			'number-field':require('components/MiniComponent/FormField/NumberField'),

			'add-new-modal' : require('./MiniComponents/AddNewModal.vue'),

			'faveo-box' : require('components/MiniComponent/FaveoBox')
		}
	};
</script> 

<style scoped>
	#submit_btn { margin-left: -10px !important; }
</style>