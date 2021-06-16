<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="box box-primary assets-type-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 id="asset-type-list" class="box-title">{{lang('asset_type_lists-page-title')}}</h2>
	
					</div>
	
					<div class="col-md-8">

						<a class="btn btn-primary add-asset-type" :href="basePath()+'/service-desk/assetstypes/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_asset_type')}}
						</a>
					</div>
				</div>
			</div>

			<div class="box-body" id="assets-type-table">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="asset-type-lists">
					
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

		name : 'asset-type-lists',

		description : 'Asset type lists table component',


		data: () => ({

			columns: ['name', 'parent','created_at', 'action'],

			options: {},

			apiUrl:'service-desk/api/asset-type-list',
		}),

		beforeMount() {

			const self = this;

			this.options = {

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				texts: { filter: '', limit: '' },

				columnsClasses : {

		  name : 'assets-type-name',
		  
		  is_default :'Default',

		  parent : 'parent',
		  
          created_at: 'assets-type-created',

        },

				templates: {

					created_at(h,row){

						return self.formattedTime(row.created_at)
					},

					parent(h, row) {
						return row.parent ? row.parent.name : '--';
					},

					
					action: 'table-actions'
				},

				sortable:  ['name','created_at'],
				
				filterable:  ['name'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
	      
	        return {
	          
	          'sort-field' : data.orderBy ? data.orderBy : 'id',
	          
	          'sort-order' : data.ascending ? 'desc' : 'asc',
	          
	          'search-query' : data.query,
	          
	          page : data.page,
	          
	          limit : data.limit,
	        }
	      },

			 	responseAdapter({data}) {
					return {
						data: data.data.asset_types.map(data => {
						
						data.edit_url = self.basePath() + '/service-desk/assetstypes/' + data.id + '/edit';

					    data.delete_url = self.basePath() + '/service-desk/api/asset-type/' + data.id;


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

<style type="text/css" scoped>
	
	.add-asset-type{
		float: right;
	}

	.asset-type-name {
		width:25%;
		word-break: break-all;
	}
	
	.asset-type-created{
		width:25%;
		word-break: break-all;
	}

</style>
