<template>

	<div id="associated-changes">
		
		<alert componentName="AssociatedChange"/>

		<div :class="[{'box box-primary' : !from}]">
		
			<div v-if="from != 'tab'" class="box-header with-border">
		
				<div class="row">
		
					<div class="col-md-4">
		
						<h2 class="box-title">{{lang('associated_changes')}}</h2>
		
					</div>
		
				</div>
			</div>

			<div v-if="loading" class="row">
								
				<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
				
			</div>

			<data-table :url="apiUrl" :dataColumns="columns"  :option="options" componentTitle="AssociatedChange"
			scroll_to="associated-changes">
				
			</data-table>

		</div>

		<transition name="modal">

		 	<change-detach-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :ticketId="getStoredTicketId"
		 	:changeId="changeId" :type="change_type" alert="timeline"> 
		 	
		 	</change-detach-modal>

		</transition> 
	</div>

</template>

<script>
	
	import axios from 'axios'

	import { mapGetters } from 'vuex'

	export default {

		name : 'associated_contracts',

		description : 'Associated contracts data table page',

		props : { 
    
      data: {type: String|Object},

      from : { type : String, default : ''}
    },

		data() {
			return {
				
				currentComponent:'associated_changes',
				
				loading:false,

				apiUrl: '',

				columns: ['change_number','subject', 'type', 'action'],

				options: null,

				changeId : '',

				change_type : '',

				showModal : false

			}
		},

		computed:{

			...mapGetters(['getStoredTicketId'])
		},

		watch:{
			
			getStoredTicketId(newValue,oldValue){
				
				this.apiUrl = '/service-desk/api/attached-change/ticket?ticket_id='+newValue	
			}
		},

		beforeMount(){

			const self = this;

			this.apiUrl = '/service-desk/api/attached-change/ticket?ticket_id='+this.getStoredTicketId

			this.options = {

				texts : { 'filter' : '', 'limit': ''},

				columnsClasses : {

					change_number: 'changes-number',

					subject: 'changes-subject', 

					type: 'changes-created',

					action: 'changes-action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				templates: {

					subject(createElement, row){

						return createElement('a', {
		          
		          attrs:{

		            href : self.basePath()+'/service-desk/changes/' + row.id + '/show',
		          },
		        }, row.subject);
					},

					action(createElement,row){

						var actions = JSON.parse(self.data);

						let icon = createElement('i', {
              attrs:{
                'class' : 'fa fa-unlink'
              }
            });
            
            return createElement('a', {

            	attrs:{ class : 'btn btn-primary', title : 'Detach', disabled : !actions.detach_change },
              
              on: {
			          click: function() {
			            self.onDetach(row.id, row.type,actions.detach_change);
			          }
			        }
            }, [icon]);
					}
				},

				sortable:  ['subject','created_at'],
				
				filterable:  ['subject','created_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},

				requestAdapter(data) {
	      
	        return {
	          
	          'sort-field' : data.orderBy ? data.orderBy : 'id',
	          
	          'sort-order' : data.ascending ? 'desc' : 'asc',
	          
	          'search_query' : data.query.trim(),
	          
	          page : data.page,
	          
	          limit : data.limit,
	        }
	      },

				responseAdapter({data}) {

					return {
						data: data.data.changes,

						count: data.data.total
					}
				},
			}
		},

		methods : {

			onDetach(id, type,condition){
				
				if(condition){

					this.changeId = id;

					this.change_type = type;

					this.showModal = true;
				}
			},

			onClose(){

				this.showModal = false;
			}
		},

		components : {
			
			'data-table' : require('components/Extra/DataTable'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			"alert": require("components/MiniComponent/Alert"),

			'change-detach-modal' : require('./DetachChange')
		
		}
	
	};
</script>

<style scoped>
	#asset_tab{
		cursor: pointer;
	}
</style>