<template>
	<div> 
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
			
			<div slot="title" id="alert_top">

				<alert componentName="Widget-Modal"/>
			
			</div>

			<div slot="title">

				<h4>{{lang(modalMode)}}</h4>
			
			</div>
			
		

			<div slot="fields" >
				

				<div v-if="modalMode === 'edit'">
	                      <text-field :label="lang('title')" :onChange="onChange" :value="title" type="text" name="title" 
							classname="col-xs-12" >
						
						</text-field>
       

					<ck-editor :value="value" type="text" :onChange="onChange" name="value" 
						label="Value" classname="col-sm-12"  >
						
					</ck-editor>
				</div>
				<div v-if="modalMode === 'view'">
					<div v-if="value">
	                      <text-field :label="lang('title')" :onChange="onChange" :value="title" type="text" name="title" 
							classname="col-xs-12"  :disabled="true">
						
						</text-field>
       

					<ck-editor :value="value" type="text" :onChange="onChange" name="value" 
						label="Value" classname="col-sm-12" v-html="value" >
						
					</ck-editor>
					</div>
					<div v-if="!value" class="modal-message">
						<p>{{trans('no_data_found')}}</p>
					</div>
				</div>	
	



        <!-- TODO add modal box content here -->

      


	 
			

</div>	
						
			<div slot="controls">


<button v-if="modalMode === 'edit'" type="button" id="submit_btn" @click="onSubmit()" class="btn btn-primary"><i :class="{'fa fa-save': !loading, 'fa fa-spinner fa-spin': loading}" aria-hidden="true"></i> {{lang('update')}}</button>				
			</div>

		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios';

	export default {
		
		name : 'Widget-Modal',

		description : 'Widget Modal component',

		props:{
			data : { type : Object, required : true },

			/**
			 * status of the modal popup
			 * @type {Object}
			 */
      showModal:{type:Boolean,default:false},

			/**
			 * The function which will be called as soon as user click on the close button        
			 * @type {Function}
			*/
			onClose:{type: Function},

      /**
       * Mode of the modal, edit/view
       */
			modalMode : { type: String , required : true },
			
    
		},

		data:()=>{
			return {
				/**
				 * width of the modal container
				 * @type {Object}
				 */
				containerStyle:{
					width:'500px',
					
				},

				title:"",
				value:"",

				/**
				 * initial state of loader
				 * @type {Boolean}
				 */
				loading:false,

			}
		},

		beforeMount(){
			this.title=this.data.title
			this.value=this.data.value
		},

		created(){
		// getting locale from localStorage
			this.lang_locale = localStorage.getItem('LANGUAGE');
			this.ticketId = this.getStoredTicketId;
		},

		methods:{
			 onChange(value, name){
            this[name] = value;
        },
		/**
		 * api calls happens here
		 * @return {Void} 
		 */
			onSubmit() {
						this.loading = true;
						let params = this.getSubmitParams();
						axios.post('api/update-widget/' + this.data.id, params)
							.then(res => {
								this.loading = false;
								this.onClose();
								successHandler(res, 'dataTableModal');
								window.eventHub.$emit('refreshData')
							})
							.catch(err => {
								this.loading = false;
								errorHandler(err, 'Widget-Modal');
							});
					},
		getSubmitParams(){
			 let params = {
				 title: this.title,
				 value: this.value,
				 
			}
			return params;
			
		},
		
		
	},
	components:{
		'text-field': require('components/MiniComponent/FormField/TextField'),
		'modal':require('components/Common/Modal.vue'),
		'alert' : require('components/MiniComponent/Alert'),
		'loader':require('components/Client/Pages/ReusableComponents/Loader'), 
		'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),
	}
	

};
</script>

<style type="text/css" scoped>
.has-feedback .form-control {
	padding-right: 0px;
}
#H5{
	margin-left:16px; 
}
.fulfilling-bouncing-circle-spinner{
	margin: auto;
}
.margin {
	margin-right: 16px;margin-left: 0px;
}
 #alert_top{
	margin-top:20px;
	font-size: 14px;
}

#containerStyle{
width: 500px;
	
	overflow-y: hidden;
}

.modal-message p {
	text-align: center;
}

</style>