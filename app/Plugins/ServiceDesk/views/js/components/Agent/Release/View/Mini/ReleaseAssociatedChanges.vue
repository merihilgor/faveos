<template>

	<div id="changes_list">

		<data-table :url="apiEndPoint" :dataColumns="columns"  :option="options" 
			scroll_to="release-associates" :componentTitle="componentTitle">

		</data-table>

		<transition  name="modal">

			<release-change-detach v-if="showModal" :onClose="onClose" :showModal="showModal" :fieldId="fieldId" 
				:releaseId="releaseId" :compName="componentTitle" category="change">

      	</release-change-detach>
		</transition>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex'

	import Vue from 'vue'

	export default {

		name : 'release-associated-changes',

		description : 'Release associated changes table page',

		props : {

			actions : { type : String | Object, default : '' },

			apiUrl : { type : String, default : ''},

			releaseId : { type : String | Number, default : ''},

			componentTitle : { type : String, default : ''},
		},

		data() {

			return {

				columns: this.actions.release_change_detach ? ['identifier','subject','requester','status','priority','department','created_at', 'action'] : ['identifier','subject','requester','status','priority','department','created_at'],

				options : {},

				apiEndPoint : this.apiUrl,

				showModal : false,

				fieldId : '',
			}
		},

		computed :{
		
			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount(){

			const self = this;

			this.options = {
				
				columnsClasses : {
					
					identifier: 'change-identifier',

					subject: 'change-subject', 

					requester: 'change-requester', 

					status: 'change-status', 

					priority: 'change-priority', 

					department: 'change-department', 

					created_at: 'change-created',

					action: 'change-action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				texts: { filter: '', limit: '' },

				templates: {

					requester: function(createElement, row) {
						
						if(row.requester){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/user/'+row.requester.id,
										
										target : '_blank'
									},
							}, row.requester.full_name ? row.requester.full_name : row.requester.email);
						} else { 
							return '---'
						}
					},

					status(h,row){
						
						return row.status ? row.status.name : '---';
					},

					priority(h,row){

						return row.priority ? row.priority.name : '---';
					},

					department: function(createElement, row) {
						
						if(row.department){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/department/'+row.department.id,
										
										target : '_blank'
									},
							}, row.department.name);
						} else { 
							return '---'
						}
					},

					created_at(h,row) {
						
						return self.formattedTime(row.created_at)
					},

					action: function(createElement, row) {
				  
					let span = createElement('i', {  attrs:{ 'class' : 'fa fa-unlink' } });
				
					return createElement('a', {

						attrs: { href: 'javascript:;', class : 'btn btn-primary' },

						on: {
							
							click: function() {
						  
								self.showModal = true;

								self.fieldId = row.id
							}
						}
					}, [span]);
				},
				},

				sortable:  ['identifier','subject','created_at'],
				
				filterable:  ['identifier','subject','requester','status','priority','department'],
				
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
						
						data: data.data.changes,

						count: data.data.total
					}
				},
			}
		},

		methods : { 

			onClose(){
		       
		      this.showModal = false;
		    },
		},

		components : {

			'data-table': require('components/Extra/DataTable'),
			
			'release-change-detach': require('./Child/ReleaseDetach'),
		}
	};
</script>

<style type="text/css">
	
	.table-bordered { font-size: 14px !important; }
	
	.change-identifier,.change-subject,.change-requester,.change-priority,.change-status,.change-department,.change-created,.change-action{ 
		max-width: 250px; word-break: break-all;
	}
	#changes_list .VueTables .table-responsive {
		overflow-x: auto;
	}
	#changes_list .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>