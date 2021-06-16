<template>
	
	<div>
	
		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="procurementType"/>

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

	import { validateProcurementTypeSettings } from "../../../validator/procurementTypeSettings.js";

	export default {

		data(){

			return {

				title : 'open_new_procurment',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				procurement_type_id : '',

				name : ''
			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getValues(path){

				const procurementTypeId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit-procurement'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(procurementTypeId)

				} else {

					this.loading = false;

					this.hasDataPopulated = true;
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/service-desk/api/procurement-mode/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.name = res.data.data.procurement_mode.name;

					this.procurement_type_id = res.data.data.procurement_mode.id;
				
				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'procurementType');

					this.redirect('/service-desk/procurement')
				});
			},

			isValid() {

				const { errors, isValid } = validateProcurementTypeSettings(this.$data);
				
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
					
					if(this.procurement_type_id != ''){
					
						fd.append('id',this.procurement_type_id);
					}
					
					fd.append('name', this.name)
		
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/procurement-mode', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,'procurementType')
						
						if(!this.procurement_type_id){
						
							this.redirect('/service-desk/procurement')
						}
					}).catch(err => {

						this.loading = false
						
						errorHandler(err,'procurementType')
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