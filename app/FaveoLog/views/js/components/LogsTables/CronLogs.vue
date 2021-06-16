<template>
	<faveo-box :title="lang('cron_logs')">

		<div slot="headerMenu" class="pull-right" style="cursor:pointer">
			<span><i class="fa fa-refresh" aria-hidden="true" :title="lang('refresh')" @click="refreshTable()"></i></span>
		</div>

		<div class="row">
			<date-time-field :label="lang('created_date')"
				:value="created_date"
				type="datetime"
				:time-picker-options="timeOptions"
				name="created_date"
				:required="false"
				:onChange="onChange" range
				:currentYearDate="false"
				format="YYYY-MM-DD HH:mm:ss" classname="col-xs-6"
				:clearable="true" :editable="true" :disabled="false">
			</date-time-field>
		</div>

		<div class="row">
			<logs-table
				id="cron_logs_title"
				v-if="!options.length"
				:created_at_start="created_at_start" 
				:created_at_end="created_at_end" 
				:cron_start_time_start="cron_start_time_start"
				:cron_start_time_end="cron_start_time_end"
				:columns="columns"
				:options="options"
				:apiUrl="apiUrl"
				componentTitle="cronLogs">
			</logs-table>
		</div>
	</faveo-box>
</template>

<script>

	import axios from 'axios';
	import moment from 'moment'
	import { mapGetters } from 'vuex';
	import FaveoBox from 'components/MiniComponent/FaveoBox';
	import Vue from 'vue';

	Vue.component('log-status', require('./ReusableComponent/LogStatus'));

	export default {

		data() {

			return {

				apiUrl : '/api/logs/cron' ,

				columns: ["command", "description", 'duration', "created_at", "status"],

				options : {},

				cron_start_time_start : '',

				cron_start_time_end : '',

				moment : moment,

				timeOptions: {
					start: '00:00',
					step: '00:30',
					end: '23:30'
				},

				created_date : '',
				created_at_start : '',
				created_at_end : '',
			}
		},

		beforeMount(){
			const self = this;
			this.options = {

				headings: { 
					duration: this.trans("duration_in_seconds"),

					command: this.trans("command"),

					description: this.trans("description"),

					created_at: this.trans("created_at"),

					status: this.trans('status'),
				},

				columnsClasses : { 

					message : 'cron-log-message',

					created_at : 'cron-created',
					status: 'log-status'
				},

				texts: { filter: '', limit: '' },

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				templates: {

					created_at(h, row) {
						
						return self.formattedTime(row.created_at)
					},

					status : 'log-status',
				},

				sortable:  ["command", "description", 'duration', "created_at", "status"],

				filterable:  ['category', 'start_time', 'end_time', 'message', 'created_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},

				requestAdapter(data) {

					return {
					
						sort_field: data.orderBy,
					
						sort_order: data.ascending ? 'desc' : 'asc',
					
						'search-query':data.query.trim(),
					
						page:data.page,
					
						limit:data.limit,

					}
				},
				responseAdapter({data}) {
					return {
						
						data: data.data.data,

						count: data.data.total
					
					}
				},
			}
		},

		computed:{
			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			onChange(value,name){

				this[name] = value

				if(name === 'created_date'){
					this.created_at_start = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD+HH:mm:ss') : '';
					this.created_at_end =  value[1] !== null ? moment(value[1]).format('YYYY-MM-DD+HH:mm:ss') : '';
				}

				this.cron_start_time_start = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD+HH:mm:ss') : '';
				
				this.cron_start_time_end = value[1] !== null ? moment(value[1]).format('YYYY-MM-DD+HH:mm:ss') : '';
			},

			refreshTable() {
				window.eventHub.$emit('cronLogsrefreshData');
			}
		},

		components : {
			'logs-table': require('./ReusableComponent/LogsTable.vue'),
			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
			'faveo-box' : FaveoBox
		}
	};
</script>

<style>
	.cron-log-message{
		width:30% !important;
		word-break: break-all;
	}

	.cron-created{
		width:25% !important;
		word-break: break-all;
	}

</style>