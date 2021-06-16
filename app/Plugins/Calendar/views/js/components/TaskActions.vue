<template>

  <div class="pull-right">

    <loader v-if="loading" :animation-duration="4000" color="#1d78ff" :size="60"/>

    <div class="btn-group">

      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

        <i class="fa fa-cog"> </i> {{lang('actions')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu" id="more_actions">

        <li>

          <a v-if="!notEdit" :href="basePath()+'/tasks/task/'+task.id+'/edit'">
           <i class="fa fa-edit" style="color: #0d6aad"></i> {{lang('edit')}}
          </a>
        </li>

        <li>

          <a href="javascript:;" @click="showDeleteModal = true"><i class="fa fa-trash" style="color: red"></i>{{lang('delete')}}</a>
        </li>

      </ul>
    </div>

     <div class="btn-group">

      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

        <i class="fa fa-exchange"> </i> {{lang('status')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu" id="more_actions">

        <li>
          <a  href="#" :class="task.status === 'Open' ? 'disabledLink' : ''"
             @click.prevent="changeTaskStatus('open')"><i class="fa fa-clock-o" style="color: limegreen"></i>{{lang('open')}}
          </a>

          <a  href="#" :class="task.status === 'Closed' ? 'disabledLink' : ''"
             @click.prevent="changeTaskStatus('close')"><i class="fa fa-minus-circle" style="color: orangered"></i>{{lang('closed')}}
          </a>

          <a  href="#" :class="task.status === 'In-progress' ? 'disabledLink' : ''"
             @click.prevent="changeTaskStatus('inprogress')"><i class="fa fa-exclamation-triangle" style="color: orange"></i>{{lang('inprogress')}}
          </a>

        </li>
      </ul>
    </div>


     <transition name="modal">

      <delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
          alertComponentName="tasks-view" :deleteUrl="'/tasks/task/' + task.id"
          redirectUrl="/tasks/task">

      </delete-modal>

		</transition> 

  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import {errorHandler, successHandler} from 'helpers/responseHandler'

  import axios from 'axios'

  export default {

    props : {

      task : { type : Object, default : ()=> {}},
      notEdit: {type: Boolean,default: false}
    },

    data() {

      return {

        showDeleteModal : false,

        showTaskStatus : false,

        actions : '',

        loading: false
      }
    },


    methods : {

      subString(value,length = 15){

        return getSubStringValue(value,length)
      },


      onClose(){

        this.showDeleteModal = false;

        this.showTaskStatus = false;

      },

      changeTaskStatus(status) {
          this.loading = true;
          axios.get('/tasks/change-task/'+this.task.id+'/'+status)
          .then((res) => {
            this.loading = false;
              successHandler(res,'taskDetails');
              setTimeout(()=>location.reload(),1350);
          })
          .catch((err)=> {
            this.loading = false;
              errorHandler(err,"taskDetails");
          })

      }
    },

    components : {

      'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),
      'loader' : require('components/MiniComponent/Loader')

    }
  };
</script>

<style scoped>

  #more_actions {
    right: 0 !important;
    left: unset !important;
  }

  .wrapper {
    cursor: not-allowed !important;
  }

  a.disabledLink {
      pointer-events: none !important;
  }
</style>
