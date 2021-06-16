<template>
	
	<div id="template-index">
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 class="box-title">{{lang('list_of_article_template')}}</h2>
	
					</div>
	
					<div class="col-md-8">
	
						<a class="btn btn-primary right" :href="basePath()+'/article/create/template'">
							<span class="glyphicon glyphicon-plus"> </span> 
							{{lang('create_template')}}
						</a>
					
					</div>
				
				</div>
			
			</div>
			
			<div class="box-body">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="template-index"></data-table>
			</div>
		</div>
	
	</div>

</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import Vue from 'vue';

  Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {
		
		name : 'article-template-index',

		description : 'Article template table component',


		data: () => ({

			columns: ['name', 'status', 'action'],
				
			options: {},

			apiUrl:'/articletemplate/index',				
		}),

		beforeMount(){

			const self = this;

			this.options = {

				texts : { 'filter' : '', 'limit': ''},

				headings: { name: 'Name', status: 'Status', action:'Action'},
				
				templates: {
				
					status: 'data-table-status',
				
					action: 'table-actions'
				},

				columnsClasses : {

					name: 'template-name',

					status: 'template-status'
				},
				
				sortable:  ['name', 'status'],
				
				filterable:  ['name', 'status'],
				
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
						
						data.edit_url = self.basePath() + '/articletemplate/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/article/deletetemplate/' + data.id;
				
						data.active = (data.active == '1') ? 'active' : 'inactive';

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
	.template-status{
		width: 30%;
		word-break: break-all;
	}
	.template-name{
		width: 40%;
		word-break: break-all;
	}
</style>