<style scoped>
.u_tab {
  margin-left: -3px;
  margin-right: 0px;
}
.c_tab {
  margin-left: -3px;
}
.t_tab {
  margin-right: 0px;
}
.ticket_field {
  width: 100%;
}

.mrl8 {
  margin: 0px 8px;
}

.trash-placeement {
  position: relative;
  top: 5px;
}
.faveo-active-border {
  color: #333 !important;
  background-color: #e6e6e6 !important;
  border: 2px solid #3c8dbc;
}
.ticket-rule .form-group{
  margin-bottom : inherit;
}

</style>
<template>
	<div class="ticket-rule">
      <!-- MODAL -->
      <modal v-if="deletePopup" :showModal="deletePopup" :onClose="onCloseModal">
        <!-- SLOT TITLE -->
         <div slot="title">
           <h4>{{lang('delete')}}</h4>
         </div>
        <div slot="fields">
          <div class="container">
            <div class="row">
              <div class="col-sm-12">
                <span class="text-left">{{lang('are_you_sure_you_want_to_delete')}}</span>
              </div>
            </div>
          </div>
        </div>
        <!-- SLOT TITLE END -->

        <!-- SLOT CONTROL -->
        <div slot="controls">
           <button :id="'delete-rule-confirm-button-'+index" type="button" @click = "onSubmitDelete()" class="btn btn-success"><i class="fa fa-save" aria-hidden="true"></i> Submit</button>
        </div>
        <!-- SLOT CONTROLEND -->
      </modal>
      <!-- MODAL END -->
      <!-- TAB TO SELECT THE RESPECTIVE FIELD -->
          <div class=" pull-left margin-top-8" :title="'Delete' + ' ' + rule.category">
            <i  :id="'delete-rule-button-'+index" class="fa fa-trash faveo-trash trash-placeement" @click="deleteRule(rule.id,index)">
            </i>
          </div>
          <div class="col-lg-2 max-width-max-content margin-top-8">
            <!-- TICKET FIELD TAB -->
              <a href="javascript:void(0)"
                  :id="`rule-ticket-tab-`+ index"
                  class="btn btn-default  m-clear"
                  :class="{'faveo-active-border':ticket}" data-toggle="tooltip"
                  data-placement="top" title="Ticket Field"
                  @click="tabActive('ticket', index)">
                <i class="fa fa-file-text"></i>
              </a>
            <!-- TICKET FIELD TAB END -->

              <!-- USER REQUESTER TAB -->
              <a href="javascript:void(0)"
                  :id="`rule-user-tab-`+ index"
                  class="btn btn-default m-clear"
                  :class="{'faveo-active-border':user}"
                  data-toggle="tooltip"
                  data-placement="top" title="User Field"
                  @click="tabActive('user', index)">
                <i class="fa fa-user"></i>
              </a>
          </div>
      <!-- TAB TO SELECT THE RESPECTIVE FIELD END -->

      <!-- DISPLAY SELECT AS THE TAB SELECTION -->
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12  margin-top-8">
            <select2-list v-if="ruleList.length > 0" :listMenu1='[]' :listMenu2='[]' :listMenu3='[]' :selectName="'select_condition'"
              :field="rule" :clasName="'rule_menu'"
              :index="index" :fieldChange="fieldChange"  :fetchValues="fetchValues" :editvalue="editRuleValues" :customForm="ruleList" style="margin:0px !important">
            </select2-list>
          </div>
      <!-- DISPLAY SELECT AS THE TAB SELECTION END -->

      <!-- RULE EVENTS -->
        <!-- if rule relation exists, then only we show this tab -->
        <div v-if="rule.field">
            <!-- DISPLAY THE RULE Conditions -->
            <div class="col-lg-2 col-md-2 col-xs-12 col-sm-2 margin-top-8">
                <select id="rule-condition-container" class="m-clear"  :class="{'form-control':true,'faveo-field-danger':errors.has(name='Relation Field'+index) }" v-model="rule.relation" :name="'Relation Field'+index" v-validate="{required:true}">
                    <option v-for="(relation, relationIndex) in relationList"
                        :selected="relationIndex == 0 ? 'selected': false"
                        :value="relation.value">
                      {{lang(relation.name)}}
                    </option>
                </select>
            </div>
            <!-- DISPLAY THE RULE EVENTS END -->

            <!-- DISPLAY WHEN THE PECEFIC RULE HAS BEEN SELECTED -->
            <div v-if="rule.field" class="col-lg-5">

              <!-- CUSTOM FIELDS -->
              <div v-if="ticket && refreshLabelWithFormfield">
                  <label-with-formfield  :formType="'vertical'" :customForm="selectRule" :displayLabel="false" :person="'agent'" :category="'ticket'" :fetchValues="fetchValues"></label-with-formfield>
              </div>

              <div v-if="user && refreshLabelWithFormfield">
                  <label-with-formfield  :formType="'vertical'" :customForm="selectRule" :displayLabel="false" :person="'agent'" :category="'user'" :fetchValues="fetchValues"></label-with-formfield>
              </div>
              <!-- CUSTOM FIELDS END -->
            </div>

        </div>
      <!-- RULE EVENTS END -->
    </div>
