<template>
    <div id="organisation-form">
      <faveo-box :title="lang(mode)">
        <create-form
          person='agent'
          category='organisation'
          usedby='agent-panel'
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

  name: 'organisation-form',

  components : {
    'create-form' : require('components/Common/Form/CreateForm'),
    'faveo-box' : FaveoBox
  },

  data(){
    return {
      editDataApiEndpoint : null,
      organisationId : '',
      submitApiEndpoint: null,
    }
  },

  beforeMount(){
    if(this.mode == 'edit'){
      this.organisationId = getIdFromUrl(this.currentPath());
      this.editDataApiEndpoint = "/organisation/edit/api/" + this.organisationId;
      this.submitApiEndpoint = "/organisation/update/api/" + this.organisationId;
    } else {
      this.submitApiEndpoint = "/organisation/create/api/";
    }
  },

  created(){
    // redirect as soon as form is submitted
    window.eventHub.$on('organisationFormSubmitted', () => {
      if(this.mode == 'create'){
        this.redirect('/organizations');
      }
    });
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

<style scoped>
  #organisation-view-button {
    margin : 0px 18px 5px 18px;
    font-size: 14px;
    font-weight:400px;
  }
</style>
