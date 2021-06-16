<template>
  <faveo-box :title="lang(title)">
    <create-form
      :buttonClass="buttonClass" :buttonStyle="buttonStyle"
      :color="color"
      :person="panel"
      :category="'asset'"
      :editDataApiEndpoint="editDataApiEndpoint"
      :submitApiEndpoint="submitApiEndpoint"
      :submit-button-icon-class="submitButtonIconClass"
      :submit-button-text="submitButtonText"
      :edit-form-id="editItemId"
    ></create-form>
  </faveo-box>
</template>
<script>
import FaveoBox from 'components/MiniComponent/FaveoBox';
import { getIdFromUrl } from 'helpers/extraLogics';
export default {
  name: 'asset-form',
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
      editItemId: null,
      title: 'create_new_asset',
      submitButtonIconClass: 'fa fa-save',
      submitButtonText: 'submit'
    }
  },
  beforeMount() {
    if(this.isEditPage(window.location.pathname)) {
      this.title = 'edit_asset';
      this.submitButtonIconClass = 'fa fa-refresh'
      this.submitButtonText = 'update'
      this.editDataApiEndpoint = '/service-desk/api/edit-asset/' + this.editItemId;
      this.submitApiEndpoint = '/service-desk/api/asset';
      this.$store.dispatch('setFormMode', 'edit-asset');
    } else {
      this.title = 'create_new_asset';
      this.submitButtonIconClass = 'fa fa-save'
      this.submitButtonText = 'submit'
      this.submitApiEndpoint = '/service-desk/api/asset';
      this.$store.dispatch('setFormMode', 'create-asset')
    }
  },
  created() {
    window.eventHub.$on('ticketFormSubmitted',()=>{
      // after ticket form submitted, sidebar should be refreshed
      window.eventHub.$emit('update-sidebar');
    });
  },
  methods: {
    isEditPage(path){
      if(path.indexOf("edit") >= 0) {
        this.editItemId = getIdFromUrl(path); // Getting agent id from url
          return true
        } else {
          return false;
        }
      }
    }
}
</script>