<template>
    <div id="recur-ticket-form">
      <faveo-box :title="lang(mode)">
        <create-form
          :person="'agent'"
          :category="'ticket'"
          :usedby="'recur'"
          :editDataApiEndpoint="editDataApiEndpoint"
          submitApiEndpoint="/api/ticket/recur"
        ></create-form>
      </faveo-box>
  </div>
</template>

<script>

import FaveoBox from 'components/MiniComponent/FaveoBox';
import { getIdFromUrl } from 'helpers/extraLogics';

export default {

  name: 'recur-ticket',

  components : {
    'create-form' : require('components/Common/Form/CreateForm'),
    'faveo-box' : FaveoBox
  },

  data(){
    return {
      editDataApiEndpoint : null,
      recurId : '',
    }
  },

  beforeMount(){
    if(this.mode == 'edit'){
      this.recurId = getIdFromUrl(this.currentPath());
      this.editDataApiEndpoint = "/api/ticket/recur/" + this.recurId;
    }

    this.$store.dispatch('setFormMode', 'recur-ticket');
  },

  created(){

    // redirect as soon as form is submitted
    window.eventHub.$on('ticketFormSubmitted', () => {
      if(this.mode == 'create'){

        window.location.pathname.split('/').includes('agent') ? this.redirect('/agent/recur/list') : this.redirect('/recur/list');
      }
    })

  },

  computed : {

    mode(){
      if(this.currentPath().indexOf("edit") >= 0){
        return 'edit';
      }
      return 'create';
    },

  },
}
</script>
