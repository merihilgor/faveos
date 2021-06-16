<template>

  <div>
    <alert componentName="taskDetails" />
    <div class="box box-primary">

    <div class="box-header with-border">
      <h2 class="box-title" style="vertical-align:middle" :title="task.subject"> 
        {{ subString(task.task_name,50) }} 
        <span class="label" :class="labelClass">{{ task.status }}</span>
        <span v-if="overdue" class="label label-danger">{{ lang('task-plugin-overdue') }}</span>
      </h2>
      <task-actions :task="task"></task-actions>
    </div>

    <div class="box-body">

      <div class="row">

        <div class="col-md-12">

          <div class="callout callout-info">

            <div class="row">

              <div class="col-md-4" :title="formattedTime(task.created_at)">

                <b>{{lang('created_date')}} : </b> {{ formattedTime(task.created_at) }}

              </div>

              <div class="col-md-4" :title="task.task_start_date">

                <b>{{lang('start_date')}} : </b> {{ formattedTime(task.task_start_date) }}

              </div> 

              <div class="col-md-4" :title="task.task_end_date">

                <b>{{lang('end_date')}} : </b> {{ formattedTime(task.task_end_date) }}
                    
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-md-12">

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('task_name') }}</label></div>

            <div class="col-md-6">

              <a :title="lang('task-details-name')">{{task.task_name | capitalize}}</a>

            </div>

          </div>


          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('created_by') }}</label></div>

            <div class="col-md-6">
              <a :title="lang('task-details-created-by')">{{task.created_by.full_name}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('ticket_assoc') }}</label></div>

            <div class="col-md-6">

              <a :title="lang('task-details-ticket')" href="#"  @click.prevent="gotoTicket()">
                {{(task.ticket) ? subString(task.ticket.ticket_number,30) : '---'}}
              </a>
            </div>

          </div>


          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('task_status') }}</label></div>

            <div class="col-md-6">

              <a :title="lang('task-details-status')">{{task.status | capitalize}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('assignees') }}</label></div>

            <div class="col-md-6">

              <a v-if="task.assigned_agents.length === 0" class="text-center">---</a>

              <template v-else>

                <a v-for="(agent,index) in task.assigned_agents"  :title="lang('task-details-agents')">
                  {{subString(agent.name)}}<span v-if="index !== Object.keys(task.assigned_agents).length - 1">, </span>
                </a>

              </template>

            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('task_description') }}</label></div>

            <div class="col-md-6">

              <a href="javascript:;" class="btn btn-info btn-xs" @click="showDescription = true">
                <i class="fa fa-file-text">&nbsp;&nbsp;</i>{{lang('show_description')}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('task_list') }}</label></div>

            <div class="col-md-6">

              <a :title="lang('task-details-ticket')" >
                {{(task.task_list) ? subString(task.task_list.name,30) : '---'}}
              </a>
            </div>

          </div>

          <div class="col-md-6 info-row"></div>

        </div>
      </div>
    </div>

  </div>

    <transition name="modal">

      <task-description  v-if="showDescription" :onClose="onClose" :showModal="showDescription" :description="task.task_description">

      </task-description>
    </transition>

  </div>


</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'
  import axios from 'axios';
  import { mapGetters } from 'vuex';

  export default {

    props : {
      task : { type : Object, default : ()=>{} },
    },

    filters: {
      capitalize: function (value) {
        if (!value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
      }
    },

    data(){
      return {
        showDescription : false,
      }
    },

    computed :{

      labelClass() {
        switch (this.task.status) {
            case "Open":
              return 'label-success';
            case "Closed":
              return 'label-danger';
            case "In-progress":
              return 'label-warning';
            default:
              return 'label-default'
        }
      },

      overdue() {
        let today = new Date().toISOString();
        return today > this.task.task_end_date;
      },

      ...mapGetters(['formattedTime','formattedDate']),
    },

    methods : {

      subString(value,length = 15) {
        return getSubStringValue(value,length)
      },

      gotoTicket() {
        if (this.task.ticket)
          window.open(this.basePath() + "/thread/"+this.task.ticket_id, "_blank");
      },

      onClose(){
        this.showDescription = false;
      },

    },
    
    components : {
      'task-actions'     : require('./TaskActions'),
      'alert'            : require("components/MiniComponent/Alert"),
      'task-description' : require('./MiniComponents/TaskDescription')
    }
  };
</script>

<style scoped>
  .info-row{
    border-top: 1px solid #f4f4f4; padding: 10px;
  }
</style>