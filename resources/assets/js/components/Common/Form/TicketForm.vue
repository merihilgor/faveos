<template>
    <div id="ticket-form">
      <faveo-box :title="panel !== 'user' ? lang('submit_a_ticket') : ''">
        <create-form
          :buttonClass="buttonClass" :buttonStyle="buttonStyle"
          :color="color"
          :person="panel"
          :category="'ticket'"
          :editDataApiEndpoint="editDataApiEndpoint"
          :submitApiEndpoint="submitApiEndpoint"
        ></create-form>
      </faveo-box>
  </div>
</template>
<script>

import FaveoBox from 'components/MiniComponent/FaveoBox';
import { getIdFromUrl } from 'helpers/extraLogics';

export default {

  name: 'ticket-form',

  components : {
    'create-form' : require('components/Common/Form/CreateForm'),
    'faveo-box' : FaveoBox
  },

  props : {

    /**
     * Which panel form is viewed from
     * possible values `user` or `agent`
     */
    panel : { type: String, default: 'agent'},

    /**
     * Id of the ticket
     */
    ticketId : { type:Number|String, default: null},

    buttonStyle : { type : Object, default : ()=>{}},

    buttonClass : { type : String, default : 'btn btn-primary'},

    color : { type : String, default : '#1d78ff'},
  },

  data(){
    return {
      editDataApiEndpoint : null,

      submitApiEndpoint: null,
    }
  },

  beforeMount(){

    if(this.panel == 'user') {
      this.submitApiEndpoint = '/postedform';
    } else {
      this.submitApiEndpoint = '/newticket/post';
    }
  },

  created(){

    window.eventHub.$on('batchRequesterStatus', (value) => {
      if(value){
        this.submitApiEndpoint = '/newbatchticket/post';
      } else {
        this.submitApiEndpoint = '/newticket/post';
      }
    });

    window.eventHub.$on('ticketFormSubmitted',()=>{
      // after ticket form submitted, sidebar should be refreshed
      window.eventHub.$emit('update-sidebar');
    });

  },
}
</script>