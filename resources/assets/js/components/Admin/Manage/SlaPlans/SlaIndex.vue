<template>
	
	<div>
	
		<alert componentName="dataTableModal" />

		<div class="box box-primary">
			
			<div class="box-header with-border">
				
				<div class="row">
					
					<div class="col-md-4">
						
						<h2 class="box-title">{{lang(title)}}</h2>
					</div>
					
					<div class="col-md-8">
						
						<a class="btn btn-primary right" :href="basePath()+'/sla/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_SLA')}}</a>
						
						<a id="reorder" v-if="showTable && total_records > 1" class="btn btn-primary right" href="javascript:void:;" 
							@click="reorderMethod">

							<span class="fa fa-reorder"> </span> &nbsp;{{lang('reorder')}}
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="sla-table">
				
				<data-table v-if="apiUrl !== '' && showTable" :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="sla-table">
					
				</data-table>
				
				<sla-reorder v-if="!showTable" :onClose="onClose" :url="apiUrl+'?type=sla&meta=true&sort=order&sort_order=asc'" 
					reorder_type="sla">
			
				</sla-reorder>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from "axios";

	import { mapGetters } from 'vuex'

	import moment from 'moment';

	import Vue from 'vue'

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {
		
		name: "sla-index",

		description: "SLA table component",

		data(){

			return {

				columns: [ "name", "status", "order", "created_at", "updated_at", "action"],

				options: {},

				apiUrl: "api/get-enforcer-list/",

				title: "list_of_SLA_plans",

				showTable: true,

				total_records : 0,
			}
		},

		computed :{
		
			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount() {

			const self = this;

			this.options = {
			
				texts: { filter: "", limit: "" },
	
				headings: {
					
					created_at: "Created",
					
					updated_at: "Updated",
				},

				columnsClasses : {

					name: 'sla-name',

					status : 'sla-status',

					order : 'sla-order',

					updated_at : 'sla-updated',

					created_at : 'sla-created'
				},

				templates: {
				
					action: "table-actions",

					status: function(createElement, row) {
						
						let span = createElement('span', {
						
							attrs:{
							
								'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
							}
						}, row.status ? 'Active' : 'Inactive');
									
						return createElement('a', {}, [span]);
					},

					created_at(h, row) {

						return self.formattedTime(row.created_at)
					},

					updated_at(h, row) {
						
						return self.formattedTime(row.updated_at)
					},
				},
				
				sortable: [ "name", "status", "order", "created_at", "updated_at"],

				filterable: ["name", "created_at", "updated_at"],
				
				pagination: { chunk: 5, nav: "scroll" },
				
				requestAdapter(data) {
					
					return {
						
						type: "sla",
						
						sort: data.orderBy ? data.orderBy : "order",
						
						sort_order: data.ascending ? "asc" : "desc",
						
						search: data.query.trim(),
						
						page: data.page,
						
						limit: data.limit
					};
				},

				responseAdapter({ data }) {  

					self.total_records = data.data.total;      
				
					return {
						
						data: data.data.data.map(data => {
							
							data.edit_url = self.basePath() + "/sla/" + data.id + '/edit';
							
							data.delete_url = self.basePath() + "/api/delete-enforcer/sla/" + data.id;
							
							return data;
						}),
						
						count: data.data.total
					};
				}
			};
		},

		methods: {
			
			reorderMethod() {
				
				this.showTable = false;
				
				this.title = "reorder";
			},
			
			onClose() {
				
				this.showTable = true;
				
				this.title = "list_of_SLA_plans";
			}
		},

		components: {
			
			"data-table": require("components/Extra/DataTable"),
			
			"sla-reorder": require("components/Admin/Workflow/Reorder.vue"),
			
			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style type="text/css" scoped>

	.box-header h3 {		
		font-family: Source Sans Pro !important;
	}
	.box.box-primary {
		padding: 0px !important;
	}
	.right {
		float: right;
	}
	#reorder {
		margin-right: 5px !important;
	}
</style>

<style>
	
	.sla-name{
		max-width: 250px;
		word-break: break-all;
	}
	.sla-order{
		max-width: 65px;
		word-break: break-all;
	}

	.sla-status{
    max-width: 60px;
		word-break: break-all;
	}
	.sla-created{
		word-break: break-all;
	}
	.sla-updated{
		word-break: break-all;
	}

	#sla-table .VueTables .table-responsive {
		overflow-x: auto;
	}

	#sla-table .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>
