<template>
    <div id="user-form">
      <faveo-box :title="lang(mode)">
        <a v-if="mode == 'edit'" id="user-view-button"
            :href="viewLink"
            target="_blank"
            class="btn btn-primary pull-right btn-sm">
           <i class="fa fa-eye" style="color:white;"></i>&nbsp;
             {{lang('view')}}
        </a>
        <create-form
          :person="'agent'"
          :category="'user'"
          :usedby="'agent-panel'"
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

  name: 'user-form',

  components : {
    'create-form' : require('components/Common/Form/CreateForm'),
    'faveo-box' : FaveoBox
  },

  data(){
    return {
      editDataApiEndpoint : null,
      userId : '',
      submitApiEndpoint: null,
    }
  },

  beforeMount(){
    if(this.mode == 'edit'){
      this.userId = getIdFromUrl(this.currentPath());
      this.editDataApiEndpoint = "/user/edit/api/" + this.userId;
      this.submitApiEndpoint = "/user/update/api/" + this.userId;
    } else {
      this.submitApiEndpoint = "/user/create/api/";
    }
  },

  created(){
    // redirect as soon as form is submitted
    window.eventHub.$on('userFormSubmitted', () => {
      if(this.mode == 'create'){
        this.redirect('/user');
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

    viewLink(){
      return this.basePath() + '/user/' + this.userId;
    }
  },
}
</script>

<style scoped>
  #user-view-button {
    margin : 0px 18px 5px 18px;
    font-size: 14px;
    font-weight:400px;
  }
</style>
