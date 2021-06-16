<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary product-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="product-list" class="box-title">{{lang('list_of_product')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-product" :href="basePath()+'/service-desk/products/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_new_product')}}
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="product-table">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="product-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'product-list',

		description : 'Product lists table component',


		data: () => ({

			columns: ['name', 'manufacturer', 'product_status', 'status', 'action'],

			options: {},

			apiUrl:'/service-desk/api/product-list',
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

          name : 'product-name',

          manufacturer: 'product-man',

          product_status : 'product_status',

          status: 'product-status'
        },

				templates: {

         status: function(createElement, row) {
            
            let span = createElement('span', {
              attrs:{
                'class' : row.status === 1 ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
              }
            }, row.status === 1 ? 'Enable' : 'Disable');
            
            return createElement('a', {}, [span]);
          },

          product_status: function(h, row) {
             
            return row.product_status.name;
          },

					action: 'table-actions'
				},

				sortable:  ['name', 'manufacturer', 'status'],
				
				filterable:  ['name', 'manufacturer', 'status'],
				
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
						data: data.data.products.map(data => {

						data.edit_url = self.basePath() + '/service-desk/products/' + data.id + '/edit';

						data.view_url = self.basePath() + '/service-desk/products/' + data.id + '/show';

						data.delete_url = self.basePath() + '/service-desk/api/product-delete/' + data.id;

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
	
	.add-product{
		float: right;
	}

	.product-name {
		width:25%;
		word-break: break-all;
	}
	
	.product-man{
		width:25%;
		word-break: break-all;
	}

	.product-status{
		width:15%;
		word-break: break-all;
	}

	.product_status{ 
		width: 20%; word-break: break-all;
	}
</style>
