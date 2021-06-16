<template>

	<div>

		<alert componentName="dataTableModal" />
		<div class="box box-primary">

			<div class="box-header with-border">

				<div class="row">

					<div class="col-md-4">

						<h2 class="box-title">{{lang('listener_list')}}</h2>
					</div>

					<div class="col-md-8">

						<a id="create" class="btn btn-primary right" :href="base+'/listener/create'"><span class="glyphicon glyphicon-plus"> </span> {{lang('create_listener')}}</a>

						<a v-if="showTable && total_records > 1" class="btn btn-primary right" href="javascript:void:;" @click="reorderMethod"><span class="fa fa-reorder"> </span> &nbsp;{{lang('reorder')}}</a>
					</div>
				</div>
			</div>

			<div class="box-body">
				<data-table v-if="apiUrl !== '' && showTable" :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>

				<listener-reorder v-if="!showTable" :onClose="onClose" :url="apiUrl+'?type=listener&meta=true&sort=order&sort_order=asc'" reorder_type="listener">

				</listener-reorder>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import moment from 'moment';

	import momentTimezone from 'moment-timezone';
	import {mapGetters} from "vuex";

	export default {

		name : 'listener-index',

		description : 'Listener table component',

		data: () => ({
			/**
			* base url of the application
			* @type {String}
			*/
			base:window.axios.defaults.baseURL,

			columns:[],

			options:{},

			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'api/get-enforcer-list/',

			total_records : 0,

			showTable: true,

		}),

		beforeMount(){

			const self = this;

			/**
			* columns required for datatable
			* @type {Array}
			*/
			this.columns= ['name', 'status', 'order', 'created_at', 'updated_at', 'action']

			this.options= {

				texts: {  filter: '', limit: ''},

				headings: { name: 'Name', status : 'Status',
							order : 'Order', created_at: 'Created',
							updated_at: 'Updated',action:'Action'
				},

				templates: {

					action: 'data-table-actions',

					status: function(createElement, row) {

			            let span = createElement('span', {
			              attrs:{
			                'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
			              }
			            }, row.status ? 'Active' : 'Inactive');

			            return createElement('a', {

			            }, [span]);
			        },

					name: 'datatable-name',
					created_at(h, row) {
						return self.formattedTime(row.created_at)
			        },
			        updated_at(h, row) {
						return self.formattedTime(row.updated_at)
					},
				},

				sortable:  ['name', 'status', 'order', 'rules', 'target','created_at', 'updated_at'],

				filterable: ['name', 'created_at', 'updated_at'],

				pagination:{chunk:5,nav: 'scroll'},

				requestAdapter(data) {
			        return {
			        	type : 'listener',
			          sort: data.orderBy ? data.orderBy : 'order',
			          sort_order: data.ascending ? 'asc' : 'desc',
			          search:data.query.trim(),
			          page:data.page,
			          limit:data.limit,
			        }
			    },

			 	responseAdapter({data}) {

			 		self.total_records = data.data.total;

					return {
						data: data.data.data.map(data => {
						data.edit_url = window.axios.defaults.baseURL + '/listener/'+data.id+'/edit' ;
						data.delete_url = window.axios.defaults.baseURL + '/api/delete-enforcer/listener/'+ data.id;
						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		methods :{

			reorderMethod() {
	      this.showTable = false;
	      this.title = "reorder";
	    },

	    onClose() {
	      this.showTable = true;
	      this.title = "list_of_ticket_workflows";
	    }
		},

		computed:{
			...mapGetters(['formattedTime'])
		},

		components:{
			'data-table' : require('components/Extra/DataTable'),
			"listener-reorder": require("components/Admin/Workflow/Reorder.vue"),
    	'alert' : require('components/MiniComponent/Alert'),
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
	#create {
	  margin-right: 0px !important;
	}
</style>
