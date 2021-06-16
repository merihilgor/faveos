<template>
	
	<div>
	
		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="assetstypes"/>

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

					<text-field :label="lang('name')" :value="name" type="text" name="name" :onChange="onChange" 
						classname="col-xs-6" :required="true">
					
					</text-field>



   <dynamic-select :label="lang('parent')" :multiple="false" name="parent_id" 
						:prePopulate="false" classname="col-xs-6" apiEndpoint="/service-desk/api/dependency/asset_types" 
						:value="parent_id" :onChange="onChange" :clearable="parent_id ? true : false">

					</dynamic-select>



				</div>
				<div class="row">
					 
       <!-- default asset type checkbox -->

					<div class="form-group col-md-12" id="text">
						<label class="label_align1">
							<input class="checkbox_align" type="checkbox" name="default" @change="changeDefault" :checked="make_default == 1" >{{lang(' Make Default Asset Type')}}
							<tool-tip :message="lang('make_default_asset_type_tooltip')" size="medium"></tool-tip>
						</label>
					</div>
      
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

	import { validateAssetsSettings } from "../../../validator/assetTypeValidation.js";


	export default {

		data(){

			return {


				title : 'new_assets',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				
				name : '',

				parent_id : '',

				assetTypeId : '',
				
				/**
				 * for making default asset type
				 * @type {Boolean}
				 */
				make_default: false

				
			}
		},
		
			beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getValues(path){

				const assetTypeId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_assetstypes'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(assetTypeId)

				} else {

					this.loading = false;

					this.hasDataPopulated = true

					this.make_default = 0
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/asset-type/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true;

					const responseData = res.data.data.asset_type;

					this.assetTypeId = responseData.id;

					this.make_default = responseData.is_default;

					this.parent_id = responseData.parent;

					this.name = responseData.name;
				
				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'assetstypes');

					this.redirect('/service-desk/assetstypes')
				});
				
			},

			isValid() {
				const { errors, isValid } = validateAssetsSettings(this.$data);			
				return isValid;
			},

		

			onChange(value, name) {
				this[name] = value;
				if(!value){

					this[name] = '';
				}
			},
				/**
			 * for making default asset type (checked=>returns 'on',unchecked=>returns 'off')
			 * @return {Void}
			 */
			changeDefault(e){

				this.make_default = e.target.checked  === true;
				
			},

			onSubmit(){

				if(this.isValid()){
                    let data={};
					this.loading = true 

				  

					var fd = new FormData();
					
					if(this.assetTypeId != ''){
					
						fd.append('id',this.assetTypeId);
					}
					
					fd.append('name', this.name)

					fd.append('parent_id', this.parent_id ? this.parent_id.id : '');

					fd.append('is_default',this.make_default );

					data['default_asset-type']=this.make_default;

				  


					
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/asset-type', fd,config).then(res => {


						this.loading = false
						
						successHandler(res,'assetstypes')
						
						if(this.assetTypeId){
							this.getInitialValues(this.assetTypeId)
						
						}
						else {
							this.redirect('/service-desk/assetstypes')
							
						}
					}).catch(err => {

						this.loading = false
						
						errorHandler(err,'assetstypes')
					});
			
				}
			},
		},

	

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"tool-tip": require("components/MiniComponent/ToolTip"),
		}
	};
</script>

<style type="text/css" scoped>
.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}
	.box-title
	{
		margin-left: -5px;

	}

</style>