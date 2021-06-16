<template>
	<div>
		<alert componentName="dataTableModal"/>

		<div class="box box-primary">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-4">
						<h2 class="box-title">{{lang('list_of_business_hours')}}</h2>
					</div>
					<div class="col-md-8">
						<a class="btn btn-primary right" :href="base+'/sla/business-hours/create'"><span class="glyphicon glyphicon-plus"> </span> {{lang('create_business_hour')}}</a>
					</div>
			</div>
		</div>

		<div class="box-body">
			
			<data-table :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
		</div>
	</div>
</div>
</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';
	import axios from 'axios';

	export default {

		name : 'business-hours-index',

		description : 'Business hours table component',


		data: () => ({

			/**
			* base url of the application
			* @type {String}
			*/
			base:window.axios.defaults.baseURL,

			/**
			* columns required for datatable
			* @type {Array}
			*/
			columns: ['name', 'status', 'action'],

			options: {
				headings: { name: 'Name', status: 'Status', action:'Action'},
				texts: {
		          filter: '',
		          limit: ''
		        },
				templates: {
					status: 'data-table-status',
					action: 'data-table-actions'
				},
				sortable:  ['name', 'status'],
				filterable:  ['name', 'status'],
				pagination:{chunk:5,nav: 'fixed',edge:true},
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

						data.edit_url = window.axios.defaults.baseURL + '/sla/business-hours/edit/' + data.id;

						data.delete_url = window.axios.defaults.baseURL + '/sla/business-hours/delete/' + data.id;

						data.active = (data.active == '1') ? 'active' : 'inactive';

						return data;
					}),
						count: data.message.total
					}
				},

			},

			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'/sla/business-hours/getindex/',


		}),

		components:{
			'data-table' : require('components/Extra/DataTable'),
			"alert": require("components/MiniComponent/Alert"),
		}


	};
</script>

<style type="text/css" scoped>
	.box-header h3{
		font-family: Source Sans Pro !important
	}
	.box.box-primary {
		padding: 0px !important;
	}
	.right{
		float: right;
	}
</style>
