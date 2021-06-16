<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary procurement-type-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="procurement-type-list" class="box-title">{{lang('list_of_procurements')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-procurement-type" :href="basePath()+'/service-desk/procurement/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('add-procurement')}}
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="procurement-type-table">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="procurement-type-list">
					
				</data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	import { mapGetters } from 'vuex';

	export default {

		name : 'procurement-type-list',

		description : 'Procurement type lists table component',


		data: () => ({

			columns: ['name', 'created_at', 'updated_at', 'action'],

			options: {},

			apiUrl:'/service-desk/api/procurement-mode-list',
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

          name : 'procurement-type-name',

          created_at: 'procurement-type-created',

          updated_at : 'procurement-type-updated'
        },

				templates: {

					created_at(h,row){

						return self.formattedTime(row.created_at)
					},

					updated_at(h,row){

						return self.formattedTime(row.updated_at)
					},

					action: 'table-actions'
				},

				sortable:  ['name', 'created_at', 'updated_at'],
				
				filterable:  ['name', 'created_at', 'updated_at'],
				
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
						data: data.data.procurement_modes.map(data => {

						data.edit_url = self.basePath() + '/service-desk/procurement/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/service-desk/api/procurement-mode/' + data.id;

						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),
			
			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style type="text/css">
	
	.add-procurement-type{
		float: right;
	}

	.procurement-type-name {
		width:25%;
		word-break: break-all;
	}
	
	.procurement-type-created{
		width:25%;
		word-break: break-all;
	}

	.procurement-type-updated{
		width:25%;
		word-break: break-all;
	}
</style>