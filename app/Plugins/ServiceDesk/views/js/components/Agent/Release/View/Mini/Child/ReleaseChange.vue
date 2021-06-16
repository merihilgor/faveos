<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose">
			
		<div slot="title">

			<h4 class="text-capitalize">{{trans('attach_change')}}</h4>
		</div>

		<div slot="fields" v-if="!loading">

			<div class="row">

				<div class="col-xs-12">
						
					<dynamic-select :label="trans('change')" :multiple="true" name="change" :prePopulate="false"
						classname="col-sm-12" :apiEndpoint="'/service-desk/api/dependency/changes?supplements[relation_name]=attachReleases&supplements[attribute_name]=release_id&supplements[attribute_value]='+releaseId" :value="change" :onChange="onChange" :required="true">

					</dynamic-select>
				</div>
			</div>
		</div>

		<div v-if="loading" class="row" slot="fields" >

		  <loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" 
				:disabled="change.length > 0 ? false : true">

			 <i class="fa fa-paperclip"></i> {{trans('attach')}}</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from "axios"

	export default {

		name : 'release-associate-change',

		description : 'Release Change Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

			releaseId : { type : String | Number, default : ''},

			updateReleaseData : { type : Function, default : ()=>{}}
		},

		data(){

			return {

				change : [],

				loading : false,
			}
		},

		methods : {

			onChange(value, name) {

				this[name] = value;
			},

			onSubmit() {

				this.loading = true 

				const data = {};

				var ids = [];

				for(var i in this.change){

					ids.push(this.change[i].id)
				}

				data['change_ids'] = ids;

				axios.post('/service-desk/api/release/attach-change/'+this.releaseId,data).then(res => {

					window.eventHub.$emit('updateReleaseAssociates');
					
					this.loading = false
					
					this.onClose();

					successHandler(res,'release-view');

					this.updateReleaseData();
						
				}).catch(err => {
						
					this.loading = false
						
					this.onClose();

					errorHandler(err,'release-view')
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