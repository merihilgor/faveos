<template>

    <div>

        <alert componentName="taskView"/>

        <div class="box box-primary pblm-table">

            <div class="box-header with-border">

                <div class="row">

                    <div class="col-md-4">

                        <h2 class="box-title">{{lang('associated_tasks') }}</h2>

                    </div>

                    <div class="col-md-8">
                        <!-- <button style="margin-left:5px;" class="btn btn-primary pull-right" @click.prevent="taskCalendarPage()"><span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;{{ lang('calendar_view') }}</button> -->

                        <a id="associates_tab" class="text-muted pull-right" @click="refreshTable"
                           v-tooltip="lang('click_to_refresh')">

                            <i class="fa fa-refresh"></i>
                        </a>



                    </div>
                </div>
            </div>

            <div class="box-body">
                <data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="problem-list"></data-table>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">

    import Vue from 'vue';

    import { mapGetters } from 'vuex'

    Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));
    Vue.component('task-status', require('./TaskStatus'));

    export default {

        props: ['ticketProp'],

        data: () => ({

            columns: ['task_name', 'task_description', 'status', 'task_start_date', 'task_end_date', 'assigned', 'action'],

            options: {},

            apiUrl:'',

            hideData : '',

            ticketId: ''
        }),

        computed :{

            ...mapGetters(['formattedTime','formattedDate','getStoredTicketId'])
        },

        watch:{

            getStoredTicketId(newValue,oldValue){

                this.apiUrl = '/tasks/get-all-ticket-tasks?ticket_id='+newValue
            }
        },

        created() {
            window.eventHub.$on('refreshData',this.updateSideBar);
        },

        beforeMount() {
            if(this.ticketProp)
                this.apiUrl = '/tasks/get-all-ticket-tasks?ticket_id='+this.getStoredTicketId;

            const self = this;

            this.options = {

                headings: {
                    task_name: 'Name', task_description : 'Description',
                    status:'Status', task_start_date: 'Start Date',
                    action:'Actions', task_end_date: 'Due Date', assigned: 'Assignee(s)'
                },

                columnsClasses : {

                    task_name: 'task-name',

                    task_start_date:'task-date',

                    task_end_date: 'task-date',

                    status: 'task-status',

                    action: 'task-action',

                    task_description: 'task-description'

                },

                sortIcon: {

                    base : 'glyphicon',

                    up: 'glyphicon-chevron-up',

                    down: 'glyphicon-chevron-down'
                },

                texts: { filter: '', limit: '' },

                templates: {

                    task_start_date(h,row) {
                        return self.formattedTime(row.task_start_date)
                    },

                    task_end_date(h,row) {
                        return self.formattedTime(row.task_end_date)
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

                        'search_term' : data.query,

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

                            data.alertComponentName = 'timeline';

                            data.tableName =  'task'

                            data.listElementObj = {
                                key: 'assigned_agents',
                                redirectUrl: self.basePath() + '/user/'
                            }

                            return data;
                        }),
                        count: data.data.total
                    }
                },
            }
        },

        methods:{

            refreshTable() {

                window.eventHub.$emit('refreshData');
            },

            updateSideBar() {
                window.eventHub.$emit('update-sidebar');
            },

        },

        components : {

            "data-table" : require('components/Extra/DataTable'),

            "alert": require("components/MiniComponent/Alert"),


        }
    };
</script>

<style>

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
    .task-description{
        width:15% !important;
        word-break: break-all;
    }

    .task-assigned {
        width: 15% !important;
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

    .text-muted { color: #777777 !important; }
</style>
