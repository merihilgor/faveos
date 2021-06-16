<template>
	
	<div id="asset-checkbox">

		<data-table :url="apiUrl" :dataColumns="columns"  :option="options" :tickets="tickets" 
			:componentTitle="componentTitle" scroll_to ="asset-checkbox">
			
		</data-table>
	
	</div>

</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import Vue from 'vue'

	import { mapGetters } from 'vuex'

	export default {
		
		name : 'asset-list-with-checkbox',

		description : 'Assets table component',

		props : { 

			apiUrl : { type : String, default : ''},

			tickets : { type : Function,  default: ()=>{}},

			componentTitle : { type : String, default : ''},
		},

		data: () => ({

			columns: ['id', 'name', 'used_by','managed_by','contract'],
				
			options: {},
			
		}),

		beforeMount(){

			const self = this;

			this.options = {

				texts : { 'filter' : '', 'limit': ''},

				headings: { 

					id : 'Id', 

					name: 'Name', 

					used_by: 'Used by', 

					managed_by: 'Managed by',
				 	
				 	contract : 'Contract' 
				},

				columnsClasses : {

					id : 'asset-check-id',

					name : 'asset-check-name',

					used_by : 'asset-check-used',
					
					managed_by : 'asset-check-manage',
					
					contract : 'asset-check-contract',
				},

				templates: {

					used_by(h,row){  

						return row.used_by ? row.used_by.full_name : '--' 
					},

					managed_by(h,row){  

						return row.managed_by ? row.managed_by.full_name : '--'
					},

					contract: function(createElement, row) {
            
            if(row.contract){

            	return createElement('a', {
		            attrs:{

		              href : self.basePath()+'/service-desk/contracts/'+row.contract.id+'/show',
		              target : '_blank'
		            },
		          }, row.contract.name);
            } else {
            	return '--'
            }
	        },
				},

				sortable:  ['name', 'used_by','managed_by'],

				sortable:  ['name', 'used_by','managed_by'],

				pagination:{chunk:5,nav: 'scroll'},

				requestAdapter(data) {

					return {
						'sort-field': data.orderBy ? data.orderBy : 'name',
						'sort-order': data.ascending ? 'desc' : 'asc',
						'search-query':data.query.trim(),
						'page':data.page,
						'limit':data.limit,
					}

				},

				responseAdapter({data}) {
					return {
						data: data.data.assets.map(data => {

						data.detach = true;

						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		computed:{
	      
	      ...mapGetters(['getStoredTicketId'])
	    
	    },

		components:{
		
			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		
		} 

		
	};
</script>

<style type="text/css">
	
	.VueTables__table input[type=checkbox] {
	    display: block !important;
	}
 	
 	.asset-check-used{
		width: 22%;
		word-break: break-all;
	}
	.asset-check-manage{
		width: 22%;
		word-break: break-all;
	}
	.asset-check-name{
		width: 23%;
		word-break: break-all;
	}
	.asset-check-contract{
		width: 23%;
		word-break: break-all;
	}
</style>