<template>
	
	<div id="org-list">

		<alert componentName="dataTableModal"/>
		
		<div class="box box-primary">
		
			<div class="box-header with-border">
			
				<div class="row">
					
					<div class="col-md-3">
						
						<h3 class="box-title ">{{lang('organization_list')}}</h3>
					</div>
					
					<div class="col-md-9 text-right">

						<a :href="basePath()+'/organizations/create'" class="btn btn-primary">
								
							<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;{{lang('create_organization')}}
						</a>
					</div>
				</div>
			</div>
				
			<div class="box-body">
						
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="org-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script>
	
	import Vue from 'vue'

	import { mapGetters } from 'vuex'

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'organizations-directory',

		description : 'Organization list component',

		props : { 

		},

		data() {

			return {

				apiUrl : '/org-list-data' ,

				columns: ['name', 'phone', 'created_at', 'updated_at', 'action'],

				options : {},
			}
		},

		computed:{
    
      ...mapGetters(['formattedTime','formattedDate'])
    },

		beforeMount(){

			const self = this;
			
			this.options = {

				headings: {

					name: 'Name',

					phone: 'Phone',

					created_at : 'Created at',

					updated_at : 'Updated at',

					action: 'Action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				columnsClasses : {

					name: 'organization-name',

					phone: 'organization-phone',

					created_at : 'organization-created',

					updated_at : 'organization-updated',

					action: 'organization-action',
				},

				texts: { filter: '', limit: '' },

				templates: {

					created_at(h, row) {

						return self.formattedTime(row.created_at);
					},

					updated_at(h, row) {

						return self.formattedTime(row.updated_at);
					},

					phone(h, row) {

						return row.phone === ' Not available' ? '---' : row.phone;
					},

					name: function(createElement, row) {
						
						return createElement('a', {
							attrs: {
								href: self.basePath()+'/organizations/' + row.id,
							}
						}, row.name);
					},

					action : 'table-actions',
				},

				sortable: ['name', 'phone','created_at', 'updated_at'],

				filterable: ['name', 'phone','created_at', 'updated_at'],

				pagination:{chunk:5,nav: 'fixed',edge:true},

				requestAdapter(data) {

					return {

						'sort-by': data.orderBy,

						'order': data.ascending ? 'desc' : 'asc',

						'search-query':data.query.trim(),

						page:data.page,

						limit:data.limit,

					}
				},
				responseAdapter({data}) {
					return {

						data: data.message.data.map(data => {
							
							data.edit_url = self.basePath()+'/organizations/' + data.id + '/edit';
							
							data.view_url = self.basePath()+'/organizations/' + data.id ;

							data.delete_url = self.basePath()+'/org/delete/' + data.id;
							
							return data;
						}),
						
						count: data.message.total
					}
				},
			}
		},

		components : { 

			"data-table" : require("components/Extra/DataTable"),

			"alert": require("components/MiniComponent/Alert"),
		},
	};
</script>

<style>
	
	.organization-name{
		width:30% !important;
		word-break: break-word;
	}

	.organization-phone{
		width:15% !important;
		word-break: break-word;
	}

	.organization-created{
		width:20% !important;
		word-break: break-word;
	}

	.organization-updated{
		width:20% !important;
		word-break: break-word;
	}

	.organization-action{
		white-space: nowrap;
	}
</style>