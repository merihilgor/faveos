<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary report-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="report-list" class="box-title">{{lang('assets_reports')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-report" :href="basePath()+'/service-desk/reports/assets/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_asset_report')}}
						</a>
					</div>
				</div>
			</div>

			<div class="box-body">

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="report-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	import axios from 'axios';

	import { mapGetters } from 'vuex'

	export default {

		name : 'report-list',

		description : 'Report lists table component',


		data: () => ({

			columns: ['name', 'description', 'created_at', 'updated_at', 'action'],

			options: {},

			apiUrl:'/service-desk/api/reports',
		}),

		computed:{
			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount() {

			const self = this;

			this.options = {

				headings: { 

					name: 'Name', 

					description : 'Description', 

					created_at : 'Created at', 

					updated_at : 'Updated at', 

					action:'Action'
				},
				
				columnsClasses : {

					name: 'report-name', 

					description: 'report-desc', 

					created_at: 'report-time',

					updated_at: 'report-time',

					action: 'report-action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				templates: {

					created_at(h, row) {

						return self.formattedTime(row.created_at);
					},

					updated_at(h, row) {

						return self.formattedTime(row.updated_at);
					},

					action: 'table-actions'
				},

				sortable:  ['name', 'description', 'created_at', 'updated_at'],
				
				filterable:  ['name', 'description'],
				
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
						data: data.data.reports.data.map(data => {

						data.view_url = self.basePath() + '/service-desk/reports/assets/' + data.id;

						data.edit_url = self.basePath() + '/service-desk/reports/assets/edit/' + data.id;

						data.delete_url = self.basePath() + '/service-desk/api/reports/' + data.id;

						return data;
					}),
						count: data.data.reports.total
					}
				},
			}
		},

		methods:{
			
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),
			
			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style type="text/css">
	
	.report-name,{
		width:20% !important;
		word-break: break-all;
	}
	.report-desc{
		width:30% !important;
		word-break: break-all;
	}
	.report-time{
		width:15% !important;
		word-break: break-all;
	}
	.report-action{
		width:20% !important;
		word-break: break-all;
	}
	.report-table {
		padding: 0px !important;
	}
	.add-report{
		float: right;
	}
	.round-btn {
		margin-right: 5px;
	}
</style>
