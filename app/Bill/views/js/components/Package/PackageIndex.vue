<template>
	<div>
		
		<alert componentName="dataTableModal"/>

		<div class="box box-primary">

			<div class="box-header with-border">
				<div class="row">

					<div class="col-md-4">
						<h2 class="box-title" id="pack-title">{{lang('list_of_packages')}}</h2>
					</div>
					
					<div class="col-md-8">

						<a id="create_pack" class="btn btn-primary right" :href="base+'/bill/package/create'">

							<span class="glyphicon glyphicon-plus"> </span> {{lang('create-package')}}
						</a>
						
						<a v-if="selectedData.length > 0" id="delete_pack" class="btn btn-danger right" @click="deletePackage()">

							<span class="glyphicon glyphicon-trash"> </span> {{lang('delete-package')}}
						</a>
					</div>
				
				</div>
			</div>
			
			<div class="box-body">
				<!-- datatable -->
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="pack-title"  :tickets="packages"></data-table>
			</div>

			<transition name="modal">

			 	<delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="deleteUrl" ></delete-modal>
			</transition>

		</div>	
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { lang } from 'helpers/extraLogics';

	export default {

		name : 'pacakges',

		description : 'Pacakges data table component',

		props : {

		},

		data(){

			return {

				base:window.axios.defaults.baseURL,
				
				columns: ['id', 'name', 'validity', 'allowed_tickets', 'price', 'status', 'action'],

				options : {},
		
				apiUrl:'/bill/package/get-inbox-data',

				selectedData : [],

				showModal : false,

				deleteUrl : '',
			}
		},

		computed :{

		},

		watch : {

		},

		beforeMount(){
			
			const self = this;

			this.options = {
				
				headings: { 

					name: 'Name', 

					validity : 'Validity', 

					allowed_tickets : 'Incident credit',

					price : 'Price', 

					status: 'Status', 

					action:'Action'
				},
					
				texts: {
					
					filter: '',
					
					limit: ''
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},
					
				templates: {
						
					status: 'data-table-status',
						
					action: 'data-table-actions',

					validity(h,row){
						
						return row.validity === null ? lang('one_time') : lang(row.validity);
					}
				},
					
				sortable:  [ 'name', 'validity', 'allowed_tickets', 'price', 'status' ],
					
				filterable:  [ 'name', 'validity', 'allowed_tickets', 'price', 'status' ],
					
				pagination:{chunk:5,nav: 'fixed',edge:true},
					
				requestAdapter(data) {
					
					return {

						'sort-field': data.orderBy ? data.orderBy : 'id',
        
            'sort-order': data.ascending ? 'desc' : 'asc',
        
            'search-query':data.query.trim(),
        
            'page':data.page,
        
            'limit':data.limit,
						}
					},
					
					responseAdapter({data}) {
						
						return {
							
							data: data.message.data.map(data => {

								data.edit_url = window.axios.defaults.baseURL + '/bill/package/'+data.id+'/edit';

								return data;
							}),
							
							count: data.message.total
						}
					},
				}
		},

		methods : {
			packages(data){

				this.selectedData = data;
			},

			deletePackage(){

				this.deleteUrl = 'bill/package/delete?package_ids=' + this.selectedData

				this.showModal = true
			},

			onClose(){
		    
		    this.showModal = false;
		    
		    this.$store.dispatch('unsetValidationError');
		  },
		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),

			'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),
		}
	};
</script>

<style scoped>
	
	.box-header h3{ font-family: Source Sans Pro !important }
	
	.box.box-primary { padding: 0px !important; }
	
	.right{ float: right; }

	#dept-title{ margin-left: -8px }

	#delete_pack, #create_pack{  margin : 3px; }
</style>