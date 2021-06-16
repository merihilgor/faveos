<template>

	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			
			<div slot="title" id="alert_top">

				<alert componentName="problem-modal"/>
			
			</div>

			<div slot="title">
			
				<h4>{{lang(title)}}</h4>
			
			</div>
			
			<!-- adding new problem -->

			<div slot="fields" v-if="title === 'attach_new_problem'">

				<div class="row" v-if="createLoader" id="load_margin">

					<loader :duration="4000"></loader>
				</div>

				<problem v-else box_class="box" :styleObj="styleObj" :title_val="titleParsed" :closePopup="onClose"
				 	ref="create_problem" alert="timeline">
					
				</problem>	

			</div>

			<!-- adding existing problem -->

			<div slot="fields" v-if="title === 'attach_existing_problem'" id="dynamic_select">

				<div class="row">
					<dynamic-select :label="lang('problems')" :multiple="false" name="problem" :prePopulate="true"
						classname="col-xs-12" apiEndpoint="/service-desk/api/dependency/problems" :value="problem" :onChange="onChange" 
						:required="true">
					</dynamic-select>	
				</div>

			</div>

			<div class="row" slot="fields" v-if="loading === true">

				<custom-loader :duration="4000"></custom-loader>
			
			</div>
						
			<div slot="controls">
				<button type="button" id="submit_btn" @click = "onSubmit" class="btn btn-primary" :disabled="isDisabled">
					<i class="fa fa-save"></i> {{lang('attach')}}
				</button>
			</div>

		</modal>

	</div>

</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {
		
		name : 'problem-modal',

		description : 'Problem Modal component',

		props:{
			
			/**
			 * status of the modal popup
			 * @type {Object}
			 */
			showModal:{type:Boolean,default:false},

			/**
			 * @type {Number}
			 */
			title:{type:String , default : '' },

			/**
			 * The function which will be called as soon as user click on the close button        
			 * @type {Function}
			*/
			onClose:{type: Function},

			/**
			 * ticket data
			 */
			data : { type : Object|String, required : true}

		},

		data:()=>({
			
			
			/**
			 * buttons disabled state
			 * @type {Boolean}
			 */
			isDisabled:false,

			/**
			 * width of the modal container
			 * @type {Object}
			 */
			containerStyle:{ width:'800px' },

			/**
			 * initial state of loader
			 * @type {Boolean}
			 */
			loading:false,

			/**
			 * size of the loader
			 * @type {Number}
			 */
			size: 60,

			styleObj : {
				'max-height': '350px',
				'overflow-y': 'scroll',
				'overflow-x': 'hidden',
				'border-top': '0px solid #d2d6de',
				'margin-top' : '-20px',
				'margin-bottom' : '-21px'
			},

			createLoader : false,

			// selected existing problem
			problem  : ''
		}),
		
		watch : {
			problem(newValue,oldValue){
				this.isDisabled = this.title === 'attach_existing_problem' && newValue !== '' ? false : true
				return newValue
			}
		},

		computed:{
			
			titleParsed(){
				return JSON.parse(this.data).title;
			},
			ticketId(){
				return JSON.parse(this.data).id;
			},

			alertName() {

				return JSON.parse(this.data).alertName ? JSON.parse(this.data).alertName : 'problem-modal'
			}
		
		},

		beforeMount(){
			
			this.isDisabled = this.title === 'attach_existing_problem' && this.problem === '' ? true : false
			
			if(this.title == 'attach_new_problem'){

				this.createLoader = true;

				setTimeout(()=>{

					this.createLoader = false;
				},1000)
			}
		},

		methods:{
		/**
		 * api calls happens here
		 * @return {Void} 
		 */
		onSubmit(){
			
			if(this.title === 'attach_new_problem'){
				
				this.$refs.create_problem.onSubmit();
			}else{
			
				this.loading = true
			
				axios.post('/service-desk/api/problem-attach-ticket/'+this.ticketId+'/'+this.problem.id).then(res=>{
			
					successHandler(res,this.alertName)
			
					this.loading = false
					
					window.eventHub.$emit('actionDone');

					this.onClose();
				
				}).catch(error=>{
					
					errorHandler(error,this.alertName)
					
					this.loading = false
				
				})
			}	
		},

		/**
		 * to get changed value from child component
		*/
		onChange(value,name){
			
			this[name] = value
		
		}

	},

	components:{

		'modal':require('components/Common/Modal.vue'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		'custom-loader' : require('components/MiniComponent/Loader'),

		'loader' : require('components/Client/Pages/ReusableComponents/Loader'),
		
		'problem':require('../../Problem/Problem.vue'),
		
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
.fulfilling-bouncing-circle-spinner{
	margin: auto !important;
}
.margin {
	margin-right: 16px !important;margin-left: 0px !important;
}
.spin{
	left:0% !important;right: 43% !important;
 }
 #alert_msg {
	margin: 0px 5px 30px 5px;
}
#dynamic_select{
			margin-left: 15px;
		margin-right: 15px;
}
#alert_top{
	margin-top:20px;
	font-size: 14px !important;
}
#load_margin { margin-top: 50px; margin-bottom: 50px; }
</style>