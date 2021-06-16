<template>

	<div>

		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang('attach_assets')}}</h4>
			</div>

			<div slot="fields" id="static_select">

				<div class="row">

					<dynamic-select :label="lang('asset_types')" :multiple="false" name="asset_type_ids" :prePopulate="false"
						classname="col-xs-3" apiEndpoint="/service-desk/api/dependency/asset_types" :value="asset_type_ids"
						:onChange="onChange" :clearable="asset_type_ids ? true : false" strlength="10">

					</dynamic-select>

					<dynamic-select :label="lang('used_by')" :multiple="false" name="used_by_ids" :prePopulate="false"
						classname="col-xs-3" apiEndpoint="api/dependency/users?meta=true" :value="used_by_ids"
						:onChange="onChange" :clearable="used_by_ids ? true : false" strlength="10">

					</dynamic-select>

					<dynamic-select :label="lang('managed_by')" :multiple="false" name="managed_by_ids" :prePopulate="false"
						classname="col-xs-3" apiEndpoint="api/dependency/agents?meta=true" :value="managed_by_ids"
						:onChange="onChange" :clearable="managed_by_ids ? true : false" strlength="10">

					</dynamic-select>

					<dynamic-select :label="lang('organizations')" :multiple="false" name="org_ids" :prePopulate="false"
						classname="col-xs-3" apiEndpoint="api/dependency/organizations" :value="org_ids"
						:onChange="onChange" :clearable="org_ids ? true : false" strlength="10">

					</dynamic-select>

					<div class="col-sm-12">

						<button id="apply-btn" class="btn btn-primary round-btn pull -right" type="button" @click="onApply()">

							<span class="fa fa-check"></span>&nbsp; {{ lang('apply')}}
						</button>
					</div>
				</div>
				<br>
				<div class="height-limit">

					<asset-list-with-checkbox v-if="apiUrl" :apiUrl="apiUrl" :tickets="assetsData"
						:componentTitle="componentTitle">

					</asset-list-with-checkbox>

				</div>

			</div>

			<div class="row" slot="fields" v-if="loading === true">

				<custom-loader :duration="4000"></custom-loader>

			</div>

			<div slot="controls">
				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">
					<i class="fa fa-save"></i> {{lang('attach')}}
				</button>
			</div>

		</modal>

	</div>

</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from "helpers/responseHandler"

	import { mapGetters } from "vuex"

	import axios from "axios"

	export default {

		name : 'product-assets',

		description : 'Product asset Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

      productId : { type : String | Number, default : ''}
		},

		data() {

      return {

  			used_by_ids : '',

  			asset_type_ids : '',

  			managed_by_ids : '',

  			org_ids : '',

			  isDisabled:false,

        containerStyle:{ width:'950px' },

        loading:false,

			  size: 60,

			  apiUrl : '',

			  assetIds : [],

			  selectedFilters : {},

			  componentTitle : 'ProductAssets'
      }
		},

		methods:{

			onChange(value, name){

				window.eventHub.$emit(this.componentTitle+'uncheckCheckbox')

				this[name] = value ? value : '' ;

				this.selectedFilters[name] = value ? value : '';
			},

			onApply(){

				let baseUrlForFilter = '/service-desk/api/asset-list?';

				let params = '';

				for (var key in this.selectedFilters) {

					if(this.selectedFilters[key].id){

						params +=  key+'=' + this.selectedFilters[key].id + '&'
					}
				}

				if(params[params.length-1] === '&') {

					params = params.slice(0, -1);
				}

				this.apiUrl = baseUrlForFilter + params;
			},

			assetsData(data){

				this.assetIds = data
			},

			onSubmit(){

				if(this.assetIds.length > 0){

					this.loading = true

					const data = {};

					data['asset_ids'] = this.assetIds;

					data['product_id'] = this.productId;

					const config = { headers: { 'Content-Type': 'application/json' } }

					axios.post('/service-desk/api/product/attach-asset',data,config).then(res => {

						this.loading = false

						successHandler(res,'product-view')

						window.eventHub.$emit(this.componentTitle+'refreshData');

						this.onClose();

					}).catch(err => {

						this.loading = false

						errorHandler(err,'product-view')

						this.onClose();
					});
				} else {

					alert('Please select assets.')
				}
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'asset-list-with-checkbox' : require('../../../../Asset/AssetListWithCheckbox.vue'),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}
	};
</script>

<style type="text/css">
.has-feedback .form-control {
	padding-right: 0px !important;
}
#H5{
	margin-left:16px;
}
.margin {
	margin-right: 16px !important;margin-left: 0px !important;
}

#alert_msg {
	margin: 0px 5px 30px 5px;
}
#static_select{
			margin-left: 15px;
		margin-right: 15px;
}
#alert_top{
	margin-top:20px
}
.height-limit{
	overflow-y: auto;
		max-height: 300px;
		overflow-x: hidden;
}
</style>