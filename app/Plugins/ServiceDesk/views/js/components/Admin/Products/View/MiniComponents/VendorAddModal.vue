<template>

  <div>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang('vendor')}}</h4>
			</div>

			<div v-if="!loading" slot="fields">

				<div class="row">

					<div class="col-xs-12">
						<dynamic-select :label="lang('vendor')" :multiple="true" name="vendorIds" :prePopulate="false"
							classname="col-xs-12" :value="vendorIds" :onChange="onChange" :required="true"
							:apiEndpoint="'/service-desk/api/dependency/vendors_based_on_product?product_id='+productId" 
						>

						</dynamic-select>
					</div>
				</div>
			</div>

			<div v-if="loading" class="row" slot="fields" >

        <loader :animation-duration="4000" :size="60"/>
			</div>

			<div slot="controls">

				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

          <i class="fa fa-check"></i> {{lang('proceed')}}</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'release-add-modal',

		description : 'Release add Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			productId:{type:String|Number , default : ''},

			onClose:{type: Function},
		},

		data(){

      return {

		    isDisabled : true,

			  containerStyle:{ width:'500px' },

			  loading:false,

			  vendorIds : ''
		  }
    },

		methods:{

			onChange(value,name){

				this[name] = value;

				this.isDisabled = this.vendorIds.length > 0 ? false : true;
			},

			onSubmit(){

				this.loading = true

				this.isDisabled = true

				const data = {};

				data['vendor_ids'] = this.vendorIds.map(a => a.id);

				data['product_id'] = this.productId;

				const config = { headers: { 'Content-Type': 'application/json' } }

				axios.post('/service-desk/api/product/attach/vendor',data,config).then(res=>{

	        successHandler(res,'product-view');

					window.eventHub.$emit('ProductVendorsrefreshData');

					this.onClose();
	        
					this.loading = false;

	        this.isDisabled = true

	      }).catch(err => {

	        this.loading = false;

	        this.isDisabled = true

	        errorHandler(err,'product-view');

	        this.onClose();
				})
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

	    'loader':require('components/Client/Pages/ReusableComponents/Loader'),

	    "dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}

	};
</script>