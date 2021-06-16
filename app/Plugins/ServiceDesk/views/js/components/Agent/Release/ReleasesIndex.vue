<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary releases-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="releases-list" class="box-title">{{lang('list_of_releases')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-release" :href="basePath()+'/service-desk/releases/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_release')}}
						</a>

						<a id="advance-filter-btn" class="btn btn-primary pull-right round-btn" @click = "toggleFilterView">
							<span class="glyphicon glyphicon-filter"></span>
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="releases_list">
				
				<releases-filter id="filter-box" v-if="isShowFilter" :metaData="filterOptions" :appliedFilters="appliedFilters"
					@selectedFilters="selectedFilters">
        
        		</releases-filter>

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="releases-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios';

	import { mapGetters } from 'vuex'

	import moment from 'moment';

	import  { getApiParamsFromArray } from 'helpers/extraLogics';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'releases-list',

		description : 'Releases lists table component',


		data: () => ({

			columns: ['identifier','subject','release_type','priority', 'status','planned_start_date','planned_end_date', 'action'],

			options: {},

			apiUrl:'/service-desk/api/release-list',

			filterOptions: [
				{
					section : [
						
						{ 
							name: 'release_ids',
							url: '/service-desk/api/dependency/releases',
							label: 'releases',
						},

						{ 
							name: 'status_ids',
							url: '/service-desk/api/dependency/release_statuses',
							label: 'status',
						},

						{ 
							name: 'location_ids',
							url: '/api/dependency/locations',
							label: 'locations',
						},
					]
				},

				{
					section : [
						
						{ 
							name: 'priority_ids',
							url: '/service-desk/api/dependency/release_priorities',
							label: 'priority',
						},

						{ 
							name: 'release_type_ids',
							url: '/service-desk/api/dependency/release_types',
							label: 'release_type',
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
							name: 'change_ids',
							url: '/service-desk/api/dependency/changes',
							label: 'changes',
						},

						{ 
		        	name: 'planned_start_date',
		          type: 'date',
		          label: ['planned_start_date', 'planned_start_date_within_last'],
		          timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
		          pickers : true,
		          range : true
		        },

		        { 
		        	name: 'planned_end_date',
		          type: 'date',
		          label: ['planned_end_date', 'planned_end_date_within_last'],
		          timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
		          pickers : true,
		          range : true
		        }
					]
				},

				{
					section : [
						
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

				headings: { planned_start_date: 'Plan Start Date', planned_end_date: 'Plan End Date', action:'Actions'},

				columnsClasses : {

					identifier: 'release-identifier',

					subject: 'release-subject', 

					release_type: 'release-type', 
					
					priority: 'release-priority', 
					
					status: 'release-status', 
					
					planned_start_date: 'release-start', 
					
					planned_end_date: 'release-end',			
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				texts: { filter: '', limit: '' },

				templates: {

					planned_start_date(h,row) {

						return row.planned_start_date && row.planned_start_date != '0000-00-00 00:00:00' ? self.formattedTime(row.planned_start_date) : '---'
					},

					planned_end_date(h,row) {
						
						return row.planned_end_date && row.planned_end_date != '0000-00-00 00:00:00' ? self.formattedTime(row.planned_end_date) : '---'
					},

					status(h,row){

						return row.status ? row.status.name : '---' 
					},

					priority(h,row) {

						return row.priority ? row.priority.name : '---' 
					},

					release_type(h,row) {

						return row.release_type ? row.release_type.name : '---' 
					},

					action: 'table-actions'
				},

				sortable : ['subject', 'identifier', 'planned_start_date', 'planned_end_date'],
				
				filterable : ['identifier','subject','release_type','priority', 'status','planned_start_date','planned_end_date'],
				
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
						
						data: data.data.releases.map(data => {
						
						data.view_url = self.basePath() + '/service-desk/releases/' + data.id + '/show';

						if(data.is_edit){
							
							data.edit_url = self.basePath() + '/service-desk/releases/' + data.id + '/edit';
						}

						if(data.is_delete){

							data.delete_url = self.basePath() + '/service-desk/api/release/' + data.id;
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

      	this.apiUrl = '/service-desk/api/release-list';
        	
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
      	let baseUrlForFilter = '/service-desk/api/release-list?';
        
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

			'releases-filter': require("components/Extra/DataTableFilter")
		}
	};
</script>

<style type="text/css">

	.release-identifier,.release-subject,.release-type,.release-priority,.release-status,.release-start,.release-end{ max-width: 250px; word-break: break-all;}

	#contracts_list .VueTables .table-responsive {
		overflow-x: auto;
	}

	#releases_list .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
	.releases-table {
		padding: 0px !important;
	}
	.add-release{
		float: right;
	}
	.round-btn {
		margin-right: 5px;
	}
</style>