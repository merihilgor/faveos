<template>

  <div>

    <modal v-if="showModal" :showModal="showModal" :onClose="onClose">

      <div v-if="showAlert" slot="title" id="alert_top">

        <alert componentName="problem-ticket"/>
        
      </div>
      
      <div slot="title"> <h4>{{lang('associate_tickets')}}</h4> </div>

      <div slot="fields" v-if="!loading">

        <div class="row">

          <div class="col-xs-12">
            
            <dynamic-select :label="lang('tickets')" :multiple="true" name="ticket_ids" :prePopulate="false"
              classname="col-xs-12" :apiEndpoint="'/service-desk/api/dependency/tickets_based_on_problem?supplements='+problemId" :value="ticket_ids" :onChange="onChange" :required="true">

            </dynamic-select>
          </div>
        </div>
      </div>

      <div v-if="loading" class="row" slot="fields" >

        <loader :animation-duration="4000" :size="60"/>
      </div>

      <div slot="controls">

        <button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="ticket_ids.length > 0 ? false : true">

          <i class="fa fa-paperclip"></i> {{lang('attach')}}</button>
      </div>
    </modal>
  </div>
</template>

<script type="text/javascript">

  import {errorHandler, successHandler} from 'helpers/responseHandler'

  import axios from "axios"

  export default {

    name : 'problem-ticket-associate',

    description : 'Problem ticket associate Modal component',

    props:{

      showModal:{type:Boolean,default:false},

      onClose:{type: Function, default : ()=>{}},

      problemId : { type : String | Number, default : ''},

      associate : { type : String , default : ''},
    },

    data(){

      return {

        ticket_ids : [],

        loading : false,

        showAlert : false,
      }
    },

    methods : {

      onChange(value, name) {

        this[name] = value;
      },

      onSubmit() {

        this.loading = true 

        const data = {};

        var ticketIds = [];

        for(var i in this.ticket_ids){

          ticketIds.push(this.ticket_ids[i].id)
        }

        data['ticket_ids'] = ticketIds;

        axios.post('/service-desk/api/problem-attach-ticket/'+this.problemId,data).then(res => {

          this.loading = false
          
          this.ticket_ids = [];
          
          this.onClose();

          window.eventHub.$emit('updateProblemAssociates');

          successHandler(res,'problem-view')
            
        }).catch(err => {
            
          this.loading = false;

          this.showAlert = true;
            
          errorHandler(err,'problem-ticket')
        });
      },
    },

    components:{

      'modal':require('components/Common/Modal.vue'),

      'alert' : require('components/MiniComponent/Alert'),

      "dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    }
  };
</script>

<style type="text/css">
  
  #alert_top{
    margin-top:20px
  }

  .body-height{
    max-height: 200px !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
  }
</style>