<template>
	<div>
		 <!-- alert message which only gets mounted when vuex has non empty alert values -->
    <alert/>
		

		<div class="box box-primary">

			<!-- form layout design-->
			<form-layout headerClass="box-header with-border" title="time-track-settings">
				<div slot="fields" v-if="loading === false" class="form-group">
					

					
<!-- for enable and disable reply page track settings -->
					<!-- <div v-if="status"> -->
					<div> 
						<label for="replyForm" class="col-sm-3 control-label">{{lang('make_it_visible_to_reply_form')}}</label>
						<div class="col-sm-2">
							<status-switch name="additional" :value="additional" :onChange="onChange"
								classname="pull-left" :bold="true">
							</status-switch>
						</div>
					</div>
				</div>
				<!--loader-->
			<div v-if="loading === true" class="row" slot="fields" style="margin-top:30px;margin-bottom:30px">
				<loader :class="{load: lang_locale == 'ar'}" :animation-duration="4000" :color="color" :size="size"/>
			</div>

			</form-layout>
		
		</div>
	</div>
</template>

<script type="text/javascript">
	
	import axios from 'axios'
	import {errorHandler, successHandler} from 'helpers/responseHandler'
	
	export default {
	
		name:'timetrack-settings',

		description:'time track settings page',
		
		props:{

		},
		components:{
			
			'form-layout': require('components/MiniComponent/FormLayout'),
		
			'status-switch':require('components/MiniComponent/FormField/Switch'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			'alert' : require('components/MiniComponent/Alert'),
		},
		beforeMount(){
			this.getInitialValues()
		},
		data:()=> ({

			/**
			* initial status for showing time tarck fields in agent panel reply form
			* @type {Boolean}
			*/
			additional:true,

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

			/**
			 * color of the loader
			 * @type {String}
			 */
			color: '#1d78ff',
			
			/**
			 * name of the button
			 * @type {String}
			 */
			btnName:'save',

			/**
			 * class of the button
			 * @type {String}
			 */
			btnClass:'fa fa-save',

			/**
			 * initial state of the button
			 * @type {Boolean}
			 */
			isDisabled:false,

			/**
			 * locale of  the user
			 * @type {String}
			 */
			lang_locale:''
		}),

		methods:{
			/**
			* gets initial state of states defined in this component
			* @return {void}
			*/
			getInitialValues(){
				this.loading = true
				this.isDisabled = true
				axios.get('time-track/get-settings').then(res => {
					this.updateStatesWithData(res.data.data)
					this.loading = false
					this.isDisabled = false
				}).catch(err => errorHandler(err))

			},

			/**
			* updates state data for this component
			* @param {Object} emailSettingsData    settings data object (from backend)
			* */
			updateStatesWithData(timeTrackSettingsData){
				const self = this
				const stateData = this.$data
				Object.keys(timeTrackSettingsData).map((key) => {
					//if vue state has a property with name same as 'key'
					if(stateData.hasOwnProperty(key)){
						self[key] = timeTrackSettingsData[key]
					}
				});
			},

				/**
				* for showing time track fields in reply form
				* populates the states corresponding to 'name' with 'value'
				* @param  {string} value
				* @param  {[type]} name
				* @return {void}
				*/
			onChange(value,name){
				this[name]=value;
				
				axios.post('time-track',{
					// status : this.status ? 1 : 0,
					additional : this.additional ? 1 : 0
					}).then(res=>{
						this.initialState();
						successHandler(res)
					}).catch(err => {
						this.initialState();
						errorHandler(err)
				})
			},

			/**
			 * initial state of the component
			 * @return {Void}
			 */
			initialState(){
				this.loading = false
				this.isDisabled = false
				this.btnName = 'save'
				this.btnClass = 'fa fa-save'
				document.documentElement.scrollTop = 0;
			},
	
		}
	};
</script>
<style type="text/css">
	.box{
		padding-bottom:1px !important;
		padding-top:6px !important;
	}
	.box-footer{
		border-top:none !important;
	}
</style>