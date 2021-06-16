<template>
	
	<div>

		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>
		</div>

		<alert componentName="product"/>

		<div class="box box-primary" v-if="hasDataPopulated">

			<div class="box-header with-border">
			
				<div class="row">
					
					<h2 class="box-title">{{lang(title)}}</h2>
					
				</div>
			</div>

			<div class="box-body">
				
				<div class="row">

					<text-field :label="lang('name')" :value="name" type="text" name="name" :onChange="onChange" 
						classname="col-xs-6" :required="true">
					
					</text-field>

					<radio-button :options="radioOptions" :label="lang('status')" name="status" :value="status"
								:onChange="onChange" classname="form-group col-xs-4" ></radio-button>
				</div>

				<div class="row">
					
					<text-field :label="lang('manufacturer')" :value="manufacturer" type="text" name="manufacturer" :onChange="onChange" 
						classname="col-xs-6" :required="true">
					
					</text-field>

					<dynamic-select :label="lang('product_status')" :multiple="false" name="product_status" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/product_statuses" :value="product_status" 
						:onChange="onChange" :required="true" :clearable="product_status ? true : false">

					</dynamic-select>
				</div>

				<div class="row">
					
					<dynamic-select :label="lang('product_mode_procurement')" :multiple="false" name="product_proc_mode" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/product_proc_mode" :value="product_proc_mode" 
						:onChange="onChange" :required="true" :clearable="product_proc_mode ? true : false">

					</dynamic-select>

					<dynamic-select :label="lang('department_access')" :multiple="false" name="department" :prePopulate="false"
						classname="col-xs-6" apiEndpoint="/api/dependency/departments" :value="department" :onChange="onChange"
						:clearable="department ? true : false" :required="true">
					
					</dynamic-select>
				</div>

				<div class="row">

					<dynamic-select :label="lang('asset_type')" :multiple="false" name="assetType" :disabled="assetDisabled"
						:prePopulate="false" classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/asset_types" 
						:value="assetType" :onChange="onChange" :clearable="assetType ? true : false">

					</dynamic-select>
				</div>

				<div class="row">
					
					<ck-editor :value="description" type="text" :onChange="onChange" name="description" :label="lang('description')"
						classname="col-xs-12" :required="true" :lang="'en'">
						
					</ck-editor>
				</div>
			</div>

			<div class="box-footer">

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

	import { validateProductSettings } from "../../../validator/productValidation.js";

	export default {

		data(){

			return {

				title : 'create_new_product',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				product_id : '',

				name : '',

				status : 1,

				radioOptions:[{name:'enable',value:1},{name:'disable',value:0}],

				manufacturer : '',

				product_status : '',

				product_proc_mode : '',

				department : '',

				description : '',

				assetType : '',

				assetDisabled : false,

			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getValues(path){

				const productId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_product'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(productId)

				} else {

					this.loading = false;

					this.hasDataPopulated = true
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/product/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.updateStatesWithData(res.data.data.product)

					this.product_id = res.data.data.product.id;
				
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

				this.product_proc_mode = data.procurement;

				this.assetType = data.asset_type;

				this.assetDisabled = data.asset_type ? true : false;
			},

			isValid() {

				const { errors, isValid } = validateProductSettings(this.$data);
				
				if (!isValid) {
				
					return false;
				}
				
					return true;
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
					
					if(this.product_id != ''){
					
						fd.append('id',this.product_id);
					}

					fd.append('name', this.name)

					fd.append('status_id', this.status)

					if(this.product_status && this.product_status.id) {
						fd.append('product_status_id', this.product_status.id)
					}

					fd.append('manufacturer', this.manufacturer)

					if(this.department && this.department.id) {
						fd.append('department_id', this.department.id)
					}

					if(this.product_proc_mode && this.product_proc_mode.id) {
						fd.append('procurement_mode_id', this.product_proc_mode.id)
					}
					
					fd.append('description', this.description)

					if(this.assetType && this.assetType.id) {
						fd.append('asset_type_id', this.assetType.id);
					}
					

					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/product', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,'product')
						
						if(!this.product_id){
							
							this.redirect('/service-desk/products')
							
						} else {

							this.getInitialValues(this.product_id)
						}

					}).catch(err => {
						
						this.loading = false
						
						errorHandler(err,'product')
					});
				}
			}
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

			"radio-button": require("components/MiniComponent/FormField/RadioButton"),

			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),
		}
	};
</script>