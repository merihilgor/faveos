<template>
	
	<div>
	
		<alert componentName="task-view"/>

		<div class="box box-primary pblm-table">
	
			<div class="box-header with-border">
	
				<div class="row">
	
					<div class="col-md-4">
	
						<h2 class="box-title">{{ lang('list_of_tasks')}}</h2>
						
					</div>
	
					<div class="col-md-8">

						<template>

							<a class="btn btn-primary add-task" :href="basePath()+'/tasks/task/create/'">
								<span class="glyphicon glyphicon-plus"> </span> {{lang('add_tasks')}}
							</a>

							<a id="advance-filter-btn" class="btn btn-primary pull-right round-btn" @click = "toggleFilterView">
								<span class="glyphicon glyphicon-filter"></span>
							</a>

							<button style="margin-right:5px;" class="btn btn-primary pull-right" @click.prevent="taskCalendarPage()">
								<span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;{{ lang('calendar_view') }}
							</button>

						</template>

					</div>
				</div>
			</div>

			<div class="box-body">
				
				<task-filter id="filter-box" v-if="isShowFilter" :metaData="filterOptions" 
					@selectedFilters="selectedFilters" :appliedFilters="appliedFilters"
				>
				
				</task-filter>

				<data-table :url="apiUrl" :dataColumns="columns" :option="options" scroll_to ="problem-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import Vue from 'vue';

	import { mapGetters } from 'vuex'

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));
	Vue.component('task-status', require('./TaskStatus'));

	export default {
		
		props: {
			category: {
				type: String,
				default: 'assigned'
			}
		},

		data() {
			return {

				appliedFilters : {},

				columns: ['task_name', 'status', 'task_start_date', 'task_end_date', 'ticket', 'assigned', 'action'],

				options: {},

				apiUrl:'',

				filterOptions: [
					{
						section : [
							{
								name: 'task_ids',
								url: `/tasks/list?category=${this.category}`,
								label: 'task',
							},

							{
								name: 'ticket_ids',
								url: 'api/dependency/tickets',
								label: 'ticket',
							},

							{
								name: 'task_lists',
								url: 'tasks/tasklist/view',
								label: 'tasklist',
							}


						]
					},

					{
						section : [

							{
								name: 'assigned_to',
								url: 'api/dependency/agents?meta=true',
								label: 'assignees',
							},

							{
								name: 'projects',
								url: '/tasks/project/view',
								label: 'project',
							},
							{
								name: 'status',
								elements: [
									{
										name: 'Open',
										id: 'Open'
									},
									{
										name: 'Closed',
										id: 'Closed',
									},
									{
										name:'In-progress',
										id: 'In-progress'
									}
								],
								label: 'task_status',
							}
						]
					},

					{
						section : [

							{
								name: 'created_by_array',
								url: 'api/dependency/agents?meta=true',
								label: 'created_by',
							},
						]
					}



				],

				isShowFilter : false,

				hideData : '',

				ticketId: ''
			}
		},

		computed :{
			...mapGetters(['formattedTime','formattedDate'])
		},

		created() {
			window.eventHub.$on('refreshData',this.updateSideBar);
		},

		beforeMount() {

			this.filterData();

			this.apiUrl = `/tasks/get-all-tasks?category=${this.category}`;

			const self = this;

			this.options = {

				headings: {
					task_name: 'Name', task_description : 'Description',
					status:'Status', task_start_date: 'Start Date',
					action:'Actions', task_end_date: 'Due Date',
					ticket: 'Ticket', assigned: 'Assignee(s)'
				},

				columnsClasses : {

					task_name: 'task-name', 

					task_start_date:'task-date',

					task_end_date: 'task-date',

					status: 'task-status',

					action: 'task-action',

					ticket_id: 'task-ticket',

					assigned: 'task-assigned'
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				templates: {

					task_start_date(h,row) {
						if (!row.task_start_date) {
							return "--";
						}
						return self.formattedTime(row.task_start_date)
					},

					task_end_date(h,row) {
						if (!row.task_end_date) {
							return "--";
						}
						return self.formattedTime(row.task_end_date)
					},

					ticket: (createElement,row) => {
						let ticket = row.ticket;
						if (!ticket)
							return '--';
						return createElement('a', {
							attrs: {
								href: self.basePath()+'/thread/'+ticket.id,
								target: "_blank"
							}
						}, ticket.ticket_number)
					},

					status: 'task-status',

					assigned: 'table-list-elements',

					action: 'table-actions'
				},

				sortable:  ['task_name', 'task_start_date','task_end_date'],
				
				filterable:  ['task_name'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
				
					return {
						
						'sort_field' : data.orderBy ? data.orderBy : 'id',
						
						'sort_order' : data.ascending ? 'desc' : 'asc',
						
						'search_term' : data.query.trim(),
						
						page : data.page,
						
						limit : data.limit,
					}
				},

				responseAdapter({data}) {

					return {
						data: data.data.tasks.map(data => {

						data.view_url = self.basePath() + '/tasks/task/' + data.id;

						data.edit_url = self.basePath() + '/tasks/task/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/tasks/task/' + data.id;

						data.listElementObj = {
							key: 'assigned_agents',
							redirectUrl: self.basePath() + '/user/'
						}

						data.alertComponentName = 'task-view';

						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		methods:{

			updateSideBar() {
				window.eventHub.$emit('update-sidebar');
			},

			filterData(){

				this.filterOptions.map(function (obj) {
					obj.section.map(function(row) {
						row.elements = (row.elements) ? row.elements : [];
						row.isMultiple = true;
						row.isPrepopulate = false;
						row.value = '';
						row.className = 'col-xs-4';
					})
				});
			},

			selectedFilters(value){ 

				if(value === 'closeEvent') {
				
					return this.isShowFilter = false;
				}
				if(value === 'resetEvent') {
					
					return this.resetFilter();
				}
				
				return this.applyFilter(value)
			},

			resetFilter(){
				this.apiUrl = '/tasks/get-all-tasks?';
				this.filterOptions = this.filterOptions.map(function (obj) { 
					obj.section.map(function(row){
						row.elements = (row.elements) ? row.elements : [];
						row.isMultiple = true;
						row.isPrepopulate = false;
						row.value = '';
						row.className = 'col-xs-4';
					})
					return obj;
				});
			},

			applyFilter(value){

				this.appliedFilters = value;

				let baseUrlForFilter = `/tasks/get-all-tasks?category=${this.category}&`;
				
				let params = '';

				for( var i in this.filterOptions){

					for(var j in this.filterOptions[i].section){

						if(value[this.filterOptions[i].section[j].name]){

							this.filterOptions[i].section[j].value = value[this.filterOptions[i].section[j].name];

							value[this.filterOptions[i].section[j].name].forEach(function(element, index) {

								params +=  this.filterOptions[i].section[j].name+'[' + index + ']=' + element.id + '&'
							},this);
						}     		
					}
				}

				if(params[params.length-1] === '&') {
					params = params.slice(0, -1);
				}

				this.apiUrl = baseUrlForFilter + params;
					
				this.isShowFilter = false;
			},

			toggleFilterView() {
				
				this.isShowFilter = !this.isShowFilter;
			},

			taskCalendarPage() {
				this.redirect(`/tasks/?category=${this.category}`);
			}
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),
			
			"alert": require("components/MiniComponent/Alert"),

			'task-filter' : require('./TaskFilter')

		}
	};
</script>

<style scoped>
	
	.task-name{
		width:10% !important;
		word-break: break-all;
	}

	.task-date{
		width:15% !important;
		word-break: break-all;
	}

	.task-action{
		width:15% !important;
		word-break: break-all;
	}

	.task-status{
		width:15% !important;
		word-break: break-all;
	}

	.task-ticket {
		width: 18% !important;
		word-break: break-all;
	}

	.task-assigned {
		width: 17% !important;
		word-break: break-all;
	}

	.pblm-table {
		padding: 0 !important;
	}

	.add-task{
		float: right;
	}

	.round-btn {
		margin-right: 5px;
	}
	#associates_tab{ cursor: pointer; }

	.text-muted { color: #777 !important; }
</style>
