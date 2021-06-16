<template>

	<div id="asset_status_list">

		<alert componentName="dataTableModal"/>

		<div class="box box-primary">

			<div class="box-header with-border">

				<div class="row">

					<div class="col-md-4">

						<h4 class="box-title">{{trans('list_of_asset_statuses')}}</h4>

					</div>

					<div class="col-md-8">

						<a class="btn btn-primary pull-right" :href="basePath()+'/service-desk/asset-status/create'">

							<span class="glyphicon glyphicon-plus"> </span> &nbsp;{{trans('create_asset_status')}}
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="asset_status_table">

				<data-table :url="apiUrl" :dataColumns="columns" :option="options" scroll_to="asset_status_list"></data-table>
			</div>
		</div>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex'

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'asset-status-index',

		description : 'Asset Status Index Component',

		data() {

			return {

				apiUrl:'/service-desk/api/asset-status-list',

				columns: ['name', 'created_at', 'updated_at', 'action'],

				options : {},
			}
		},

		computed : { ...mapGetters(['formattedTime']) },

		beforeMount(){

			const self = this;

			this.options = {

				sortIcon: {

					base : 'glyphicon',

					up: 'glyphicon-chevron-down',

					down: 'glyphicon-chevron-up'
				},

				columnsClasses : {

					name: 'asset-status-name',

					created_at : 'asset-status-created',

					updated_at : 'asset-status-updated',
					
					action : 'asset-status-action',
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

				sortable : ['name', 'created_at', 'updated_at'],

				filterable : ['name'],

				pagination : { chunk : 5, nav : 'fixed', edge : true},

				requestAdapter(data) {

					return {

						'sort-field' : data.orderBy ? data.orderBy : 'id',

						'sort-order': data.ascending ? 'desc' : 'asc',

						'search-query':data.query,

						page:data.page,

						limit:data.limit,
					}
				},

				responseAdapter({data}) {
				
					return {

						data: data.data.data.map(data => {

							data.edit_url = self.basePath() + '/service-desk/asset-status/'+ data.id +'/edit';

							data.delete_url = self.basePath() + '/service-desk/api/asset-status/' + data.id ;

							return data;
						}),

						count: data.data.total
					}
				},
			}
		},

		components : {

			"data-table" : require("components/Extra/DataTable"),

			"alert": require("components/MiniComponent/Alert"),
		},
	};
</script>

<style>

	.asset-status-name,.asset-status-created,.asset-status-updated,.asset-status-action { 
		max-width: 250px; word-break: break-all;
	}

	#asset_status_table .VueTables .table-responsive {
		overflow-x: auto;
	}

	#asset_status_table .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>