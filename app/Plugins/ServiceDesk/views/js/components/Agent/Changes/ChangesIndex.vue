<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary changes-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="changes-list" class="box-title">{{lang('list_of_changes')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-changes" :href="basePath()+'/service-desk/changes/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_change')}}
						</a>

						<a id="advance-filter-btn" class="btn btn-primary pull-right round-btn" @click = "toggleFilterView">
							<span class="glyphicon glyphicon-filter"></span>
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="changes_list">
				
				<changes-filter id="filter-box" v-if="isShowFilter" :metaData="filterOptions" :appliedFilters="appliedFilters"
					@selectedFilters="selectedFilters">
		  
				</changes-filter>

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="changes-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import { mapGetters } from 'vuex'

	import moment from 'moment';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	import  { getApiParamsFromArray } from 'helpers/extraLogics';

	export default {

		name : 'changes-list',

		description : 'Changes lists table component',


		data: () => ({

			columns: ['identifier','subject','requester','status','priority','department','created_at', 'actions'],

			options: {},

			apiUrl:'/service-desk/api/change-list',

			filterOptions: [

				{
					section : [

						{ 
						name: 'change_ids',
					 	url: '/service-desk/api/dependency/changes?meta=true',
					 	label: 'changes',
				  		},
				  		{ 
						name: 'department_ids',
					 	url: '/api/dependency/departments',
					 	label: 'departments',
				  		},
				  		{ 
						name: 'team_ids',
					 	url: '/api/dependency/teams',
					 	label: 'teams',
				  		},
					],
				},

				{
					section : [

						{ 
						name: 'requester_ids',
					 	url: '/api/dependency/users?meta=true',
					 	label: 'requester',
				  		},
				  		{ 
						name: 'status_ids',
					 	url: '/service-desk/api/dependency/change_statuses',
					 	label: 'status',
				  		},
				  		{ 
						name: 'priority_ids',
						url: '/service-desk/api/dependency/change_priorities',
						label: 'priority',
						},
					]
				},

				{

					section : [
						{ 
						name: 'change_type_ids',
						url: '/service-desk/api/dependency/change_types',
						label: 'change_type',
						},
						{ 
						name: 'impact_ids',
						url: '/service-desk/api/dependency/impacts',
						label: 'impact',
						},
						{ 
						name: 'location_ids',
					 	url: '/api/dependency/locations',
					 	label: 'location',
				  		},
					]
				},

				{
					section : [
						{ 
						name: 'asset_ids',
						 url: '/service-desk/api/asset-list?meta=true',
					 	label: 'assets',
				  		},
				  		{ 
						name: 'release_ids',
					 	url: '/service-desk/api/dependency/releases',
					 	label: 'releases',
				  		},
				  		{ 
						name: 'ticket_ids',
					 	url: '/api/dependency/tickets',
					 	label: 'tickets',
				  		},
					]
				},
				
				{
					section : [
						{ 
						name: 'problem_ids',
					 	url: '/service-desk/api/dependency/problems',
					 	label: 'problems',
				  		},
						{ 
						name: 'created_at',
						type: 'date',
						label: ['created_at', 'created_within_last'],
						timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
						pickers : true,
						range : true
						},
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
					
					identifier: 'change-identifier',

					subject: 'change-subject', 

					requester: 'change-requester', 

					status: 'change-status', 

					priority: 'change-priority', 

					department: 'change-department', 

					created_at: 'change-created',

					actions: 'change-action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				texts: { filter: '', limit: '' },

				templates: {

					requester: function(createElement, row) {
						
						if(row.requester){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/user/'+row.requester.id,
										
										target : '_blank'
									},
							}, row.requester.full_name ? row.requester.full_name : row.requester.email);
						} else { 
							return '---'
						}
					},

					status(h,row){
						
						return row.status ? row.status.name : '---';
					},

					priority(h,row){

						return row.priority ? row.priority.name : '---';
					},

					department: function(createElement, row) {
						
						if(row.department){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/department/'+row.department.id,
										
										target : '_blank'
									},
							}, row.department.name);
						} else { 
							return '---'
						}
					},

					created_at(h,row) {
						
						return self.formattedTime(row.created_at)
					},

					actions: 'table-actions'
				},

				sortable:  ['identifier','subject','created_at'],
				
				filterable:  ['identifier','subject','requester','status','priority','department'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
			
			  return {
				 
				 'sort-field' : data.orderBy ? data.orderBy : 'id',
				 
				 'sort-order' : data.ascending ? 'desc' : 'asc',
				 
				 'search-query' : data.query.trim(),
				 
				 page : data.page,
				 
				 limit : data.limit,
			  }
			},

				responseAdapter({data}) {

					return {
						data: data.data.changes.map(data => {
						
						data.view_url = self.basePath() + '/service-desk/changes/' + data.id + '/show';

						data.edit_url = self.basePath() + '/service-desk/changes/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/service-desk/api/change/' + data.id;

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

			this.apiUrl = '/service-desk/api/change-list';
			
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
      	let baseUrlForFilter = '/service-desk/api/change-list?';
        
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

			'changes-filter': require("components/Extra/DataTableFilter")
		}
	};
</script>

<style type="text/css">
	.changes-table {
		padding: 0px !important;
	}
	.add-changes{
		float: right;
	}
	.round-btn {
		margin-right: 5px;
	}
	.change-identifier,.change-subject,.change-requester,.change-priority,.change-status,.change-department,.change-created,.change-action{ 
		max-width: 250px; word-break: break-all;
	}
	#changes_list .VueTables .table-responsive {
		overflow-x: auto;
	}
	#changes_list .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>