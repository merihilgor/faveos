<template>

  <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title"><h4>{{lang(type)}}</h4></div>

		<div v-if="!loading && type == 'attach_existing_change'" slot="fields">

			<div class="row">

				<div class="col-xs-12">
					
					<dynamic-select :label="lang('change')" :multiple="false" name="change" :prePopulate="false"
						classname="col-xs-12" apiEndpoint="/service-desk/api/dependency/changes" :value="change" 
						:onChange="onChange" :required="true" :clearable="change ? true : false">

					</dynamic-select>
				</div>
			</div>
		</div>

		<div v-if="!loading && type == 'attach_new_change'" slot="fields" id="change_fields">

				<div v-if="showLoader" class="row" slot="fields" >

				  	<loader :animation-duration="4000" :size="60"/>
				</div>

				<change v-else from="problem_modal" ref="changeCreate" :problem_id="problemId" :onComplete="onCompleted" 
					alertName="problem-view">
					
				</change>
			</div>

		<div v-if="loading" class="row" slot="fields" >

		  	<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="isDisabled">

			 	<i class="fa fa-check"></i> {{lang('proceed')}}
			 </button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'problem-change-modal',

		description : 'Problem Change Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			problemId:{type:String|Number , default : ''},

			onClose:{type: Function},

			type : { type : String, default : '' },

			updateChangeData : { type  : Function }
		},

		data(){

			return {

				 isDisabled : this.type === 'attach_existing_change' ? true : false,

				  containerStyle:{ width: this.type === 'attach_existing_change' ? '500px' : '800px' },

				  loading:false,

				  showLoader : true,

				  change : ''
			  }
		 },

		 beforeMount() {

			this.updateLoader();	 	
		 },

		methods:{

			updateLoader(){

				if(this.type == 'attach_new_change'){

					setTimeout(()=>{

						this.showLoader = false;
					},1000)
				}	
			},

			onChange(value,name){

				this[name] = value;

				this.isDisabled = value ? false : true;
			},

			onSubmit(){

				if(this.type === 'attach_existing_change'){

					this.loading = true

					this.isDisabled = true

					axios.post('/service-desk/api/problem-attach-change/'+this.problemId+'/'+this.change.id).then(res=>{

					  	successHandler(res,'problem-view');
					  
					  	this.onClose();

					  	this.updateChangeData();

					  	window.eventHub.$emit('updateProblemAssociates');

						this.loading = false;

					  	this.isDisabled = true

					}).catch(err => {

					  	this.loading = false;

					  	this.isDisabled = true

					  	errorHandler(err,'problem-view')
					})
				} else {

					this.$refs.changeCreate.onSubmit();
				}
			},

			onCompleted(){
				 
				this.onClose();

				this.updateChangeData();

				window.eventHub.$emit('updateProblemAssociates');
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

		 	'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		 	"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

		 	 'change' : require('../../../../components/Agent/Changes/ChangesCreateEdit.vue')
		}
	};
</script>

<style scoped>
	
	#change_fields {
		margin-left: 15px;
    	margin-right: 15px;
    	max-height: 300px;
   	overflow-y: auto;
    	overflow-x: hidden;
		}
</style>