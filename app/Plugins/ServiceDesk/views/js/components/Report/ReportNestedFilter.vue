<template>
	
	<div>
		
		<div class="row" v-if="loading">
			
			<custom-loader :duration="4000"></custom-loader>
		</div>

		<alert/>
		
		<div class="box box-primary">

			<div class="box-header with-border">
					
				<h3 class="box-title">{{lang(title)}}</h3>

				<div class="pull-right">
					
					<button class="btn btn-primary save-btn" @click="filterAction('save')" type="button">

						<i :class="iconClass"> </i>&nbsp;{{lang(btnName)}}
					</button>
				</div>
			</div>

			<div class="box-container">
				
				<div class="box-header with-border">
					
					<h3 class="box-title">{{lang('report_info')}}</h3>
				</div>

				<div class="box-body">
					
					<div class="row">

						<text-field :label="lang('name')" :value="name" type="text" name="name"
							:onChange="onFieldChange" classname="col-xs-12" :required="true">
								
						</text-field>

						<text-field :label="lang('description')" :value="description" type="textarea" name="description"
							:onChange="onFieldChange" classname="col-xs-12">
						
						</text-field>
					</div>
				</div>
			</div>
			
			<div class="box-container">
				
				<div class="box-header with-border">
					
					<h3 class="box-title">{{lang('report_filter')}}</h3>
						
				</div>


				<div class="box-body">
					
					<asset-filter v-if="!reset" id="filter-box" :metaData="componentMetaData" 
						@selectedFilters="selectedFiltersMethod" from="report">
	        
	        </asset-filter>

				
				</div>

				
			</div>
			
			<div class="box-container">
				
				<div class="box-header with-border">
					
					<h3 class="box-title">{{lang('graph')}}</h3>
				</div>

				<div class="box-body">
					

					<div class="row">

						<static-select :label="lang('select_chart_type')"  :elements="chart_types" name="chart_type"
							:value="chart_type" classname="col-xs-6" :onChange="onFieldChange" :hideEmptySelect="true">
						
						</static-select>

						<chart v-if="showChart" :chartData="chartData" :type="chart_type"></chart>

						<div class="row" v-else id="reset">
				
							<loader :animation-duration="4000" :size="60"/>
						</div>

					</div>				
				</div>
			</div>

			<div class="box-container">
				
				<div class="box-header with-border">
					
					<h3 class="box-title">{{lang('list_of_assets')}}</h3>
				</div>

				<div class="box-body">
					

					<div class="row">

						<agent-asset-index v-if="apiUrl" @columns-list="getColumns" :ApiUrl="apiUrl"></agent-asset-index>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'

	import { errorHandler, successHandler } from "helpers/responseHandler";

	import { validateAssetReportSettings } from "../../validator/assetReportValidation.js";

	import  { getIdFromUrl } from 'helpers/extraLogics';

	export default {
		
		name : "report-nested-filter",

		description : "Report Nested Filter component",

		data(){
			
			return{

				title : 'create_asset_report',

				iconClass : 'fa fa-save',

				btnName : 'save',

				filter_id : '',

				editData : '',

				apiUrl: '',

				name: '',

				description: '',
				
				selectedFilters: {},

				componentMetaData: [
				{
					section : [
						
						{ 
							name: 'asset_ids',
							url: '/service-desk/api/asset-list?meta=true',
							label: 'assets',
						},

						{ 
							name: 'asset_type_ids',
							url: '/service-desk/api/dependency/asset_types',
							label: 'asset_types',
						},

						{ 
							name: 'used_by_ids',
							url: 'api/dependency/users?meta=true',
							label: 'used_by',
						},
					]
				},
				
				{
					section : [
						
						{ 
							name: 'managed_by_ids',
							url: 'api/dependency/agents?meta=true',
							label: 'managed_by',
						},
						
						{ 
							name: 'location_ids',
							url: 'api/dependency/locations',
							label: 'location',
						},

						{ 
							name: 'dept_ids',
							url: 'api/dependency/departments',
							label: 'departments',
						},
					]
				},

				{
					section : [
						
						{ 
							name: 'org_ids',
							url: 'api/dependency/organizations',
							label: 'organizations',
						},
						
						{ 
							name: 'product_ids',
							url: '/service-desk/api/dependency/products',
							label: 'products',
						},

						{ 
							name: 'ticket_ids',
							url: 'api/dependency/tickets',
							label: 'ticket',
						},
					]
				},

				{
					section : [
						
						{ 
							name: 'impact_type_ids',
							url: '/service-desk/api/dependency/impacts',
							label: 'impact_types',
						},

						{ 
		        	name: 'assigned_on',
		          type: 'date',
		          label: 'assigned_on',
		          timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
		          pickers : true,
		        },
					]
				},   
				],

				sections : [1,2,3],

				loading : true,

				fieldsArr  :[],

				chart_types : [ { id:'bar', name:"Bar" } ,{ id:"pie", name:"Pie" },
												{ id:"horizontal", name:"Horizontal Bar" },{ id:"doughnut", name:"Doughnut" }],

				chart_type : 'bar',

				showChart : false,

				chartData  : '',

				chartApiUrl : '',

				columns : '',

				reset : false
			}
		},

		beforeMount() {

			const path = window.location.pathname
			
			this.getValues(path);
		},

		methods:{

			selectedFiltersMethod(value){ 

				if(value === 'resetEvent') {
					
					return this.onReset();
				}
				
				return this.applyFilter(value) 
      },

			getColumns(value){

				this.columns = value
			},

			getValues(path){

				const filterId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_asset_report'

					this.iconClass = 'fa fa-refresh'

					this.btnName = 'update'

					this.getInitialValues(filterId)

				} else {

					this.apiUrl = '/service-desk/api/asset-list?config=true&';
					
					this.chartApiUrl = '/service-desk/api/asset-list?count=true&';

					this.filterData();

					this.getChartData();

					this.loading = false;

					this.reset = false;
				}
			},

			getInitialValues(id){

				this.loading = true
				
				this.reset = true

				axios.get('/service-desk/api/reports/'+id).then(res=>{
					
					this.loading = false;

					this.reset = false;
					
					this.editData = res.data.data.report_filter;

					this.filter_id = this.editData.id
					
					this.name = this.editData.name;

					this.description = this.editData.description;

					this.filterData(this.editData.filter_meta);

					for( var i in this.editData.filter_meta){

						this.selectedFilters[this.editData.filter_meta[i].key] = this.editData.filter_meta[i].value_meta; 
					}
					this.applyFilter(this.selectedFilters)

				}).catch(error=>{

					this.loading = false;

					this.reset = false;
				});
			},

			onChange(value, name){

				this.selectedFilters[name] = value;
				
			},
	
			onFieldChange(value, name){

				this[name] = value;

				if(name === 'chart_type'){

					if(value){

						this.showChart = false;

						this.getChartData();
					}
				}
			},

			isValid() {

				const { errors, isValid } = validateAssetReportSettings(this.$data);
				
				if (!isValid) {
				
					return false;		
				}
				
				return true;
			},

			filterAction(type){

				if(this.isValid()) {

					this.storeFields(this.selectedFilters)
				}
			},

			applyFilter(value){

				this.selectedFilters = value;

				this.loading = true;

				let baseUrlForFilter = '/service-desk/api/asset-list?config=true&';

				this.chartApiUrl= '/service-desk/api/asset-list?count=true&';
						
				let params = this.commonMethod(value,'apply');

				if(params[params.length-1] === '&') {
					
					params = params.slice(0, -1);
				}

				this.apiUrl = baseUrlForFilter + params;

				this.chartApiUrl = this.chartApiUrl + params;	

				this.getChartData(this.chartApiUrl);

				this.loading = false; 
			},

			commonMethod(value,from){

				let params = '';

				for( var i in this.componentMetaData){

					for(var j in this.componentMetaData[i].section){

						if(value[this.componentMetaData[i].section[j].name]){

							this.componentMetaData[i].section[j].value = value[this.componentMetaData[i].section[j].name];

							if(from === 'apply'){

								if(this.componentMetaData[i].section[j].name === 'assigned_on'){

	        				let create = value.assigned_on;

									let start = create[0] !== null ? moment(create[0]).format('YYYY-MM-DD+HH:mm:ss') : '';

									let end =  create[1] !== null ? moment(create[1]).format('YYYY-MM-DD+HH:mm:ss') : '';

									params += 'assigned_on_begin=' + start + '&assigned_on_end=' + end + '&';

	        			} else{

	        				value[this.componentMetaData[i].section[j].name].forEach(function(element, index) {

										params +=  this.componentMetaData[i].section[j].name+'[' + index + ']=' + element.id + '&'
									},this);
	        			}
							} else {

								let field = this.componentMetaData[i].section[j];
								
								this.fieldsArr.push({ key : field.name, value :field.value.map(a => a.id), value_meta : field.value})
							}
						}     		
					}
				}

				return params;

			},

			storeFields(value){

				this.loading = true;

				this.commonMethod(value,'store')

				const data = {};

				data['name'] = this.name;

				data['description'] = this.description;

				data['type'] = 'asset';

				data['fields'] = this.fieldsArr;

				data['report_column_keys'] = this.columns;
				
				if(this.filter_id != ''){
					
					data['id'] = this.filter_id;	
				}
		
				axios.post('/service-desk/api/reports/create',data).then((response)=> {
			
					this.loading = false;

					successHandler(response);

					if(!this.filter_id){

						this.redirect('/service-desk/reports/assets');
					}

				}).catch((err) => {
			
					this.loading = false;

					errorHandler(err);
				})
			},

			onReset() {

				this.fieldsArr = [];

				this.selectedFilters = {};
				
				this.apiUrl = '/service-desk/api/asset-list?config=true&';

				this.chartApiUrl= '/service-desk/api/asset-list?count=true&';
				
				this.getChartData(this.chartApiUrl);

				this.componentMetaData = this.componentMetaData.map(function (obj) { 
							
					obj.section.map(function(row){

						row.elements = [];
							
						row.isMultiple = true;
						
						row.isPrepopulate = false;
						
						row.value = '';
						
						row.className = 'col-xs-4';
					})

					return obj;
				
				},this);
			},

			getChartData(){

				this.showChart = false;

				axios.get(this.chartApiUrl).then(res=>{
				
					this.showChart = true
				
					this.chartData = res.data
				
				}).catch(err=>{ this.showChart = true })
			},

			filterData(data){

				const self = this;

				this.componentMetaData.map(function (obj) { 

					obj.section.map(function(row){

						if(data){
							
							data.map(function(val){
								
								if(val.key === row.name){
								
									row.value = val.value_meta
								}
							})
						} else {
							row.value = '';
						}
						row.elements = [];
						row.isMultiple = true;
						row.isPrepopulate = false;
						row.className = 'col-xs-4';
					})
				});	

				this.loading = false;
			}
		},

		components: {

			'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),

			'static-select': require("components/MiniComponent/FormField/StaticSelect"),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'agent-asset-index' : require('./AgentAssetIndex.vue'),

			'asset-filter': require("../Asset/AssetFilter"),

			"custom-loader": require("components/MiniComponent/Loader"),

			'alert': require("components/MiniComponent/Alert"),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			'chart':require('./Charts/Chart.vue'),
		}
	};
</script>

<style>

	#reset{
		height : 150px
	}
	.save-btn {
 	 margin: 0px 0px 0px 10px;
	}

	.container {
    max-width: 800px;
    margin: 0 auto;
  }
 
  .Chart {
    padding: 20px;
    border-radius: 20px;
    margin: 50px 0;
  }

  #type{
  	visibility: hidden;
  }
</style>
