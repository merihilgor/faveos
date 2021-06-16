<template>

	<div id="cab-index">
	
		<alert componentName="dataTableModal"/>
		
		<div class="box box-primary">
			
			<div class="box-header with-border">
				
				<div class="row">
					
					<div class="col-md-4">
						
						<h2 class="box-title">{{lang('list_of_cabs')}}</h2>
					</div>
					
					<div class="col-md-8">
						
						<a class="btn btn-primary right" :href="basePath()+'/service-desk/cabs/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create_cab')}}
						</a>
					</div>
				</div>

				<data-table v-if="apiUrl !== ''" :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="cab-index">
					
				</data-table>

			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import { mapGetters } from 'vuex';

	import Vue from 'vue';

	Vue.component('table-actions',require('components/MiniComponent/DataTableComponents/DataTableActions.vue'))

	export default {

		name : 'cab-index',

		description : 'Cab table component',

		data(){

			return {

				columns:['name', 'created_at', 'updated_at', 'action'],

				options:{},
				
				apiUrl:'/service-desk/api/cab',
			}	
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount(){
			
			const self = this;

			this.options= {
				
				texts: {
					
					filter: '',
					
					limit: ''
				},
				
				headings: { name: 'Name', CreatedAt: 'created_at', UpdatedAt: 'created_at', action:'Action'},
				
				templates: {
				
					action: 'table-actions',
				
					created_at(h, row) {
						
						return self.formattedTime(row.created_at);
					},
					
					updated_at(h, row) {
						
						return self.formattedTime(row.updated_at);
					},
				},

				sortable:  ['name', 'created_at', 'updated_at'],
				
				filterable: ['name', 'created_at', 'updated_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
					
					return {
						
						sort_by: data.orderBy ? data.orderBy : 'id',
						
						order: data.ascending ? 'desc' : 'asc',
						
						search:data.query.trim(),
						
						page:data.page,
						
						limit:data.limit,
					}
				},

				responseAdapter({data}) {
					
					return {
					
						data: data.data.data.map(data => {
					
						data.edit_url = self.basePath() + '/service-desk/cabs/' + data.id + '/edit';
					
						data.delete_url = self.basePath() + '/service-desk/api/cab/' + data.id + '/workflow/';

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
