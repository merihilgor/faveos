<style scoped>
.trash-placeement {
  position: relative;
  top: 5px;
}

.non-form-field {
  margin-top: 8px!important;
  margin-top: 8px!important;
}

</style>
<template>
	<div>
      <!-- MODAL -->
      <modal v-if="deletePopup" :showModal="deletePopup" :onClose="onCloseModal">
        <!-- SLOT TITLE -->
        <div slot="title">
          <div class="container">
            <div class="row">
              <div class="col-sm-12">
                <h4 class="text-left">{{lang('are_you_sure_you_want_to_delete')}}</h4>
              </div>
            </div>
          </div>
        </div>
        <!-- SLOT TITLE END -->

        <!-- SLOT CONTROL -->
        <div slot="controls">
             <button type="button" :id="'delete-action-confirm-button-'+index" @click = "onSubmitDelete()" class="btn btn-success"><i class="fa fa-save" aria-hidden="true"></i> Submit</button>
        </div>
        <!-- SLOT CONTROLEND -->
      </modal>
      <!-- MODAL END -->
      <div class="pull-left margin-top-8" :title="'Delete Action'">
        <i  :id="'delete-action-button-'+index" class="fa fa-trash faveo-trash trash-placeement " @click="deleteAction(action.id,index)">
        </i>
      </div>
      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 margin-top-8">
        <!-- SELECT 2 LIST  -->
        <select2-list :listMenu1='[]' :listMenu2="assignAction" :listMenu3="mailAction" :selectName="'select_action'"
          :field='action' :fieldChange="fieldChange" :clasName="'action_menu'" :index='index' :customForm="actionList"
          :fetchValues="fetchValues" :disabledFields="disabledFields"></select2-list>
        <!-- SELECT 2 LIST END -->
      </div>


        <!-- custom ticket fields -->
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
              <!-- vertical fields-->
            <label-with-formfield v-if="!showMail && refreshLabelWithFormfield"  formType="vertical" :displayLabel="false" :customForm="selectAction" :person="'agent'" :category="'ticket'" :fetchValues="fetchValues"></label-with-formfield>
        </div>
        <!-- send email to requester/agent -->
        <send-mail :action='action'  :index='index' v-if="showMail"></send-mail>
    </div>
</template>
<script>
import { getValueFromNestedArray } from "helpers/extraLogics.js";
import { errorHandler, successHandler } from "helpers/responseHandler";
import { setWorkFlowValue } from "helpers/workflowlistner.js";
import {mapGetters} from 'vuex';
import axios from 'axios';
import _ from 'lodash-core';
import Modal from "../../Common/Modal.vue";

