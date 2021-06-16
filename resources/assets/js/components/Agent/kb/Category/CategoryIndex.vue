<template>
	
	<div id="category-index">
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 class="box-title">{{lang('all_category')}}</h2>
	
					</div>
	
					<div class="col-md-8">
	
						<a class="btn btn-primary right" :href="basePath()+'/category/create'">
							<span class="glyphicon glyphicon-plus"> </span> 
							{{lang('create_category')}}
						</a>
					
					</div>
				
				</div>
			
			</div>
			
			<div class="box-body">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="category-index"></data-table>
			</div>
		</div>
	
	</div>

</template>

<script type="text/javascript">

	import {getSubStringValue} from 'helpers/extraLogics';

	import axios from 'axios';

	import Vue from 'vue';

  Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {
		
		name : 'category-index',

		description : 'Category table component',


		data: () => ({

			columns: ['name', 'description', 'status', 'display_order','action'],
				
			options: {},
	
			apiUrl:'/api/get-category-data',			
		}),

		beforeMount(){

			const self = this;

			this.options = {

				texts : { 'filter' : '', 'limit': ''},

				headings: { name: 'Name', description : 'Description', status: 'Status', display_order : 'Display Order', 
										action:'Action'},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},
				
				templates: {
					
					description: function(createElement, row) {
						
						return createElement('div', {
							
							attrs:{
								title : row.description.replace(/(<([^>]+)>)/g, "")
							},

							domProps: {
						    innerHTML: row.description.length > 100 ? row.description.substring(0, 100)+'...' : row.description
						  },
						});
					},

					status: 'data-table-status',
				
					action: 'table-actions'
				},

				columnsClasses : {

					name: 'category-name',

					description: 'category-desc',

					action : 'category-action',

					display_order: 'category-order',

					status: 'category-status'
				},
				
				sortable:  ['name', 'description', 'status','display_order'],
				
				filterable:  ['name', 'description', 'status','display_order'],
				
				pagination:{chunk:5,nav: 'scroll'},

				requestAdapter(data) {
				
					return {
				
						sort: data.orderBy ? data.orderBy : 'id',
				
						order: data.ascending ? 'desc' : 'asc',
				
						search:data.query.trim(),
				
						page:data.page,
				
						limit:data.limit,
					}
				},
				
				responseAdapter({data}) {
				
					return {
				
						data: data.message.data.map(data => {

						data.view_url = data.status ? self.basePath() + '/category-list/' + data.slug : 'javascript:;';
						
						data.edit_url = self.basePath() + '/category/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/category/delete/' + data.id;

						return data;
					
					}),
					
						count: data.message.total
					}
				},
			}
		},

		components:{
			
			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		
		} 
		
	};
</script>

<style type="text/css">
	.box-header h3{
		font-family: Source Sans Pro !important
	}
	.box.box-primary {
		padding: 0px !important;
	}
	.right{
		float: right;
	}
	.category-name{
		width: 20%;
		word-break: break-all;
	}
	.category-desc{
		width: 40%;
		word-break: break-all;
	}
	.category-order{
		width: 15%;
		word-break: break-all;
	}
	.category-status{
		width: 10%;
		word-break: break-all;
	}
	.category-action{
		width: 15%;
		word-break: break-all;
	}
</style>