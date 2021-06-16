<template>
	
	<div>
	
		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="vendor"/>

		<div :class="[{'box box-primary' : !from}]" v-if="hasDataPopulated">

			<div v-if="!from" class="box-header with-border">
			
				<div class="row">
					
					<div class="col-md-4">
					
						<h2 class="box-title">{{lang(title)}}</h2>
					</div>
					
					<div class="col-md-8">
					
						<div class="dropdown pull-right">
					
							<a v-if="vendor_id" id="view_vendor" class="btn btn-primary" target="_blank" 
								:href="basePath()+'/service-desk/vendor/'+vendor_id+'/show'">
								
								<span class="fa fa-eye"></span> {{lang('view')}}
							</a>
						</div>
					</div>
				</div>
			</div>

			<div class="box-body">
				
				<div class="row">

					<text-field :label="lang('name')" :value="name" 
						type="text" 
						name="name" 
						:onChange="onChange" 
						classname="col-xs-6" 
						:required="true">
					</text-field>

					<text-field :label="lang('email')" :value="email" 
						type="email" 
						name="email" 
						:onChange="onChange" 
						classname="col-xs-6" 
						:required="true">
					</text-field>
				</div>

				<div class="row">
					
					<number-field :label="lang('primary_contact')"
						:value="primary_contact"  
	         	name="primary_contact" 
	         	classname="col-xs-6"
            :onChange="onChange" 
            type="number"
            :required="true"
            >
          </number-field>

					<text-field :label="lang('address')" :value="address" 
						type="textarea" 
						name="address" 
						:onChange="onChange" 
						classname="col-xs-6" 
						:required="true">
					</text-field>
				</div>

				<div class="row">
					
					<radio-button :options="radioOptions" :label="lang('status')" 
						name="status" 
						:value="status"
						:onChange="onChange" 
						classname="form-group col-xs-6">		
					</radio-button>
				</div>

				<div class="row">
					
					<ck-editor :value="description" type="text" :onChange="onChange" name="description" :label="lang('description')"
						classname="col-xs-12" :required="true" :lang="'en'">
						
					</ck-editor>

					<!-- <div :class="['col-md-12 form-group',{'has-error':show_err}]">

						<label for="Reply Content">{{ lang('description') }}</label><span class="text-red"> *</span>
						
						<ck-editor :value="description" name="vendor_content" :noDropdown="true"></ck-editor>
						
						<span v-if="show_err" class="help-block">This field is required</span>
					</div> -->

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

	import { validateVendorSettings } from "../../../validator/validationVendorSettings.js";

	export default {

		props : {

			from : { type : String, default : ''},

			product_id : { type : String | Number, default : ""},

			onComplete : { type : Function },

			alertName : { type : String, default : 'vendor'}
		},

		data(){

			return {

				title : 'create_vendors',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				vendor_id : '',

				name : '',

				description : '',

				email : '',

				primary_contact : '',

				address : '',

				status : 1,

				radioOptions:[{name:'active',value:1},{name:'inactive',value:0}],
			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getValues(path){

				const vendorId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					if(!this.from) {
						
						this.title = 'edit-vendor'

						this.iconClass = 'fa fa-refresh'

						this.btnName = 'update'

						this.hasDataPopulated = false

						this.getInitialValues(vendorId)
					}else {

						this.loading = false;

						this.hasDataPopulated = true;
					}
				} else {

					this.loading = false;

					this.hasDataPopulated = true;
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/vendor/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.updateStatesWithData(res.data.data.vendor)

					this.vendor_id = res.data.data.vendor.id;

					this.status = res.data.data.vendor.status.id;
				
				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'vendor');

					this.redirect('/service-desk/vendor')
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

				const { errors, isValid } = validateVendorSettings(this.$data);
				
				if (!isValid) {
				
					return false;
				}
				return true;
			},

			onChange(value, name) {

				this[name] = value;
			},

			onSubmit(){

				if(this.isValid()){

					this.loading = true 

					var fd = new FormData();
					
					if(this.vendor_id != ''){
					
						fd.append('id',this.vendor_id);
					}

					if(this.from){

						fd.append('product_id',this.product_id);
					}
					
					fd.append('name', this.name)

					fd.append('description', this.description)

					fd.append('email', this.email)

					fd.append('address', this.address)

					fd.append('primary_contact', this.primary_contact)

					fd.append('status_id', this.status)
		
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/vendor', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,this.alertName)
						
						if(!this.from){

							if(!this.vendor_id){
						
								this.redirect('/service-desk/vendor')
							
							} else {

								this.getInitialValues(this.vendor_id)
							}
						} else {

							this.onComplete(this.name,'vendors');
						}
					
					}).catch(err => {

						this.loading = false
						
						errorHandler(err,'vendor')
					});
				}
			},
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"number-field": require("components/MiniComponent/FormField/NumberField"),

			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
		}
	};
</script>

<style scoped>

</style>