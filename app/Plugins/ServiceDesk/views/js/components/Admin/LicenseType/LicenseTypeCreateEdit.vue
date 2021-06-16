<template>
	
	<div>
	
		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="licenseType"/>

		<div class="box box-primary" v-if="hasDataPopulated">

			<div class="box-header with-border">
			
				<div class="row">
					
					<div class="col-sm-12">
					
						<h2 class="box-title">{{lang(title)}}</h2>
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

	import { validateLicenseTypeSettings } from "../../../validator/licenseTypeSettings.js";

	export default {

		data(){

			return {

				title : 'open_new_license_type',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				license_type_id : '',

				name : ''
			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getValues(path){

				const licenseTypeId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_license_type'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(licenseTypeId)

				} else {

					this.loading = false;

					this.hasDataPopulated = true;
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/license-type/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.name = res.data.data.license_type.name;

					this.license_type_id = res.data.data.license_type.id;
				
				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'licenseType');

					this.redirect('/service-desk/license-types')
				});
			},

			isValid() {

				const { errors, isValid } = validateLicenseTypeSettings(this.$data);
				
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
					
					if(this.license_type_id != ''){
					
						fd.append('id',this.license_type_id);
					}
					
					fd.append('name', this.name)
		
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/license-type', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,'licenseType')
						
						if(!this.license_type_id){
						
							this.redirect('/service-desk/license-types')
						}
					}).catch(err => {

						this.loading = false
						
						errorHandler(err,'licenseType')
					});
				}
			},
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField")
		}
	};
</script>

<style scoped>

</style>