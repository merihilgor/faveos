<template>

	<div class="box box-custom">

		<div class="box-header">
			
			<button type="button" class="btn btn-primary pull-right" @click="showModal = true">

				<i class="fa fa-plus"> </i> {{lang('add_tasklist')}}
			</button>
		</div>

		<div class="box-body" id="task-list">
			
			<data-table :url="apiUrl" :dataColumns="columns" :option="options" componentTitle="TaskList" 
				scroll_to="task-list">
				
			</data-table>
		</div>

		<transition name="modal">
			
			<task-list-modal v-if="showModal" :onClose="onClose" :showModal="showModal" title="tasklist_create" 
				componentTitle="TaskList">

			</task-list-modal>
		</transition>
	</div>
</template>

<script>
	
	import { mapGetters } from 'vuex'

	import Vue from 'vue';

  Vue.component('task-table-actions', require('./TaskTableActions.vue'));

	export default {

		name : 'task-list',

		description : 'Task List Component',

		props : {

			category : { type : String, default : '' }
		},

		data() {

			return {

				apiUrl : '/tasks/tasklists',

				columns: ['name','project','created_at','action'],

				showModal : false
			}
		},

		beforeMount() {

			const self = this;

			this.options = {

				columnsClasses : {

          name: 'tasklist-name', 
          
          project: 'tasklist-created-at',

					created_at: 'tasklist-created-at',

					action: 'tasklist-action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
				},

        texts: { filter: '', limit: '' },

				templates: {

					created_at(h,row) {
						return self.formattedTime(row.created_at)
					},

					project(h,row) {

						return row.project.name
					},

          action: 'task-table-actions',
          
				},

				sortable:  ['name', 'created_at'],
				
				filterable:  ['name'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
				
					return {
						
						'sortField' : data.orderBy ? data.orderBy : 'id',
						
						'sortOrder' : data.ascending ? 'desc' : 'asc',
						
						'searchTerm' : data.query.trim(),
						
						page : data.page,
						
						limit : data.limit,
					}
				},

				responseAdapter({data}) {

					return {

						data: data.data.tasklists.map(data => {

						data.edit_modal = true;

						data.delete_url = self.basePath() + '/tasks/tasklist/delete/' + data.id;

						data.componentName = 'TaskList';

						return data;

					}),
						count: data.data.total
					}
				},
			}
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		methods : {

			onClose(){
			
				this.$store.dispatch('unsetValidationError');
			
				this.showModal = false
			},
		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			'task-list-modal' : require('./TaskListModal'),
		}
	};
</script>

<style>
	.box-custom{
		border-top: 0px solid !important;
	}
	.tasklist-name{
		width:25% !important;
		word-break: break-all;  
	}
	.tasklist-created-at {
    width:30% !important;
		word-break: break-all;
  }
	.tasklist-action {
    width:25% !important;
		word-break: break-all;
  }
</style>