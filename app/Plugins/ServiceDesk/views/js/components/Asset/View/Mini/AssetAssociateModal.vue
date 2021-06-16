<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose">
			
		<div slot="title">

			<h4 class="text-capitalize">{{trans('attach') + ' ' + associate}}</h4>
		</div>

		<div slot="fields" v-if="!loading">

			<div class="row">

				<div class="col-xs-12">
						
					<dynamic-select :label="trans(associate)" :multiple="true" name="associated" :prePopulate="false"
						classname="col-sm-12" :apiEndpoint="endPoint" 
						:value="associated" :onChange="onChange" :required="true">

					</dynamic-select>
				</div>
			</div>
		</div>

		<div v-if="loading" class="row" slot="fields" >

		  <loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" 
				:disabled="associated.length > 0 ? false : true">

			 <i class="fa fa-paperclip"></i> {{trans('attach')}}</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from "axios"

	export default {

		name : 'asset-associate-modal',

		description : 'Asset Associates Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

			assetId : { type : String | Number, default : ''},

			associate : { type : String , default : ''},
		},

		data(){

			return {

				associated : [],

				loading : false,
			}
		},

		computed : {

			endPoint() {

				let relation = (this.associate == 'tickets') ? 'assets' : (this.associate == 'contracts' ? 'attachAsset' : 'attachAssets');
				let attributeName = this.associate == 'tickets' ? 'type_id' : 'asset_id';
				
				return '/service-desk/api/dependency/'+this.associate+'?supplements[relation_name]='+relation+'&supplements[attribute_name]='+attributeName+'&supplements[attribute_value]='+this.assetId
			}
		},

		methods : {

			onChange(value, name) {

				this[name] = value;
			},

			onSubmit() {

				this.loading = true 

				let type = '';

				if(this.associate == 'tickets') {

					type = 'tickets';
				
				} else if (this.associate == 'problems') {
					
					type = 'sd_problem'
				
				} else {
					
					type = 'sd_'+this.associate
				}

				let data = {};

				var ids = [];

				for(var i in this.associated){

					ids.push(this.associated[i].id)
				}

				data['type'] = type;

				data['type_ids'] = ids;

				axios.post('/service-desk/api/attach-asset-services/'+this.assetId,data).then(res => {

					this.loading = false
					
					this.onClose();

					successHandler(res,'asset-view');

					window.eventHub.$emit('updateAssetAssociates',this.associate);
						
				}).catch(err => {
						
					this.loading = false
						
					this.onClose();

					errorHandler(err,'asset-view')
				});
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>