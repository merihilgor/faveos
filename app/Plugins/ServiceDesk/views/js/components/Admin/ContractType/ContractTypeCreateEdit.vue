<template>
	
	<div>
	
		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>	
		</div>

		<alert componentName="contractType"/>

		<div :class="[{'box box-primary' : !from}]" v-if="hasDataPopulated">

			<div v-if="!from" class="box-header with-border">
			
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
						:classname="!from ? 'col-xs-6' : 'col-xs-12'" 
						:required="true">
					</text-field>
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

	import { validateContractTypeSettings } from "../../../validator/contractTypeSettings.js";

	export default {

		props : {

			/**
			 * From where this component is calling (if this component calls from modal popup i no need to show these fields in Box layout)
			 */
			from : { type : String, default : ''},

			onComplete : { type : Function },

			alertName : { type : String, default : 'contractType'}
		},

		data(){

			return {

				title : 'new_contracts',

				iconClass : 'fa fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				// essentials
				 
				contract_type_id : '',

				name : ''
			}
		},

		beforeMount(){

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods :{

			getValues(path){

				const contractTypeId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					if(!this.from){

						this.title = 'edit_contract_type'

						this.iconClass = 'fa fa-refresh'

						this.btnName = 'update'

						this.hasDataPopulated = false

						this.getInitialValues(contractTypeId)
					} else {

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
				
				axios.get('/service-desk/api/contract-type/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.name = res.data.data.contract_type.name;

					this.contract_type_id = res.data.data.contract_type.id;
				
				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'contractType');

					this.redirect('/service-desk/contract-types')
				});
			},

			isValid() {

				const { errors, isValid } = validateContractTypeSettings(this.$data);
				
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
					
					if(this.contract_type_id != ''){
					
						fd.append('id',this.contract_type_id);
					}
					
					fd.append('name', this.name)
		
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/service-desk/api/contract-type', fd,config).then(res => {

						this.loading = false
						
						successHandler(res,this.alertName)
						
						if(!this.from){

							if(!this.contract_type_id){
						
								this.redirect('/service-desk/contract-types')
							} else {

								this.getInitialValues(this.contract_type_id)
							}
						} else {

							this.onComplete(this.name,'contract_types');
						}
					}).catch(err => {

						this.loading = false
						
						errorHandler(err,this.alertName)
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