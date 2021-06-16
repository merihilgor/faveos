<template>

	<div class="box box-primary">

		<div class="box-header with-border">
					
				<h3 class="box-title">{{reportData.name}}</h3>

				<div class="pull-right">
					
					<button class="btn btn-danger pull-right" @click="deleteReport" >
					<i class="fa fa-trash"> </i> {{lang('delete')}}

				</button>
				
				<button class="btn btn-primary pull-right" @click="redirectToEditPage" style="margin-right: 2px;">
					<i class="fa fa-edit"> </i> {{lang('edit')}}

				</button>

				<div class="dropdown pull-right" id="export">

					<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">

						<i class="fa fa-download"> </i> {{lang('export')}}
							
						<span class="caret"></span>
					</button>
						
					<ul class="dropdown-menu">
							
						<li><a href="javascript:;" @click="exportAs('csv')">{{lang('csv')}}</a></li>

						<li><a href="javascript:;" @click="exportAs('excel')">{{lang('xlsx')}}</a></li>
					</ul>
				</div>
				</div>
			</div>

		<div class="box-body">

			<p v-if="reportData.description" class="text-muted well well-sm no-shadow" id="rep_desc">
        
        <strong>Description </strong> : {{reportData.description}}
      </p>

			
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
		


		
			


		



		<div class="row mt-2">
			
			<div class="col-sm-12">
				
				<agent-asset-index :ApiUrl="apiUrl" @columns-list="getColumns" @asset-report-data="getTableData" from="view">
					
				</agent-asset-index>

			</div>

		</div>
</div>

		
	</div>




</template>

<script>

	import axios from 'axios'

	export default {
		
		name : "report-view",

		description : "Report view component",



		beforeMount() {

			let urlParamString = '';
			axios.get('service-desk/api/reports/'+this.getIDfromURL())
			.then((resp) => {
				this.reportData = resp.data.data.report_filter

				for(const k of resp.data.data.report_filter.filter_meta) {
					k.value.forEach((item,index) => {
						urlParamString += k.key+"["+index+"]="+item+"&"
						this.chartApiUrl += urlParamString
						this.apiUrl += urlParamString
						urlParamString = ''
					})

				}




				if(this.chartApiUrl.charAt(this.chartApiUrl.length-1) == '&') {
					this.chartApiUrl = this.chartApiUrl.slice(0,-1)
				}

				this.getChartData();
				
				if(this.apiUrl.charAt(this.apiUrl.length-1) == '&') {
					this.apiUrl = this.apiUrl.slice(0,-1)
				}
				
			})
			.catch((err) => {
				console.log(err);
			})
		},

		data(){
			
			return{

				layout : {
					portal:{
						"client_header_color":"#009aba",
						"client_button_color":"#009aba",
						"client_button_border_color":"#00c0ef",
						"client_input_fild_color":"#d2d6de"
					}
				},

				chart_types : [

					 { id:'bar', name:"Bar" },
					 { id:"pie", name:"Pie" },
					 { id:"horizontal", name:"Horizontal Bar" },
					 { id:"doughnut", name:"Doughnut" }
				],

				chart_type : 'bar',

				reportData : '',

				apiUrl: '/service-desk/api/asset-list?config=true&',

				chartApiUrl: '/service-desk/api/asset-list?count=true&',

				filterName: '',

				filterDescription: '',
				
				selectedFilters: {},

				close_on_select: !this.multiple,

				isShowFilter: false,

				showChart : false,

				chartData  : '',

				columns : '',

				table : '',

			}
		},

		

		methods:{

			getColumns(value){

				this.columns = value
			},

			getTableData(value){

				this.table = value
			},

			onChange(value, name){

				this.selectedFilters[name] = value;
			},

			getChartData(){

				this.showChart = false;

				axios.get(this.chartApiUrl).then(res=>{
				
					this.showChart = true
				
					this.chartData = res.data
				
				}).catch(err=>{ this.showChart = true })
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

			redirectToEditPage() {
				this.redirect("/service-desk/reports/assets/edit/"+this.getIDfromURL())
			},

			getIDfromURL(){
				let segments =  window.location.pathname.split('/');
				return segments[segments.length-1];
			},

			deleteReport() {

				let url = "/service-desk/api/reports/"+this.getIDfromURL();
				
				axios.delete(url)
				.then((resp) => {
					if(resp) {
						
						this.redirect("/service-desk/reports/assets");
					}
				})
				.catch(err => console.log(err))

			},

			exportAs(type){
				
				// let params = {filter_id:this.getIDfromURL()}

				window.open(this.basePath() + '/service-desk/api/export/'+type+'?filter_id=' +this.getIDfromURL())

				// axios.get('/service-desk/api/export/'+type,{params}).then(res=>{

				// 	window.location.reload();
				// })
			}


			
	  },

			



		

		components: {
			

			'agent-asset-index' : require('./AgentAssetIndex.vue'),

			'static-select': require("components/MiniComponent/FormField/StaticSelect"),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'chart':require('./Charts/Chart.vue'),

			


		}
	};
</script>

<style>
	
	.btn-group{
		padding: 0px 0px 10px 5px;
	}
	.single-btn{
		padding-left: 5px;
	}
	.round-btn {
		border-radius: 3px;
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
  #export{
  	margin-right: 2px;
  }
  #rep_desc{
  	margin-right: 10px;
  	margin-left: 10px;
  }
</style>
