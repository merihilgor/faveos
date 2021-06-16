<template>
	<div>
		
		<alert componentName="dataTableModal"/>

		<div class="box box-primary">

			<div class="box-header with-border">
				<div class="row">

					<div class="col-md-4">
						<h2 class="box-title" id="payment-title">{{lang('list_of_payment_gateways')}}</h2>
					</div>
				</div>
			</div>
			
			<div class="box-body">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="payment-title"></data-table>
			</div>

			<transition name="modal">
		
		 	<payment-settings-modal v-if="showModal" title="settings" :onClose="onClose" :showModal="showModal" :data="data">
		 	</payment-settings-modal>
		</transition>

		</div>	
	</div>
</template>

<script>
	
	import axios from 'axios';

	export default {

		name : 'payments',

		description : 'Payments data table component',

		data(){

			return {

				base:window.axios.defaults.baseURL,
				
				columns: ['name', 'gateway_name', 'is_default', 'status', 'action'],

				options : {},
		
				apiUrl:'/bill/get-gateways-list',

				showModal : false,

				data : {}
			}
		},

		beforeMount(){
			
			const self = this;

			this.options = {
					
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
						
					action: function(createElement, row) {
            
            let i = createElement('i', {
              attrs:{
               	'class' : 'fa fa-cogs'
              }
            });
            
            return createElement('button', {
              attrs:{
                class : 'btn btn-primary btn-xs'
              },
              on: {
				        click: function() {
				         	self.onClick(row)
				        }
				      }
            }, [i]);
          },
					
					is_default : 'data-table-is-default'
				},
					
				sortable:  [ 'name', 'gateway_name', 'is_default', 'status' ],
					
				filterable:  [ 'name', 'gateway_name', 'is_default', 'status' ],
					
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
							
							data: data.data.data,
							
							count: data.data.data.length
						}
					},
				}
		},

		methods : {
			onClick(data){

				this.data = data;
				
				this.showModal = true
			},

			onClose(){
				this.showModal = false;
				this.$store.dispatch('unsetValidationError');
			}
		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),

			'payment-settings-modal' : require('./PaymentSettingsModal')
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