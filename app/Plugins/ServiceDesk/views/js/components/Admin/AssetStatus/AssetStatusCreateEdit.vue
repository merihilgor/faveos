<template>
	
	<div>
	
		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="assetStatus"/>

		<div class="box box-primary" v-if="hasDataPopulated">

			<div class="box-header with-border">
			
				<div class="row">
					
					<div class="col-sm-12">
					
						<h2 class="box-title">{{trans(title)}}</h2>
					</div>
				</div>
			</div>

			<div class="box-body">
				
				<div class="row">

					<text-field :label="trans('name')" :value="name" 
						type="text" 
						name="name" 
						:onChange="onChange" 
						classname="col-xs-6" 
						:required="true">
					</text-field>
				</div>

				<div class="row">

					<ckeditor :value="description" type="text" :onChange="onChange" name="description" :label="trans('description')" 
						classname="col-sm-12"  :required="true" :lang="'en'">
									
					</ckeditor>
				</div>
			</div>

			<div class="box-footer">

				<button id="submit_btn" class="btn btn-primary" @click="onSubmit()">
				
					<i :class="iconClass"></i> {{trans(btnName)}}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import  { getIdFromUrl } from 'helpers/extraLogics';

	import { validateAssetStatusSettings } from "../../../validator/assetStatusSettings.js";

	export default {

		data(){

			return {

				title : 'create_asset_status',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				asset_status_id : '',

				name : '',

				description : ''
			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getValues(path){

				const assetStatusId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_asset_status'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(assetStatusId)

				} else {

					this.loading = false;

					this.hasDataPopulated = true;
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/asset-status/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.name = res.data.data.asset_status.name;

					this.description = res.data.data.asset_status.description;

					this.asset_status_id = res.data.data.asset_status.id;
				
				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'assetStatus');

					this.redirect('/service-desk/asset-statuses')
				});
			},

			isValid() {

				const { errors, isValid } = validateAssetStatusSettings(this.$data);
				
				return isValid;
			},

			onChange(value, name) {

				this[name] = value;
			},

			onSubmit(){

				if(this.isValid()){

					this.loading = true 

					var fd = new FormData();
					
					if(this.asset_status_id != ''){
					
						fd.append('id',this.asset_status_id);
					}
					
					fd.append('name', this.name);

					fd.append('description', this.description)
		
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/asset-status', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,'assetStatus')
						
						if(!this.asset_status_id){
						
							this.redirect('/service-desk/asset-statuses')
						}
					}).catch(err => {

						this.loading = false
						
						errorHandler(err,'assetStatus')
					});
				}
			},
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			'ckeditor': require('components/MiniComponent/FormField/CkEditorVue'),
		}
	};
</script>