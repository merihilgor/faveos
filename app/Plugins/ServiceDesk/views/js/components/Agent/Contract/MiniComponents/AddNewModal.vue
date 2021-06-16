<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{lang('add')}}&nbsp;{{lang(name)}}</h4>
			
		</div>
			
		<div v-if="!loading" slot="fields">
				
				<div v-if="name === 'vendor'" id="vendor_fields">

					<vendor  from="modal" ref="addVendor" :onComplete="onCompleted" 
						alertName="contract-create-edit">
					
					</vendor>
				</div> 

				<contract-type v-else from="modal" ref="addContractType" :onComplete="onCompleted" 
					alertName="contract-create-edit">
				
				</contract-type>
		</div> 
			
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>
						
		<div slot="controls">
			
			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

				<i class="fa fa-check"></i> {{lang('proceed')}}
			</button>
		</div>
	</modal>
</template>

<script>
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { findObjectByKey } from 'helpers/extraLogics'

	import axios from 'axios'

	export default {
		
		name : 'add-new-modal',

		description : 'Add new Vendor, ContractType Modal component',

		props:{
			
			showModal : { type : Boolean, default : false },

			name : { type : String , default : ''},

			onClose : { type : Function },

			createdValue : { type : Function },
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle : { width : '900px' },

				loading : false
			}
		},

		methods:{

			onSubmit(){
				
				if(this.name === 'vendor') {

				 	this.$refs.addVendor.onSubmit();
				
				} else{

					this.$refs.addContractType.onSubmit();
				}
			},

			onCompleted(value,name){

				this.getNewValue(value,name)
				
				this.onClose();
			},

			getNewValue(value,name){

				axios.get('/service-desk/api/dependency/' + name + '?search-query='+value).then(res=>{
				
					let obj = findObjectByKey(res.data.data[name], 'name',value);

					this.createdValue(obj,name);
				})
			}
		},

		components:{
		
			'modal':require('components/Common/Modal.vue'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'vendor' : require('../../../../components/Admin/Vendor/VendorCreateEdit.vue'),

			'contract-type' : require('../../../../components/Admin/ContractType/ContractTypeCreateEdit.vue'),
		}
	};
</script>

<style scoped>

	#vendor_fields {
		margin-left: 15px;
    margin-right: 15px;
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
	}
</style>