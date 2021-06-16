<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary pblm-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="problem-list" class="box-title">{{lang('list_of_problems')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-pblm" :href="basePath()+'/service-desk/problem/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('problem-add')}}
						</a>

						<a id="advance-filter-btn" class="btn btn-primary pull-right round-btn" @click = "toggleFilterView">
							<span class="glyphicon glyphicon-filter"></span>
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="problems_list">
				
				<problem-filter id="filter-box" v-if="isShowFilter" :metaData="filterOptions" :appliedFilters="appliedFilters"
					@selectedFilters="selectedFilters">
				
				</problem-filter>

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="problem-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	import  { getApiParamsFromArray } from 'helpers/extraLogics';

	export default {

		name : 'problem-list',

		description : 'Problem lists table component',


		data: () => ({

			columns: ['identifier', 'subject', 'requester','assignedTo','priority', 'department', 'status_type_id', 'action'],

			options: {},

			apiUrl:'/service-desk/api/problem-list',

			filterOptions: [
				{
					section : [
						
						{ 
							name: 'problem_ids',
							url: '/service-desk/api/dependency/problems',
							label: 'problems',
						},

						{ 
							name: 'dept_ids',
							url: 'api/dependency/departments',
							label: 'departments',
						},

						{ 
							name: 'impact_ids',
							url: '/service-desk/api/dependency/impacts',
							label: 'impact_type',
						},
					]
				},
				
				{
					section : [
						
						{ 
							name: 'status_ids',
							url: 'api/dependency/statuses',
							label: 'status',
						},
						
						{ 
							name: 'location_ids',
							url: 'api/dependency/locations',
							label: 'location',
						},

						{ 
							name: 'priority_ids',
							url: 'api/dependency/priorities',
							label: 'priority',
						},
					]
				},

				{
					section : [
						
						{ 
							name: 'assigned_ids',
							url: 'api/dependency/agents?meta=true',
							label: 'assigned',
						},
						
						{ 
							name: 'asset_ids',
							url: '/service-desk/api/asset-list?meta=true',
							label: 'assets',
						},

						{ 
							name: 'ticket_ids',
							url: 'api/dependency/tickets',
							label: 'tickets',
						},
					]
				},{
					section : [
						
						{ 
							name: 'requester_ids',
							url: '/api/dependency/users?meta=true',
							label: 'requester',
						},

						{ 
							name: 'change_ids',
							url: '/service-desk/api/dependency/changes',
							label: 'changes',
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
				},{
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
				},   
			],

			isShowFilter : false,

			appliedFilters : {}
		}),

		created() {
			
			window.eventHub.$on('updateFilter',this.updateFilter);
		},

		beforeMount() {

			this.filterData();

			const self = this;

			this.options = {

				headings: { status_type_id: 'Status', assignedTo : 'Assigned To' ,action:'Actions'},
				
				columnsClasses : {

					subject: 'problem-subject', 

					requester: 'problem-from', 

					assignedTo: 'problem-assign', 

					priority :'problem-priority', 

					department:'problem-department',

					status_type_id: 'problem-status',

					action: 'problem-action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
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

					assignedTo: function(createElement, row) {
						
						if(row.assignedTo){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/user/'+row.assignedTo.id,
										
										target : '_blank'
									},
							}, row.assignedTo.full_name ? row.assignedTo.full_name : row.assignedTo.email);
						} else { 

							return '---'
						}
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

					status_type_id(h, row) {
						
						return row.status ? row.status.name : '---';
					},

					priority(h, row) {

						return row.priority ? row.priority.priority : '---';
					},

					action: 'table-actions'
				},

				sortable:  ['subject', 'identifier'],
				
				filterable:  ['subject', 'identifier'],
				
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
						data: data.data.problems.map(data => {

						data.view_url = self.basePath() + '/service-desk/problem/' + data.id + '/show';

						if(data.is_edit){
							
							data.edit_url = self.basePath() + '/service-desk/problem/' + data.id + '/edit';
						}

						if(data.is_delete){

							data.delete_url = self.basePath() + '/service-desk/api/problem-delete/' + data.id;
						}

						data.active = (data.active == '1') ? 'active' : 'inactive';

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

				this.apiUrl = '/service-desk/api/problem-list';
					
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
				let baseUrlForFilter = '/service-desk/api/problem-list?';
				
				let params = getApiParamsFromArray(this.filterOptions,value);;
				
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

			'problem-filter': require("components/Extra/DataTableFilter")
		}
	};
</script>

<style type="text/css">
	
	.problem-subject,.problem-from,.problem-assign,.problem-department,.problem-status,.problem-action,.problem-priority
	{ max-width: 250px; word-break: break-all;}
	#problems_list .VueTables .table-responsive {
		overflow-x: auto;
	}
	#problems_list .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
	.pblm-table {
		padding: 0px !important;
	}
	.add-pblm{
		float: right;
	}
	.round-btn {
		margin-right: 5px;
	}
</style>
