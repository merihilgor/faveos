<template>

	<div>

		<alert componentName="dataTableModal"/>

		<div class="box box-primary asset-table">

			<div class="box-header with-border">

				<div class="row">

					<div class="col-md-4">

						<h2 id="asset-list" class="box-title">{{lang('list_of_assets')}}</h2>

					</div>

					<div class="col-md-8">

						<a class="btn btn-primary add-asset" :href="basePath()+'/service-desk/assets/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_asset')}}
						</a>

						<a id="advance-filter-btn" class="btn btn-primary pull-right round-btn " @click = "toggleFilterView">
							<span class="glyphicon glyphicon-filter"></span>
						</a>

						<button class="btn btn-primary pull-right round-btn label-btn" @click="generateLabel">
							<i class="fa fa-print" aria-hidden="true"></i>
							{{lang('generate_label')}}
						</button>

					</div>
				</div>
			</div>

			<div class="box-body" id="asset_index">

				<asset-filter id="filter-box" v-if="isShowFilter" :metaData="filterOptions" :selectedFields="customFields"
							  @selectedFilters="selectedFilters" :showCustomFilter="true" :updateFields="updateFilterFields"
							  :appliedFilters="appliedFilters">

				</asset-filter>

				<data-table
						:url="apiEndPoint" :dataColumns="columns"
						:option="options" :tickets="assetsData"
						scroll_to ="asset-list"
				/>

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

			assetIds: [],

			columns: ['id', 'identifier', 'name', 'asset_type', 'asset_status', 'department', 'location', 'organization', 'managed_by', 'used_by', 'actions'],

			options: {},

			apiUrl:'/service-desk/api/asset-list',

			filterOptions: [

				{
					section : [
						{
							name: 'asset_ids',
							url: '/service-desk/api/dependency/assets',
							label: 'assets',
						},

						{
							name: 'problem_ids',
							url: '/service-desk/api/dependency/problems',
							label: 'problems',
						},

						{
							name: 'change_ids',
							url: '/service-desk/api/dependency/changes',
							label: 'changes',
						}
					]
				},

				{
					section : [

						{
							name: 'release_ids ',
							url: '/service-desk/api/dependency/releases',
							label: 'releases',
						},

						{
							name: 'contract_ids ',
							url: '/service-desk/api/dependency/contracts',
							label: 'contracts',
						},

						{
							name: 'asset_type_ids',
							url: '/service-desk/api/dependency/asset_types',
							label: 'asset_types',
						}
					]
				},

				{
					section : [

						{
							name: 'used_by_ids',
							url: 'api/dependency/users?meta=true',
							label: 'used_by',
						},

						{
							name: 'managed_by_ids',
							url: 'api/dependency/agents?meta=true',
							label: 'managed_by',
						},

						{
							name: 'location_ids',
							url: 'api/dependency/locations',
							label: 'location',
						}
					]
				},

				{
					section : [

						{
							name: 'dept_ids',
							url: 'api/dependency/departments',
							label: 'departments',
						},

						{
							name: 'org_ids',
							url: 'api/dependency/organizations',
							label: 'organizations',
						},

						{
							name: 'product_ids',
							url: '/service-desk/api/dependency/products',
							label: 'products',
						}
					]
				},

				{
					section : [

						{
							name: 'assigned_on',
							type: 'date',
							label: ['assigned_on', 'assigned_within_last'],
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

						{
							name: 'updated_at',
							type: 'date',
							label: ['updated_at', 'updated_within_last'],
							timeOptions:{  start: '00:00', step: '00:30',  end: '23:30' },
							pickers : true,
							range : true
						}
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
							name: 'ticket_ids',
							url: '/api/dependency/tickets',
							label: 'ticket',
						},
				        { 
			        		name: 'asset_status_ids',
			          		url: '/service-desk/api/dependency/asset_statuses',
			          		label: 'asset_status',
			        	}
					]
				},

				{
					section : []
				},
			],

			isShowFilter : false,

			appliedFilters : {},

			customFields : [],
		}),

		created() {

			window.eventHub.$on('updateFilter',this.updateFilter);
		},

		computed : {

			apiEndPoint(){

				return this.apiUrl
			}
		},

		beforeMount() {

			this.filterData();

			const self = this;

			this.options = {

				sortIcon: {

					base : 'glyphicon',

					up: 'glyphicon-chevron-up',

					down: 'glyphicon-chevron-down'
				},

				columnsClasses : {

					identifier: 'asset-id',

					name: 'asset-name',

					asset_type: 'asset-type',

					asset_status: 'asset-status',

					department: 'asset-dept',

					location: 'asset-location',

					organization: 'asset-org',

					managed_by: 'asset-manage',

					used_by: 'asset-used',

					actions: 'asset-actions',
				},

				texts: { filter: '', limit: '' },

				templates: {

					asset_type(h,row) {

						return row.asset_type ? row.asset_type.name : '---'
					},

					asset_status(h,row) {

						return row.asset_status ? row.asset_status.name : '---' 
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

					organization: function(createElement, row) {

						if(row.organization){

							return createElement('a', {

								attrs:{

									href : self.basePath() + '/organizations/'+row.organization.id,

									target : '_blank'
								},
							}, row.organization.name);
						} else {

							return '---'
						}
					},

					location(h,row) {

						return row.location ? row.location.name : '---'
					},

					managed_by: function(createElement, row) {

						if(row.managed_by){

							return createElement('a', {

								attrs:{

									href : self.basePath() + '/user/'+row.managed_by.id,

									target : '_blank'
								},
							}, row.managed_by.full_name ? row.managed_by.full_name : row.managed_by.email);
						} else {

							return '---'
						}
					},

					used_by: function(createElement, row) {

						if(row.used_by){

							return createElement('a', {

								attrs:{

									href : self.basePath() + '/user/'+row.used_by.id,

									target : '_blank'
								},
							}, row.used_by.full_name ? row.used_by.full_name : row.used_by.email);
						} else {

							return '---'
						}
					},

					actions : 'table-actions'
				},

				sortable:  ['name', 'identifier'],

				filterable:  ['identifier', 'name', 'asset_type', 'department', 'location', 'organization', 'managed_by', 'used_by'],

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

						data: data.data.assets.map(data => {

							data.view_url = self.basePath() + '/service-desk/assets/' + data.id + '/show';

							data.edit_url = self.basePath() + '/service-desk/assets/' + data.id + '/edit';

							data.delete_url = self.basePath() + '/service-desk/api/asset-delete/' + data.id;

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

			updateFilterFields(fields) {

				this.customFields = fields;

				this.filterOptions[6]['section'] = fields;
			},

			generateLabel() {

				let baseUrl = '/service-desk/generate-barcode?'

				let params = '';

				if(this.assetIds.length != 0) {

					for(let i in this.assetIds) {

						params += "ids["+i+"]="+this.assetIds[i]+"&";
					}

					let url = baseUrl + params;

					this.redirect(url)
				}
				else {

					alert(this.lang('barcode_no_asset'));
				}
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

				this.apiUrl = '/service-desk/api/asset-list';

				this.updateFilterFields([]);

				window.eventHub.$emit('clearCustomFields');

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

				for(var i in this.customFields){

					this.customFields[i].value = value[this.customFields[i].name]
				}

				let baseUrlForFilter = '/service-desk/api/asset-list?';

				let params = getApiParamsFromArray(this.filterOptions,value);

				if(params[params.length-1] === '&') {

					params = params.slice(0, -1);
				}

				this.apiUrl = baseUrlForFilter + params;

				this.isShowFilter = false;
			},

			assetsData(data){

				this.assetIds = data
			},

			toggleFilterView() {

				this.isShowFilter = !this.isShowFilter;
			},
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),

			'asset-filter': require("components/Extra/DataTableFilter"),

			'asset-list-with-checkbox' : require("./AssetListWithCheckbox")
		}
	};
</script>

<style type="text/css">
	.asset-id,.asset-name,.asset-type,.asset-dept,.asset-org,.asset-location,.asset-manage,.asset-used,.asset-actions,.asset-status
	{ max-width: 250px; word-break: break-all;}

	#asset_index .VueTables .table-responsive {
		overflow-x: auto;
	}

	#asset_index .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}

	.VueTables__table input[type=checkbox] {
		display: block !important;
	}

	.asset-table {
		padding: 0px !important;
	}

	.add-asset{
		float: right;
	}
	.round-btn {
		margin-right: 5px;
	}
</style>
