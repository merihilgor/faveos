<template>

	<div class="box box-custom">

		<div class="box-header">
			
			<button type="button" class="btn btn-primary pull-right" @click="showModal = true">

				<i class="fa fa-plus"> </i> {{lang('add_project')}}
			</button>
		</div>

		<div class="box-body" id="task-projects">
			
			<data-table :url="apiUrl" :dataColumns="columns" :option="options" componentTitle="TaskProjects" 
				scroll_to="task-projects">
				
			</data-table>
		</div>

		<transition name="modal">
			
			<task-modal v-if="showModal" :onClose="onClose" :showModal="showModal" title="add_project" :containerStyle="style"
				componentTitle="TaskProjects">

			</task-modal>
		</transition>
	</div>
</template>

<script>
	
	import { mapGetters } from 'vuex'

	import Vue from 'vue';

  Vue.component('task-table-actions', require('./TaskTableActions.vue'));

	export default {

		name : 'task-project',

		description : 'Task Project Component',

		props : {

			category : { type : String, default : '' }
		},

		data() {

			return {

				apiUrl : '/tasks/projects',

				columns: ['name','created_at','action'],

				showModal : false,

				style:{ width:'500px' },
			}
		},

		beforeMount() {

			const self = this;

			this.options = {

				columnsClasses : {

					name: 'project-name', 

					created_at: 'project-created-at',

					action: 'project-action',
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

					action: 'task-table-actions'
				},

				sortable:  ['name', 'created_at'],
				
				filterable:  ['name', 'created_at'],
				
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

						data: data.data.projects.map(data => {

						data.edit_modal = true;

						data.delete_url = self.basePath() + '/tasks/project/delete/' + data.id;

						data.componentName = 'TaskProjects';
						
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

			'task-modal' : require('./TaskModal'),
		}
	};
</script>

<style>
	.box-custom{
		border-top: 0px solid !important;
	}
	.project-name{
		width:35% !important;
		word-break: break-all;
	}
	.project-created-at{
		width:40% !important;
		word-break: break-all;
	}
	.project-action{
		width:15% !important;
		word-break: break-all;
	}
</style>