export default {
  props: [
    "action",
    "category",
    "index",
    "customForm",
    "editActionValues",
    "list"
  ],

  created: function() {},
  /**
   * method helps to call the custom field
   * @param {String } ticket
   * @returns {void}
   */
  mounted() {
    this.getCustomFieldsForms("ticket");
  },
  data() {
    return {


      /**
       * variable to display the select tag
       */
      actionValue: false,

      /**
       * variable to display the show mail component
       */
      showMail: false,

      /**
       * variable for storing the dependency api results
       */
      actionList: [],

      /**
       * new array of custom field which is passed to labelwithformfields, created when unique value is equal to field that object is pushed to new customfield array of selectAction
       */
      selectAction: [],

      deletePopup: false,

      actionIdValue: "",

      disabledFields : [],

      teamList : [],

      refreshLabelWithFormfield: true
    };
  },
  components: {
    "select2-list": require("./Select2ListMenu.vue"),
    "send-mail": require("./SendMail.vue"),
    modal: Modal,
  },
  watch: {
    /**
     * watcher on the selectAction array if the vlues get mutated ot changes the new value is retured
     */
    selectAction(newValue) {
      return newValue;
    },
  },

  computed: {
    ...mapGetters(['getTicketFormFields']),

    /**
     * Assign Action State with key value pairs
     */
    assignAction(){
      let actions = [];

      // if mode is workflow, add reject ticket
      if(this.category == 'workflow'){
        actions.push({ name: "Reject Ticket", value: "reject_ticket", unique: "reject_ticket" });
      }
      return actions;
    },

    /**
     * Mail Action Stte wit key value pairs
     */
    mailAction(){
      return [
        { name: "send_email_to_requester", value: "mail_requester", unique:"mail_requester" },
        { name: "send_email_to_agent", value: "mail_agent", unique: "mail_agent" }
      ];
    },

  },

  methods: {

    /**
     * fieldChanges function is helpfull for selecting the field show is the dropdown,
     * as the field is changed the respective state are assigned as shown below
     * @param {String} field
     */
    fieldChange(field) {
      this.actionValue = false;
      this.showMail = false;
      this.action.field = field;
      this.action.value = "";
      this.action.actions = [];
      this.switchField(field);

      /**
       * This is a JUGAAD
       * Issue:- For condition and actions select first priority showing proper now reselect location then drop down is showing priority list
       * Jugaad:- Toggling <label-with-formfield> with timer
       * Offer:- If someone have better solution(Not Jugaad), I'll give him a chocolate
       */
      this.refreshLabelWithFormfield = false;
      setTimeout(() => {
        this.refreshLabelWithFormfield = true;
      }, 0);
    },

    /**
     * switch method is used to call either the its dependencey api or to showthe customfields,
     * for eg if field is equal to status_id then the dependency api is called and action value is
     * set to true
     * @param {String} field
     * @returns {void }
     */

    switchField(field) {
      if (typeof this.action.value === "object" && this.action.value.constructor !== Array && this.action.value !== undefined) {
        this.action.value = this.action.value.id;
      }

      switch (field) {

        case "mail_requester":
          this.showMail = true;
          if (this.editActionValues.length > 0) {
            this.editActionValues.map((res, index) => {
              if (res.field == "mail_requester" && this.action[index] != undefined) {
                this.action[index].action_email = res.action_email;
              }
            });
          }
          break;

        case "mail_agent":
          this.showMail = true;
          if (this.editActionValues.length > 0) {
            this.editActionValues.map((res, index) => {
              if (
                res.field == "mail_agent" &&
                this.action[index] != undefined &&
                this.action[index].action_email.user_ids != undefined
              ) {
                this.action[index].action_email.user_ids = res.users;
              }
            });
          }
        default:
          this.selectAction = [];
          for (let i in this.actionList) {
            if (this.actionList[i].unique == field) {
              this.selectAction.push(this.actionList[i]);
            }
          }
          break;
      }

      this.updateDisabledFields();
    },

    /**
     * updates the array for diabling selected field
     */
    updateDisabledFields(){
      this.disabledFields = this.list.map(action => {
        return action.field
      });
    },

    /**
     * getDependancy method helps to get the result of the dependancy passed to it,
     * @param {String} dependancy,
     * @returns {void}
     */
    getTeams() {
      axios.get("api/dependency/teams").then(res => {
        this.teamList = res.data.data.teams;
      });
    },

    deleteAction(idvalue, index) {
      this.deletePopup = true;
      if (idvalue != "" && idvalue != null) {
        this.actionIdValue = idvalue;
      }
    },

    onSubmitDelete() {
      if (this.actionIdValue) {
        axios
          .delete("api/delete-enforcer/action/" + this.actionIdValue)
          .then(res => {
            this.deletePopup = false;
            this.list.splice(this.index, 1);
            successHandler(res, "workflow");
          })
          .catch(value => {
            this.deletePopup = false;
            errorHandler(value, "workflow");
          });
      } else {
        this.list.splice(this.index, 1);
      }
    },

    onCloseModal() {
      this.deletePopup = false;
    },

    /**
     * fetchValues is a method which is a method passed to the custom fields
     * ie  to (Labelwithformfield)- component and when the customform array chages it fetched the new
     * array and helps to do the manipulation on it
     */
    fetchValues(data) {
      return setWorkFlowValue(data, this.editActionValues, this.action, "actions", this.index);
    },

    /**
     * getCustomFieldsForms is api method to call custom form fields
     * @type { String } type ie type is equal ticket
     * @return { void }
     */
    getCustomFieldsForms() {

      this.actionList = this.formatFieldsForActionList(_.cloneDeep(this.getTicketFormFields));

      // so that selectRule can be populate
      this.switchField(this.action.field);

      // so that value can be binded
      this.fetchValues(this.selectAction);
    },

    /**
     * Removes fields that are not required in action list
     * @param  {Array} formFields
     * @return {Array} formatted form fields
     */
    formatFieldsForActionList(formFields){

      formFields = this.addAdditionalActions(formFields)

      let fieldsToBeRemoved = ['description','subject'];
      return formFields.filter(formField => {
        if(fieldsToBeRemoved.includes(formField.unique)){
          return false;
        }
        return formField;
      });
    },

    addAdditionalActions(formFields){

      // add team and approval workflow into this list
      let teamForm = {default:1, title: 'Team', display_for_agent: true, required_for_agent: true, label:'Assigned Team', value : '', type:'select', unique:'team_id', options:[], api_info: 'url:=/api/dependency/teams?paginate=true;;'}


      formFields.push(teamForm);

      // // not required in case of listeners
      if (this.category == 'workflow') {
          let ticketNumberPrefix = {default:1, title: 'Ticket Number Prefix', display_for_agent: true, required_for_agent: true,
              label:this.trans('ticket_number_prefix'), value : '', type:'text', unique:'ticket_number_prefix',
              pattern: /^[A-Za-z0-9]{3,4}$/i, labels_for_validation: this.trans('only_four_characters_are_allowed_in_ticket_number_prefix'),
          }
          formFields.push(ticketNumberPrefix);
      }

      let approvalWorkflowForm = {default:1, title: 'Approval Workflow', display_for_agent: true, required_for_agent: true, label:'Apply approval workflow', value : '', type:'select', unique:'approval_workflow_id', options:[], api_info: 'url:=/api/dependency/approval-workflows?paginate=true;;'}

      formFields.push(approvalWorkflowForm);

      return formFields;
    },
  }
};
</script>
