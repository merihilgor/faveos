<template>
	
	<div class="box-body">
		<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="associated-asset-list"
		componentTitle="AssetList">
			
		</data-table>
	</div>

</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import Vue from 'vue'

	import { mapGetters } from 'vuex'

	Vue.component('sdesk-data-table-actions', require('../MiniComponents/SdeskDataTableActions.vue'));

	Vue.component('asset-name', require('./MiniComponents/AssetNameComponent.vue'));

	export default {
		
		name : 'associated-assets',

		description : 'Associated Assets table component',

		props : {
			data : { type:Object|String, default : ()=>{}},
			alertName : { type : String, default : '' }
		},

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
			columns: [],
				
			options: {},
			
			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'',

				
		}),

		computed:{
	      ...mapGetters(['getStoredTicketId'])
	    },

	    watch:{
	      getStoredTicketId(newValue,oldValue){
	       	this.apiUrl = '/service-desk/api/asset-list?ticket_ids='+newValue
	      }
	    },

	    beforeMount(){

	    	const self = this;

	    	const details = JSON.parse(this.data);
				
				if(details.from){

					let baseUrl = "/service-desk/api/asset-list"
					
					let params = details.from === 'user' ?  'used_by_ids='+details.user_id : 'org_ids='+details.org_id
					
					this.apiUrl = baseUrl + "?" + params

				} else {

	    		this.apiUrl = '/service-desk/api/asset-list?ticket_ids='+this.getStoredTicketId

				}
	    	
	    	this.columns = JSON.parse(this.data).show_detach_asset ? ['name', 'managed_by', 'used_by','contract', 'action'] : ['name', 'managed_by', 'used_by','contract']

	    	this.options = {

					texts : { 'filter' : '', 'limit': ''},

					headings: { name: 'Name', managed_by: 'Managed by', used_by: 'Used by', contract : 'Contract', action:'Action'},

					templates: {

						name : 'asset-name',

						managed_by(h,row){  return row.managed_by ? row.managed_by.full_name : '--'; },

						used_by(h,row){  return row.used_by ? row.used_by.full_name : '--'; },

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

						action: 'sdesk-data-table-actions'
					
					},

					sortable:  ['name', 'managed_by', 'used_by'],

					sortable:  ['name', 'managed_by', 'used_by'],

					pagination:{chunk:5,nav: 'scroll'},

					requestAdapter(data) {

						return {
							'sort-field': data.orderBy ? data.orderBy : 'name',
							'sort-order': data.ascending ? 'desc' : 'asc',
							'search-query':data.query.trim(),
							page:data.page,
							limit:data.limit,
						}

					},

					responseAdapter({data}) {
						return {
							data: data.data.assets.map(data => {

							data.detach = true;

							data['alertName'] = self.alertName;

							return data;
						}),
							count: data.data.total
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

<style type="text/css" scoped>

</style>