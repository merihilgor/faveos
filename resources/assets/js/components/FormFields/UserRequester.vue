<style scoped>
label {
  display: block;
}
.col-sm-2,
.col-sm-10 {
  padding-left: 0px;
  padding-right: 0px;
}
.fa-warning {
  color: red;
  font-size: 14px;
}

.input-group {
  width: 100%;
}

.input-group input{
  width: 101%;
}

</style>
<template>
      <div>
        <div class="input-group  has-feedback" :class="{'has-success':userValidated}">
            <input type="text" id="client-panel-requester" :name="node.title" :class="{'form-control': true, 'field-danger': errors.has(node.title) }"
              v-model="userName" @change="getRequester" placeholder="Enter Your Username or Email" v-validate="'required'" :disabled="isUserLoggedIn">
            <span v-if="userValidated" class="glyphicon glyphicon-ok form-control-feedback"></span>
            <!-- ADD NEW REQUESTER BUTTON -->
            <div class="input-group-btn">
              <add-new-requester v-if="newRequester" v-on:setRequester="setRequester" :person="person"></add-new-requester>
            </div>
        </div>

        <!--VALIDATION ERROR  -->
        <span v-if="errors.has(node.title)" class="help is-danger">
            {{ errors.first(node.title) }}
        </span>
        <!--VALIDATION ERROR END  -->
    </div>
</template>
<script>

import { boolean, lang } from "helpers/extraLogics";
import { mapGetters } from "vuex";
import axios from "axios";

export default {
  props: ["node", "person"],
  data() {
    return {
      userName: "",
      newRequester: false, // variable for the new reuester user
      existRequester: false, // to check the the requester already exist or not
      isUserLoggedIn: false,
    };
  },
  components: {
    "add-new-requester": require("./NewRequester.vue")
  },
  watch: {
    "node.value"(newvalue) {
      // if empty and user is not logged in
      if (newvalue == "" && !this.isUserLoggedIn) {
        this.userName = newvalue;
        this.existRequester = false;
        this.newRequester = false;
        this.$validator.reset();
      }

      // if empty and user is logged in
      if(newvalue == "" && this.isUserLoggedIn){
        this.$emit("assignToModel", "value", this.userName);
      }
    },

    userData(newValue) {
      this.updateUsername(newValue);
    },

    newRequester(newvalue){
      if(newvalue){
        this.existRequester = false;
        this.errors.add(this.node.title, lang('requester_does_not_exist_create_new'));
      } else {
        this.$validator.reset();
      }
    }
  },

  mounted(){
    this.updateUsername(this.userData);

    this.urlAutoFillHandling();
  },

  computed: {
    ...mapGetters({userData: 'getUserData'}),

    /**
     * If user is validated
     * @return {Boolean}
     */
    userValidated(){
      if(this.userName == ""){
        this.existRequester = false;
        return false;
      }

      // userValidated is used for showing that user has been validaed. When user is logged in
      // it is supposed to be false, so if `isUserLoggedIn` is false then only it can be true
      // as extra ticket mark is not required
      return (this.existRequester && !this.isUserLoggedIn);
    }
  },
  methods: {

    updateUsername(userObject){
      if(userObject.hasOwnProperty('user_data') && userObject.user_data.hasOwnProperty('user_name')){
        this.userName = userObject.user_data.user_name;
        this.existRequester = true;
        this.isUserLoggedIn = true;
        this.$emit("assignToModel", "value", this.userName);
      }
    },

    /**
     * Requestor method to check if the requestor alreay exists or not
     * @param {String}
     * @returns {void}
     */
    getRequester() {
      axios.get("ticket/form/requester", {params : {term: this.userName, type: 'client'}}).then(res => {
          if(res.data.hasOwnProperty('user_name')){
            this.newRequester = false;
            this.existRequester = true;
            this.$emit("assignToModel", "value", res.data.user_name);
          } else {
            this.newRequester = true;
            this.existRequester = false;
          }
        });
    },

    setRequester(requester) {
      if(requester.hasOwnProperty('user_name')){
        this.userName = requester.user_name;
        setTimeout(()=>{
          this.newRequester = false;
          this.existRequester = true;
        }, 1000)
        this.$emit("assignToModel", "value", this.userName);
      }
    },

    /**
     * Handles url autofilling
     * @return {Void}
     */
    urlAutoFillHandling(){
      let userName = this.getRequesterFromUrl();
      if(userName && !this.userName){
        this.userName  = userName;
        this.getRequester();
      }
    },

    /**
     * Gets requester out of url
     * @return {String|null}
     */
    getRequesterFromUrl(){
      let url = new URL(window.location.href);
      let urlkey = this.node.unique.replace('_id', "");
      return url.searchParams.get(urlkey);
    },
  }
};
</script>

<style>

.input-group-btn{
  font-size: inherit;
}
.modal-dialog{
  width : 800px !important;
}

</style>
