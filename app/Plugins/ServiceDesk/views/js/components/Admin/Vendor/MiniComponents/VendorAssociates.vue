<template>
	
	<div>
		
		<data-table :url="apiEndPoint" :dataColumns="columns"  :option="options" scroll_to="vendor-associates"
			componentTitle="vendor-view">

		</data-table>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { mapGetters } from 'vuex'

	export default {

		name : 'vendor-associates-table',

		description : 'Vendor associates table',

		props : {

			vendorId : { type : String | Number, default : ''},

			category : { type : String , default : ''},
		},

		computed : {

			apiEndPoint(){

				return this.category === 'contracts' ? '/service-desk/api/vendor/contract/' + this.vendorId : '/service-desk/api/vendor/product/' + this.vendorId;
			},

			columns() {

				return this.category === 'contracts' ? ['name','cost','contract_start_date','contract_end_date'] : ['name','manufacturer','status','department']; 
			},

			options(){

				const self = this;

				return { 

					sortIcon: {
						
						base : 'glyphicon',
						
						up: 'glyphicon-chevron-down',
						
						down: 'glyphicon-chevron-up'
					},
								 
					texts : { filter : '', limit : '' },

					columnsClasses : {

						name: 'associate-name', 

						cost: 'contract-cost', 

						contract_start_date: 'contract-start',

						contract_end_date: 'contract-end',

						manufacturer : 'product-manufacturer',

						department : 'product-dept',

						status : 'product-status'
					},
					
					templates: {

						name: function(createElement, row) {
						
							return createElement('a', {
									
								attrs:{
											
									href : self.basePath() + '/service-desk/'+ self.category + '/' + row.id + '/show',
											
									target : '_blank'
								},
							}, row.name);
						},
						
						status(h,row){

							return row.product_status ? row.product_status.name : '--';
						},

						department(h,row){

							return row.department ? row.department.name : '--';
						},

						contract_start_date(h,row){

							return row.contract_start_date ? self.formattedTime(row.contract_start_date) : '--'
						},

						contract_end_date(h,row){

							return row.contract_end_date ? self.formattedTime(row.contract_end_date) : '--'
						}
					},

					sortable:  self.columns,
					
					sortable:  self.columns,
					
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
							
							data: data.data[self.category].map(data => {
	
							return data;
						}),
						
						count: data.data.total
					}
				}
			}
		},

		...mapGetters(['formattedTime','formattedDate'])
	},
		components : {
			
			'data-table': require('components/Extra/DataTable')
		}
	};
</script>

<style>
	
	.associate-name { 
		width : 30%;
		word-break: break-all;
	}

	.contract-cost { 
		width : 20%;
		word-break: break-all;
	}

	.contract-start { 
		width : 25%;
		word-break: break-all;
	}

	.contract-end { 
		width : 25%;
		word-break: break-all;
	}

	.product-dept{
		width : 25%;
		word-break: break-all;
	}

	.product-status{
		width : 20%;
		word-break: break-all;
	}

	.product-manufacturer{
		width : 25%;
		word-break: break-all;
	}
</style>