</template>
<script>

import { getValueFromNestedArray, boolean, lang } from "helpers/extraLogics.js";
import { errorHandler, successHandler } from "helpers/responseHandler";
import { setWorkFlowValue } from "helpers/workflowlistner.js";
import { mapGetters } from 'vuex';
import Modal from "../../Common/Modal.vue";
import axios from 'axios';
import $ from 'jquery';
import _ from 'lodash-core';

export default {
  props: [

    /**
     * rule should be an object with one of the keys as category with value `ticket`,`user` or `organisation`
     */
    "rule",

    /**
     * Index of the action
     */
    "index",

    /**
     * Array of edited values recieved from the server (passed by parent)
     */
    "editRuleValues",

    /**
     * `workflow` or listener
     */
    "category",

    /**
     * List of all the rules
     * NOTE: this is not getting used other that splicing it. which cannot work as it is lke mutating a prop
     */
    "list"
  ],


  data() {
    return {
      /**
       * variable for storing the customfieldArray as per the click on the tab
       */
      ruleList: [],

      /**
       * variable to show ticketl
       */
      ticket: false,

      /**
       * variable to show user
       */
      user: false,

      /**
       * new array of custom field which is passed to labelwithformfields, created when unique value is equal to field that object is pushed to new customfield array of selectRule
       */
      selectRule: [],

      deletePopup: false,

      actionEditValues : this.editRuleValues,

      ruleIdValue: "",

      refreshLabelWithFormfield: true
    };
  },
  mounted() {
    if (boolean(this.rule.category)) {
      this.tabActive(this.rule.category);
    }
  },

  components: {
    "select2-list": require("./Select2ListMenu.vue"),
    modal: Modal,
  },

  computed: {
    ...mapGetters(['getTicketFormFields', 'getUserFormFields']),


    /**
     * Returns valid relations based on the form field type
     * @return {Array}
     */
    relationList(){
        try{
          let allConditions = [
            {name: 'is', value: 'equal'}, {name :'is_not', value: 'not_equal'},
            {name :'contains', value: 'contains'}, {name :'does_not_contains', value: 'dn_contains'},
            {name :'start_with', value: 'starts'}, {name :'end_with', value: 'ends'},
          ];

          if(this.selectRule.length == 1 && boolean(this.selectRule[0].type) &&
            this.selectRule[0].type != 'text' && this.selectRule[0].type != 'textarea'){

              // for tags and labels, grammer will change, since tags and labels are plurals. but at the value level,
              // it will remain same
              if(this.selectRule[0].title == "Tags"  || this.selectRule[0].title == "Labels"){
                  return [{name: 'are', value: 'equal'}, {name :'are_not', value: 'not_equal'},
                  {name :'contain', value: 'contains'}, {name :'do_not_contain', value: 'dn_contains'}];
              }

              return [{name: 'is', value: 'equal'}, {name :'is_not', value: 'not_equal'}];
          }

          return allConditions;

        } catch(error) {
          return allConditions;
        }
    },
  },

  methods: {

    /**
     * tabActive methods helps us to select the tab and return is customfieldsform data,
     * @param {String , Object} field , $event -(event Object)
     * @returns {void}
     */
    tabActive(category) {

      this.ticket = false;
      this.user = false;


      this[category] = true;

      // means if a tab is changing
      if(boolean(this.rule.category) && this.rule.category !== category){
        this.emptyValuesontabChange();
      }


      this.rule.category = category;
      this.getCustomFieldsForms(category);



      /**
       * this method helps us to empty the values and make the necessary values false when user is changes the ,
       * tabs during the edit api call
       */
      if (this.rule && this.rule.category && this.editRuleValues[this.index]) {
        if (this.rule.category !== this.editRuleValues[this.index].category) {
          this.emptyValuesontabChange();
        }
      }
    },

    emptyValuesontabChange() {
      // need to remove this index from ruleList too
      this.rule.value = "";
      this.rule.field = "";
      this.rule.relation = "equal";
      this.rule.rules = [];
    },

    /**
     * fieldChange function is helpful for selecting the field show is the dropdown,
     * as the field is changed the respective state are assigned as shown below
     * @param {String} field
     */
    fieldChange(field) {
      this.rule.field = field;
      this.rule.value = "";
      this.rule.relation = "equal";
      this.rule.rules = [];
      this.switchField(field);

      this.refreshLabelWithFormfield = false;
      setTimeout(() => {
        this.refreshLabelWithFormfield = true;
      }, 0);
    },

    switchField(field) {

      if (typeof this.rule.value === "object" && this.rule.value.constructor !== Array && this.rule.value !== undefined) {
        this.rule.value = this.rule.value.id;
      }

      if(boolean(field)) {
          this.selectRule = [];
          for (let i in this.ruleList) {
            if (this.ruleList[i].unique == field) {
              this.selectRule.push(this.ruleList[i]);
            }
          }
      }
    },

    deleteRule(idvalue, index) {
      this.deletePopup = true;
      if (boolean(idvalue)) {
        this.ruleIdValue = idvalue;
      }
    },

    onSubmitDelete() {

      if (this.ruleIdValue) {
        axios
          .delete("api/delete-enforcer/rule/" + this.ruleIdValue)
          .then(res => {
            this.onCloseModal();
            this.list.splice(this.index, 1);
            successHandler(res, "workflow");
          })
          .catch(error => {
            errorHandler(error, "workflow");
          });
      } else {

        // TODO: list is a prop. Should not mutate it but call a function in parent which deletes it
        this.list.splice(this.index, 1);

        this.onCloseModal();
      }
    },

    onCloseModal() {
      this.deletePopup = false;
    },

    /**
     * getCustomFieldsForms is api method to call custom form fields
     * @type { String } type ie type is equal ticket
     * @return { void }
     */
    getCustomFieldsForms(type) {

      if(type == 'ticket'){
        this.ruleList = _.cloneDeep(this.getTicketFormFields);
      }

      if(type == 'user'){
        this.ruleList = _.cloneDeep(this.getUserFormFields);
      }
      // so that selectRule can be populate
      this.switchField(this.rule.field);

      // so that value can be binded
      this.fetchValues(this.selectRule);
    },

    /**
     * fetchValues is a method which is a method passed to the custom fields
     * ie  to (Labelwithformfield)- component and when the customform array chages it fetched the new
     * array and helps to do the manipulation on it
     */
    fetchValues(data) {
      setWorkFlowValue(data, this.actionEditValues, this.rule, "rules", this.index);
    },
  }
};
</script>
