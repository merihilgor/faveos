<template>
	
	<div id="pages-index">
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 class="box-title" id="pages_title">{{lang('list_of_pages')}}</h2>
	
					</div>
	
					<div class="col-md-8">
	
						<a class="btn btn-primary right" :href="basePath()+'/page/create'">
							<span class="glyphicon glyphicon-plus"> </span> 
							{{lang('create_pages')}}
						</a>
					
					</div>
				
				</div>
			
			</div>
			
			<div class="box-body">

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="pages-index"></data-table>
			</div>
		</div>
	
	</div>

</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import Vue from 'vue';

	import { mapGetters } from 'vuex'
  
  Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {
		
		name : 'pages-index',

		description : 'Pages table component',


		data: () => ({

			columns: ['name', 'status', 'created_at','action'],
			
			options: {},
			
			apiUrl:'api/get-pages-data',	
		}),

		computed:{
			
			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount(){
			
			const self = this;

			this.options = {
				
				texts : { 'filter' : '', 'limit': ''},

				headings: { name: 'Name', status: 'Status', created_at : 'Created at', action:'Action'},
				
				columnsClasses : {

					name: 'page-name',

					action : 'page-action',

					created_at: 'page-created',

					status: 'page-status'
				},

				templates: {
				
					status: function(createElement, row) {
					
						let span = createElement('span', {
							
							attrs:{
								'class' : row.status === 1 ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
							}
						}, row.status === 1 ? 'Published' : 'Draft');
						
						return createElement('a', {}, [span]);
					},
				
					action: 'table-actions',

					created_at(h, row) {
						
						return self.formattedTime(row.created_at)
					},
				
				},
				
				sortable:  ['name', 'status','created_at'],
				
				filterable:  ['name', 'status','created_at'],
				
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

						data.view_url = data.status ? self.basePath() + '/pages/' + data.slug : 'javascript:;';
						
						data.edit_url = self.basePath() + '/page/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/page/delete/' + data.id;

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
	.page-name{
		width: 35%;
		word-break: break-all;
	}
	.page-created{
		width: 30%;
		word-break: break-all;
	}
	.page-status{
		width: 15%;
		word-break: break-all;
	}
	.page-action{
		width: 20%;
		word-break: break-all;
	}
</style>