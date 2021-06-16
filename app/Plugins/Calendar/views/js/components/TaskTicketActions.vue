<template>

  <div id="task-ticket-actions">

      <div v-if="ticketActions.has_calender" class="btn-group">
      
      <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" id="task-action">
        
        <i class="fa fa-calendar-plus-o"> </i> {{ lang('task') }} 

        <span class="badge label label-danger smaller">{{taskCount}}</span>
        
        <span class="caret"></span> 
      </button>
        
      <ul class="dropdown-menu" role="menu">
          
        <li>

          <a href="javascript:;" @click="showModal = true"> 

             <i class="fa fa-plus"> </i> {{ lang('create_task') }}
          </a>
        </li>
          
        <li>

          <a href="javascript:;" v-scroll-to="'#timeline-display-box-tasks'" > <i class="fa fa-eye"></i>{{ lang('view') }}</a>
        </li>
      </ul>
    </div>

    <transition name="modal">

      <task-create-modal v-if="showModal" :onClose="onClose" :showModal="showModal" 
        :ticketId="ticketId" componentTitle="timeline" :reloadDetails="reloadData">

      </task-create-modal>
    </transition>
  </div>

</template>

<script>

  import {mapGetters} from 'vuex';

  export default {

      name: 'task-ticket-actions',

      description : 'Contains ticket actions on timline page, specific to Task Plugin',

      props :{
       
        data: {type: String|Object, required: true}
      },

      data(){
      
        return {
      
          showModal : false,
        }
      },

      computed : {

        ticketId() {

           return JSON.parse(this.data).ticket_id;
        },

         taskCount() {

           return JSON.parse(this.data).task_count;
        },

        ...mapGetters({ticketActions : 'getTicketActions'}),
      },

      methods : {

        onClose(){
          
            this.showModal = false;
      
            this.$store.dispatch('unsetValidationError');
        },

          reloadData() {

            window.eventHub.$emit('updateTimelineActions')
          },
      },

      components : {

         'task-create-modal' : require('./TaskModal'),
      }
  };

</script>

<style scoped>
  
  #task-action{
        margin-top: -2px !important;
        margin-right: 1px !important;
  }

  .smaller { font-size: smaller !important; }
</style>
