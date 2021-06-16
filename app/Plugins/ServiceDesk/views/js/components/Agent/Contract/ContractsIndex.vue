<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary contracts-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="contracts-list" class="box-title">{{lang('list_of_contracts')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary export-contract" :href="basePath()+'/service-desk/contract/export'">

							<span class="fa fa-download"> </span> {{lang('export')}}
						</a>

						<a class="btn btn-primary add-contract" :href="basePath()+'/service-desk/contracts/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_contract')}}
						</a>

						<a id="advance-filter-btn" class="btn btn-primary pull-right round-btn" @click = "toggleFilterView">
							<span class="glyphicon glyphicon-filter"></span>
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="contracts_list">
				
				<contracts-filter id="filter-box" v-if="isShowFilter" :metaData="filterOptions" :appliedFilters="appliedFilters"
					@selectedFilters="selectedFilters">
        
        		</contracts-filter>

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="contracts-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import { mapGetters } from 'vuex'

	import { lang } from 'helpers/extraLogics'

	import moment from 'moment';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	import  { getApiParamsFromArray } from 'helpers/extraLogics';

	export default {

		name : 'contracts-list',

		description : 'Contracts lists table component',


		data: () => ({

			columns: ['identifier','name','cost','expiry','contract_status','contract_renewal_status','vendor','contract_type', 'actions'],

			options: {},

			apiUrl:'/service-desk/api/contract-list',

			filterOptions: [
				{
					section : [
						
						{ 
							name: 'contract_ids',
							url: '/service-desk/api/dependency/contracts',
							label: 'contracts',
						},

						{ 
							name: 'contract_type_ids',
							url: '/service-desk/api/dependency/contract_types',
							label: 'contract_type',
						},

						{ 
							name: 'approver_ids',
							url: '/api/dependency/agents?meta=true',
							label: 'approver',
						},
					]
				},

				{
					section : [
						
						{ 
							name: 'vendor_ids',
							url: '/service-desk/api/dependency/vendors',
							label: 'vendors',
						},

						{ 
							name: 'license_type_ids',
							url: '/service-desk/api/dependency/license_types',
							label: 'license_type',
						},

						{ 
							name: 'status_ids',
							url: '/service-desk/api/dependency/contract_statuses',
							label: 'status',
						},
					]
				},

				{
					section : [
						
						{ 
							name: 'renewal_status_ids',
							url: '/service-desk/api/dependency/contract_renewal_statuses',
							label: 'renewal_status',
						},

						{ 
							name: 'owner_ids',
							url: '/api/dependency/agents',
							label: 'owner',
						},

						{ 
							name: 'asset_ids',
							url: '/service-desk/api/dependency/assets',
							label: 'assets',
						},
					]
				},

				{
					section : [

						{ 
							name: 'notify_agent_ids',
							url: '/api/dependency/agents?meta=true',
							label: 'notify_agents',
						},

						{ 
							name: 'cost',
							label: 'cost',
							type: 'number'
						},

						{ 
							name: 'notify_in_days',
							label: 'notify_before',
							type: 'number'
						},
					]
				},

				{
					section : [

						{ 
		        	name: 'contract_start_date',
		          type: 'date',
		          label: ['contract_start_date', 'contract_start_date_within_last'],
		          timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
		          pickers : true,
		          range : true
		        },

		        { 
		        	name: 'contract_end_date',
		          type: 'date',
		          label: ['contract_end_date', 'contract_end_date_within_last'],
		          timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
		          pickers : true,
		          range : true
		        },

		        { 
		        	name: 'created_at',
		          type: 'date',
		         	label: ['created_at', 'created_within_last'],
		          timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
		          pickers : true,
		          range : true
		        },
					]
				},

				{
					section : [

		        { 
		        	name: 'updated_at',
		          type: 'date',
		         	label: ['updated_at', 'updated_within_last'],
		          timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
		          pickers : true,
		          range : true
		        },
					]
				}  
			],

			isShowFilter : false,

			appliedFilters : {}
		}),

		computed :{
		
			...mapGetters(['formattedTime','formattedDate'])
		},

		created() {

			window.eventHub.$on('updateFilter',this.updateFilter);
		},
		
		beforeMount() {

			this.filterData();

			const self = this;

			this.options = {

				columnsClasses : {

					contract_number: 'contract-number',

					name: 'contract-name', 

					cost: 'contract-cost',

					expiry: 'contract-expiry',

					contract_status: 'contract-status', 

					contract_renewal_status: 'contract-renew',

					vendor: 'contract-vendor',

					contract_type: 'contract-type', 

					actions: 'contract-action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				texts: { filter: '', limit: '' },

				templates: {

					expiry(createElement,row) {
						
		            if(row.expiry && row.expiry.title){

		               return createElement('span', {

		               	attrs: {
              
				                title: row.expiry.timestamp ? self.formattedTime(row.expiry.timestamp) : '',
				            }
		            	},row.expiry.title);
		            } else { return '---'}
					},

					contract_status(h,row) {

						return row.contract_status ? lang(row.contract_status.name) : '---' 
					},

					contract_renewal_status(h,row){

						return row.contract_renewal_status ? lang(row.contract_renewal_status.name) : '---' 
					},

					vendor(h,row) {

						return row.vendor ? row.vendor.name : '---' 
					},

					contract_type(h,row) {

						return row.contract_type ? row.contract_type.name : '---' 
					},

					actions: 'table-actions'
				},

				sortable : ['identifier','name', 'contract_number', 'expiry','cost'],
				
				filterable : ['contract_number','name','cost','expiry','contract_status','contract_renewal_status','vendor','contract_type'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
	      
	        return {
	          
	          'sort-field' : data.orderBy ? data.orderBy : 'id',
	          
	          'sort-order' : data.ascending ? 'desc' : 'asc',
	          
	          'search-query' : data.query,
	          
	          page : data.page,
	          
	          limit : data.limit,
	        }
	      },

			 	responseAdapter({data}) {

			 		self.setPlaceholders(data);

					return {
					
						data: data.data.contracts.map(data => {

						data.status = true;
						
						data.view_url = self.basePath() + '/service-desk/contracts/' + data.id + '/show';

						if(data.is_edit){
							
							data.edit_url = self.basePath() + '/service-desk/contracts/' + data.id + '/edit';
						}
						if(data.is_delete){
						
							data.delete_url = self.basePath() + '/service-desk/api/contract/' + data.id;
						}
						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		methods:{
			
			updateFilter(data) {

				this.filterOptions.map(function (obj) { 
				  
				  obj.section.map(function(row){

				  		if(data.name == row.name) {
				  			
				  			 row.value = data.value;
				  		}
				  })
				});
			},

			filterData(){
				
				this.filterOptions.map(function (obj) { 
				  
				  obj.section.map(function(row){

				  	row.elements = [];
		        row.isMultiple = true;
		        row.isPrepopulate = false;
		        row.value = '';
		        row.className = 'col-xs-4';
				  })
				});
			},

			setPlaceholders(data){

				this.filterOptions.map(function (obj) { 
				  
					obj.section.map(function(row){

				  	 	if(row.name == 'cost') {

		        			row['min_placeholder'] = data.data.minimum_cost;
		        			
		        			row['max_placeholder'] = data.data.maximum_cost;
		        		}

		        		if(row.name == 'notify_in_days'){

		        			row['min_placeholder'] = data.data.minimum_notify_before;
		        			
		        			row['max_placeholder'] = data.data.maximum_notify_before;
		        		}
				 	})
				});
			},

			selectedFilters(value){ 

				if(value === 'closeEvent') {
				
					return this.isShowFilter = false;
				}
				if(value === 'resetEvent') {
					
					return this.resetFilter();
				}
				
				return this.applyFilter(value) 
      },

      resetFilter(){

      	this.apiUrl = '/service-desk/api/contract-list';
        	
        this.filterOptions = this.filterOptions.map(function (obj) { 
				  
				  obj.section.map(function(row){

				  	row.elements = [];
		        row.isMultiple = true;
		        row.isPrepopulate = false;
		        row.value = '';
		        row.className = 'col-xs-4';
				  })

				  return obj;
				});
      },

      applyFilter(value){
      	this.appliedFilters = value;
      	let baseUrlForFilter = '/service-desk/api/contract-list?';
        
        let params = getApiParamsFromArray(this.filterOptions,value);

        if(params[params.length-1] === '&') {
         
          params = params.slice(0, -1);
        }

        this.apiUrl = baseUrlForFilter + params;

        this.isShowFilter = false;
      },

      toggleFilterView() {
        
        this.isShowFilter = !this.isShowFilter;
      },
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),
			
			"alert": require("components/MiniComponent/Alert"),

			'contracts-filter': require("components/Extra/DataTableFilter")
		}
	};
</script>

<style type="text/css">

	.contract-number,.contract-name,.contract-cost,.contract-expiry,.contract-status,.contract-renew,.contract-vendor,.contract-type{ max-width: 250px; word-break: break-all;}

	#contracts_list .VueTables .table-responsive {
		overflow-x: auto;
	}

	#contracts_list .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
	.contracts-table {
		padding: 0px !important;
	}
	.add-contract{
		float: right;
	}
	.round-btn {
		margin-right: 5px;
	}
	.export-contract {
		margin-left: 5px;
		float: right;
	}
</style>