<template>
	<div> 
		<!-- alert coomponent -->
		<alert/>
		<!-- business hours page fields -->
		<div class="box box-primary">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-4">
						<h2 class="box-title">{{ lang(page_title) }}</h2>
					</div>
				</div>       <!-- /.box-header -->
			</div>
		
			<div class="box-body">
				<div class="row" v-if="loading === false">
					<!-- title -->
					<text-field :label="lang('title')" :value="name" type="text" name="name"
					:onChange="onChange" classname="col-xs-6" :required="true"></text-field>
					<!-- timezone -->
					<dynamic-select :label="lang('time_zone')" :multiple="false" name="timezone" :required="true"  classname="col-xs-6" apiEndpoint="/api/dependency/time-zones" :value="timezone" :onChange="onChange"
					:clearable="timezone ? true : false">
					</dynamic-select>
				
					<div class="col-md-12" style="margin-left:-14px">
						<!-- description -->
						<text-field :label="lang('description')" :value="description" type="textarea" name="description"
								:onChange="onChange" classname="col-xs-5" :required="true"></text-field>
						<!-- hour select radio button -->
						<radio-button :options="radioOptions" :label="lang('hours')" name="hours" :value="hours"
								:onChange="onChange" classname="form-group col-xs-4" ></radio-button>
						<!-- status switch -->
						<div class="form-group col-xs-3">
							<label for="status">{{lang('status')}}</label><span style="color:red"> *</span><br>
							<status-switch name="status" :value="status" :onChange="onChange" classname="pull-left" :bold="true">
							</status-switch>   
						</div> 
					</div>	
					<!-- for open hours table -->
					<div v-if="hours" class="col-md-12">
						<div class="box-container">
							<div class="box-header with-border with-switch">
								<h3 for="select hours" class="box-title">Open hours</h3>
							</div>
							<hours-table v-for="(day,index) in dateWithTime"  :key="day.days" :day="day" :index="index" :onHoursChange="onHourChange" :onClosed="onClose" :onOpenfixed="onOpenfix"></hours-table>
						</div>
					</div>
					<!-- for holidays table -->
					<div for="holidays" class="col-md-12">
						<div class="box-container">
							<div class="box-header with-border with-switch">
								<h3 class="box-title">Yearly Holiday List
									<tool-tip :message="lang('holidays_will_be_ignored_when_calculating_SLA_for_a_ticket')" size="medium"></tool-tip>
								</h3>
							</div>
							<holiday-list :holidayData="holidays"></holiday-list>   
						</div>
					</div>
					<!-- default business hours checkbox -->
					<div class="form-group col-md-12" id="text">
						<label class="label_align1">
							<input class="checkbox_align" type="checkbox" name="default" @change="changeDefault" :checked="make_default == 'on'" :disabled="make_default == 'on'" >{{lang('make-default-business-hours')}}
						</label>
					</div>
							
					<div class="form-group col-md-12" id="text">
						<button type="button" id="submit_btn" class="btn btn-primary" @click="onSubmit"><i class="fa fa-save"> </i> {{lang(btnName)}}</button>
					</div>
				
				</div>
				<!-- loader component -->
				<div v-if="loading === true" class="row" style="margin-top:30px;margin-bottom:30px">
					<loader :animation-duration="4000" :color="color" :size="size"/>
				</div>
			</div> 
		</div>
	</div>
</template>

<script type="text/javascript">

import {validateBusinessHoursSettings} from "helpers/validator/businessHoursSettingsRules.js"

import moment from 'moment'

import {errorHandler, successHandler} from 'helpers/responseHandler'

import {getIdFromUrl} from 'helpers/extraLogics';

