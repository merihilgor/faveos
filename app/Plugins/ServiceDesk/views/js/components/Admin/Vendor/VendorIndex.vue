<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary asset-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="vendor-list" class="box-title">{{lang('list_of_vendors')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-vendor" :href="basePath()+'/service-desk/vendor/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_vendors')}}
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="vendor-table">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="vendor-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'vendor-list',

		description : 'Vendor lists table component',


		data: () => ({

			columns: ['name', 'primary_contact', 'email', 'address', 'status', 'action'],

			options: {},

			apiUrl:'/service-desk/api/vendor-list',
		}),

		beforeMount() {

			const self = this;

			this.options = {

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				columnsClasses : {

          name : 'vendor-name',

          email: 'vendor-email',

          primary_contact : 'vendor-contact',

          address: 'vendor-address'
        },

				templates: {

					status : 'data-table-status',

					action: 'table-actions'
				},

				sortable:  ['name', 'primary_contact', 'email', 'address', 'status'],
				
				filterable:  ['name', 'primary_contact', 'email', 'address', 'status'],
				
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
						data: data.data.vendors.map(data => {

						data.view_url = self.basePath() + '/service-desk/vendor/' + data.id + '/show';

						data.edit_url = self.basePath() + '/service-desk/vendor/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/service-desk/api/vendor/' + data.id;

						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),
			
			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style type="text/css">
	
	.add-vendor{
		float: right;
	}

	.vendor-name {
		width:15%;
		word-break: break-all;
	}
	
	.vendor-contact{
		width:15%;
		word-break: break-all;
	}

	.vendor-address{
		width:25%;
		word-break: break-all;
	}

	.vendor-email{ 
		width: 20%; word-break: break-all;
	}
</style>
