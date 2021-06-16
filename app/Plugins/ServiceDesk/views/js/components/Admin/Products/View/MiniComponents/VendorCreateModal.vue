<template>

  <div>

  	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang('vendor')}}</h4>
			</div>

			<div v-if="!loading" slot="fields" id="vendor_fields">

				<vendor from="modal" ref="vendorCreate" :product_id="productId" :onComplete="onCompleted" alertName="product-view">
					
				</vendor>
			</div>
		
			<div v-if="loading" class="row" slot="fields" >

        <loader :animation-duration="4000" :size="60"/>
			</div>

			<div slot="controls">

				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

          <i class="fa fa-save"></i> {{lang('save')}}</button>
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

		    isDisabled:false,

			  containerStyle:{ width:'800px' },

			  loading:false,

			}
    },

		methods:{

			onSubmit(){

        this.$refs.vendorCreate.onSubmit();
			},

			onCompleted(){

				window.eventHub.$emit('ProductVendorsrefreshData');
				 
				this.onClose();
			}
		},

	components:{

		'modal':require('components/Common/Modal.vue'),

    'loader':require('components/Client/Pages/ReusableComponents/Loader'),

    'vendor' : require('../../../../../components/Admin/Vendor/VendorCreateEdit.vue')
	}

};
</script>

<style type="text/css">
		
	#vendor_fields {
		margin-left: 15px;
    margin-right: 15px;
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
	}
</style>