import axios from 'axios'

	export default {
		
		name : 'business-hours',

		description : 'Business hours create and edit component',

		props:{
			/**
			 * this function getting called when we change hours in the hours table
			 * @type {FUnction}
			 */
			onHoursChange:{type:Function},

			/**
			 * this function getting called when we unchecking days checkbox in the hours table
			 * @type {FUnction}
			 */
			onClosed:{type:Function},

			/**
			 * this function getting called when we checking open24hours checkbox in the hours table
			 * @type {FUnction}
			 */
			onOpenfixed:{type:Function},
		},

		beforeMount() {
			//getting initial values
			this.getInitialValues();
		},

		data: () => ({
			
			/**
			* initial status business hours
			* @type {Boolean}
			*/
			status:1,

			/**
			 * typed title
			 * @type {String}
			 */
			name:'',

			/**
			 * typed description
			 * @type {String}
			 */
			description:'',

			/**
			 * selected timezone
			 * @type {String}
			 */
			timezone:'',

			/**
			 * hours status (24*7 or select workingdays/hours)
			 * @type {Number|Boolean}
			 */
			hours:0,

			/**
			 * options for selecting working hours
			 * @type {Array}
			 */
			radioOptions:[{name:'247hours',value:0},{name:'select_working_days/hours',value:1}],

			/**
			 * holiday list
			 * @type {Array}
			 */
			holidays:[],

			/**
			 * for hours table
			 * @type {Array}
			 */
			dateWithTime:[],

			/**
			 * for dynamic select component( calling api call after clicking on the field)
			 * @type {Boolean}
			 */
			prePopulate : false,

			/**
			 * button name according to create or edit page
			 * @type {String}
			 */
			btnName:'',

			/**
			 * page title
			 * @type {String}
			 */
			page_title:'',

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
			 * api endpoint for submitting form
			 * @type {String}
			 */
			apiUrl:'',

			/**
			 * for making default business hour
			 * @type {Boolean}
			 */
			make_default: false
		}),
		
		methods:{
			
			/**
			 * getting initial values of state variables
			 * @return {[type]} [description]
			 */
			getInitialValues(){
				const path = window.location.pathname;
				this.loading = true;
				if (path.indexOf("edit") >= 0) {
					this.btnName = 'update'
					this.page_title = 'edit_business_hour'
					this.prePopulate = true
					const businessHoursId = getIdFromUrl(path);
					axios.get(`/api/business-hours/edit/${businessHoursId}`).then(res => {
						this.updateStatesWithData(res.data.data);
						this.apiUrl = 'sla/business-hours/store?id='+businessHoursId
						this.make_default = res.data.data.default;
						this.loading = false
					}).catch(err => { errorHandler(err); });
				} else {
					this.btnName = 'save'	
					this.page_title = 'create_new_business_hour'
					this.apiUrl = '/sla/business-hours/store'
					this.dateWithTime = [
						{days:'Sunday',status:'Open_custom',open_time:'00:00',close_time:'00:00'},
						 {days:'Monday',status:'Open_custom',open_time:'00:00',close_time:'00:00'},
						 {days:'Tuesday',status:'Open_custom',open_time:'00:00',close_time:'00:00'},
						 {days:'Wednesday',status:'Open_custom',open_time:'00:00',close_time:'00:00'},
						 {days:'Thursday',status:'Open_custom',open_time:'00:00',close_time:'00:00'},
						 {days:'Friday',status:'Open_custom',open_time:'00:00',close_time:'00:00'},
						 {days:'Saturday',status:'Open_custom',open_time:'00:00',close_time:'00:00'}];
					this.loading = false
					this.name = ''; this.description=''; this.hours = 0; this.status = 1; this.timezone = ''; this.holidays = [];
					this.make_default = false
				}
			},

			/**
			 * updates state data for this component
			 * @param {Object} emailSettingsData    settings data object (from backend)
			 */
			updateStatesWithData(businessHoursData) {
				const self = this;
				const stateData = this.$data;
				Object.keys(businessHoursData).map(key => {
					if (stateData.hasOwnProperty(key)) {
						self[key] = businessHoursData[key];
					}
				});
			},

			/**
			* populates the states corresponding to 'name' with 'value'
			* @return {void}
			*/
			onChange(value,name){
				this[name]=value;
				if(name === 'timezone'){
					if(value.id === undefined){
						this.timezone.id = 1; 
					}
				}
			},

			/**
			* populates the open and close time corresponding to 'index'
			* @return {void}
			*/
			onHourChange(type,day,index){
				if(type === 'open_time'){
					this.dateWithTime[index].open_time = day;
				} else {
					this.dateWithTime[index].close_time = day;
				}
			},

			/**
			 * for making default business hour (checked=>returns 'on',unchecked=>returns 'off')
			 * @return {Void}
			 */
			changeDefault(e){
				if(e.target.checked === true){
					this.make_default = 'on';
				} else {
						this.make_default = 'off';	
				}
				
			},

			/**
			 * for making particular day as closed
			 * @return {Void}  
			 */
			onClose(status,index){
				this.dateWithTime[index].status = status;
			},

			/**
			 * for making particular day as open 24*7
			 * @return {Void}  
			 */
			onOpenfix(status,index){
				this.dateWithTime[index].status = status;
			},
			
			/**
			* checks if the given form is valid
			* @return {Boolean} true if form is valid, else false
			* */
			isValid(){
				const {errors, isValid} = validateBusinessHoursSettings(this.$data);
				if(!isValid){
					return false
				}
				return true
			},

			/**
			 * sends ajax call for creating and updating business hours
			 * @return {[type]} [description]
			 */
			onSubmit(){
				this.status = this.status ? 1 : 0;
				if(this.make_default === 'on'){
					this.status = 1;
				}
				if(this.isValid()){
					const data={};
					data['name']=this.name;
					data['description']=this.description;
					data['time_zone']=this.timezone.id;
					data['status']=this.status;
					data['hours']=this.hours;
					data['holiday']=this.holidays;
					data['default_business_hours']=this.make_default;
					for(var i in this.dateWithTime){
						if(this.hours === 1){
							let hour = this.dateWithTime[i];
							if(hour.status === 'Closed'){
								data['type'+i] = "Closed";
							} 
							else if(hour.status === 'Open_fixed'){
								data['type'+i] = "Open_fixed";
							} 
							else{
								let x = moment(hour.open_time).format("hh:mm");
								let y = moment(hour.close_time).format("hh:mm");
								if( x === 'Invalid date' && y === 'Invalid date'){
									data['type'+i] = { 'from':hour.open_time,'to':hour.close_time};
								} 
								else if(x === 'Invalid date' && y !== 'Invalid date'){
									data['type'+i] = { 'from':hour.open_time,'to':moment(hour.close_time).format("HH:mm")};
								}
								else if(x !== 'Invalid date' && y === 'Invalid date'){
									data['type'+i] = { 'from':moment(hour.open_time).format("HH:mm"),'to':hour.close_time};
								} 
								else{
									data['type'+i] = { 'from':moment(hour.open_time).format("HH:mm"),'to':moment(hour.close_time).format("HH:mm")};
								}
							}
						} else {
							data['type'+i] = "Open_fixed";
						}
					}
					this.loading = true
					axios.post(this.apiUrl,data).then(res=>{
						this.loading = false
						successHandler(res)
						this.getInitialValues()
					}).catch(err=>{
						this.loading = false
						errorHandler(err);
					})
				}
			},
		},

		components:{

			'hours-table':require('./HoursTable'),

			'status-switch':require('components/MiniComponent/FormField/Switch'),

			'text-field': require('components/MiniComponent/FormField/TextField'),

			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),

			'holiday-list':require('./Holiday/HolidayIndex'),

			'alert' : require('components/MiniComponent/Alert'),

			"tool-tip": require("components/MiniComponent/ToolTip"),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		}
	};
</script>

<style type="text/css" scoped>
	.label_align {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: 700 !important; padding-top: 6px;
	}
	.label_align1 {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: 500 !important; padding-top: 6px;
	}
	.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}
	.box-header h3{
			font-family: Source Sans Pro !important
		}
	.box.box-primary {
			padding: 0px;
		}
	.box-container{
		margin: 0px !important; margin-bottom: 20px !important
	}
</style>