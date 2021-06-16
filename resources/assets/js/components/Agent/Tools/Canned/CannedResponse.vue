<template>
		
	<div id="canned_response">
		
		<alert componentName="dataTableModal"/>

		<div class="box box-primary">
		
			<div class="box-header with-border">
			
				<h3 class="box-title">{{lang('canned_response')}}</h3>

				<a :href="basePath()+'/canned/create'" class="btn btn-primary pull-right">
          
          <span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;{{lang('create_canned_response')}}
        </a>
			</div>
			
			<div class="box-body">
			
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="canned_response"></data-table>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { mapGetters } from 'vuex';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'canned-list',

		description : 'Canned response list',

		props : { 

		},

		data() {

			return {

				columns: ['title', 'created_at', 'updated_at', 'action'],

				options: {},

				apiUrl:'api/canned/list',
			}
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount(){


			const self= this;

			this.options = {

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				headings: { 
					
					title: 'Name', 

					created_at : 'Created at', 

					updated_at : 'Updated at',
					
					action:'Action'
				},

				columnsClasses : {
          
         	title: 'canned-name', 

					created_at : 'canned-created', 

					updated_at : 'canned-updated', 
        },
				
				texts: { filter: '', limit: '' },

				templates: {
						
          updated_at : function(h,row){

          	return self.formattedTime(row.updated_at)
          },

          created_at : function(h,row){

          	return self.formattedTime(row.created_at)
          },
					
					action: 'table-actions'
				},

				sortable:  ['title', 'created_at', 'updated_at'],

				filterable:  ['title', 'created_at', 'updated_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
	      
	        return {
	      
	          'sort-field': data.orderBy ? data.orderBy : 'updated_at',
	      
	          'sort-order': data.ascending ? 'desc' : 'asc',
	      
	          'search-query':data.query.trim(),
	      
	          page:data.page,
	      
	          limit:data.limit,
	        }
	      },

			 	responseAdapter({data}) {

					return {
					
						data: data.data.data.map(data => {

							data.edit_url = self.basePath() + '/canned/' + data.id + '/edit';
							
							data.delete_url = self.basePath() + '/api/canned/delete/' + data.id ;

						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		watch : {

		},

		methods : {

		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style scoped>
	 
	.canned-name,.canned-created,.canned-updated{
		width:30% !important;
		word-break: break-word;
	}
	.canned-action{
		white-space: nowrap;
	}
</style>