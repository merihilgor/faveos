<style scoped>
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  -webkit-border-radius: 0; /* Safari 3-4, iOS 1-3.2, Android 1.6- */
  -moz-border-radius: 0; /* Firefox 1-3.6 */
  border-radius: 0; /* Opera 10.5, IE 9, Safari 5, Chrome, Firefox 4, iOS 4, Android 2.1+ */
}
.bootstrap-switch {
  border-radius: 0;
}
.bootstrap-switch .bootstrap-switch-handle-on {
  border-bottom-left-radius: 0px;
  border-top-left-radius: 0px;
}
.bootstrap-switch .bootstrap-switch-handle-off {
  border-bottom-right-radius: 0px !important;
  border-top-right-radius: 0px !important;
}
.select2-container--default
  .select2-selection--single
  .select2-selection__rendered {
  line-height: 32px;
}
.select2-container--default
  .select2-selection--single
  .select2-selection__arrow {
  height: 32px;
}
.select2-container .select2-selection--multiple,
.select2-container--default .select2-selection--single {
  height: 34px;
  border-radius: 0px !important;
  border: 1px solid #d2d6de !important;
  overflow-y: auto;
  overflow-x: hidden;
}
.select2-container {
  width: 100% !important;
}
.radio-inline {
  padding-top: 0px;
}
.enab {
  color: #333 !important;
  background-color: #e6e6e6 !important;
  border: 2px solid #3c8dbc;
}
input[type="file"]:focus,
input[type="radio"]:focus,
input[type="checkbox"]:focus {
  outline: none;
  outline-offset: -2px;
}
.select2-container--default .select2-results__option .select2-results__option {
  padding-left: 6px;
}
.select2-container--default
  .select2-selection--single
  .select2-selection__arrow {
  position: fixed;
}
.row {
  margin-left: 0px;
}
.submit-button {
  border-top: 1px solid gainsboro;
  background-color: white;
  padding: 5px;
  margin-bottom: 15px;
  padding-top: 15px;
  margin-top: 20px;
  width: 98%;
}

</style>
<template>
    <div class="min-height-500">

      <custom-loader v-if="!hasDataPopulated || loading"></custom-loader>

      <div v-if="hasDataPopulated">

            <alert  componentName="workflow"></alert>


            <!-- HEADER MENU -->
            <header-menu :category="category" :obj="obj"></header-menu>
            <!-- HEADER MENU END -->

            <!-- ACTION PERFORM -->
            <action-perform v-if="category=='listener'" :category="category" :obj="obj"></action-perform>
            <!-- ACTION PERFORM END -->

            <!-- EVENT MENU LIST -->
            <event-menu v-if="category=='listener'" :obj="obj" :eventList="eventList"></event-menu>
            <!-- EVENT MENU LIST END -->

            <!-- RULE LIST  -->
            <rule-menu  :category="category" :obj="obj" :ruleList="ruleList" :customForm="ticketCustomFields" :editRuleValues="editRuleValues"></rule-menu>
            <!-- RULE LIST END -->


            <!-- ACTION MENU -->
            <action-menu :category="category" :obj="obj" :actionList="actionList" :customForm="ticketCustomFields" :editActionValues="editActionValues"></action-menu>
            <!-- ACTION MENU END -->


            <!-- INTERNAL NOTES -->
            <internal-notes :obj="obj"></internal-notes>
            <!-- INTERNAL NOTES END -->

            <button type="button" class="btn btn-primary" id="submitForm" ref="submitForm"  @click="validateForm()"><i class="fa fa-save">&nbsp;</i>{{lang('submit')}}</button>
      </div>
    </div>


</template>
<script>
import { validateWorkflowListner } from "helpers/validator/workflowlistnerRules.js";
import {
  boolean,
  getValueFromNestedArray,
  extractOnlyId,
  getIdFromUrl
} from "helpers/extraLogics.js";
import { errorHandler, successHandler } from "helpers/responseHandler";
import { assignLabel } from "helpers/assignCustomFieldLabel";

export default {
  name: "workflow",

  props: {
    /**
     * variable used to define ,form is based on which category
     * ie - it would be either workflow or listner
     */
    category: { type: String, default: "" },

    /**
     * variable used to give the button name
     */
    buttonlabel: { type: String, default: "Submit" }
  },
  data() {
    return {
      /**
       * variable to store the  post data
       */
      submitData: {},

      loading: false,

      count: 0,

      /**
       * rulelist array
       */
      ruleList: [
        {
          id: null,
          field: "",
          relation: "equal",
          category: "ticket",
          value: "",
          rules: []
        }
      ],

      /**
       * action list array
       */
      actionList: [
        {
          id: null,
          field: "",
          value: "",
          actions: [],
          action_email: {
            id: null,
            subject: "",
            body: "",
            user_ids: []
          }
        }
      ],

      /**
       * event list array
       */
      eventList: [
        {
          id: null,
          field: "",
          value: "",
          from: 0,
          to: 0
        }
      ],

      /**
       * workflow / listner object
       */
      obj: {
        id: null,
        name: "",
        status: true,
        description: "",
        target: "",
        triggered_by: "agent",
        matcher: "any",
        internal_notes: ""
      },

      /**
       * variable to store the customields
       */
      ticketCustomFields: [],

      /**
       *state for storing the edit data
       */
      editData: {},

      editActionValues: [],
      editRuleValues: [],

      /**
       *editform is used to check if the edit api has been called or not
       */
      editformcall: false,

      editId : 0,

      hasDataPopulated: false,
    };
  },
  components: {
    "header-menu": require("./HeaderMenu.vue"),
    "action-perform": require("./ActionPerformed.vue"),
    "event-menu": require("./EventMenu.vue"),
    "rule-menu": require("./RuleMenu.vue"),
    "action-menu": require("./ActionMenu.vue"),
    "internal-notes": require("./InternalNotes.vue"),
    alert: require("components/MiniComponent/Alert"),
    "custom-loader": require("components/MiniComponent/Loader")
  },
  created() {

    // setting form mode to workflow-lisetner
    this.$store.dispatch('setFormMode', 'workflow-listener');

    // pass the id from the url
    this.editId = getIdFromUrl(window.location.pathname);

    this.getFormData();
  },

  watch: {
    editformcall(newvalue) {
      this.editformcall = newvalue;
    }
  },

  methods: {
    isValid() {
      const { errors, isValid } = validateWorkflowListner(this.$data);
      if (!isValid) {
        return false;
      }
      return true;
    },

    validateForm() {
      this.childrenCount = 0;
      this.validationArray = [];
      this.isValid();

      // console.log(this.$children, "childerend");
      $.each(this.$children, this.nestedChildValidation);
    },
    nestedChildValidation(key, value) {
      this.childrenCount++;
      value.$validator.validateAll().then(result => {
        if (result) {
          this.validationArray.push(result);
        }
      });
      if (value.$children.length != 0) {
        $.each(value.$children, this.nestedChildValidation);
      } else {
        setTimeout(() => {
          if (this.validationArray.length == this.childrenCount) {
            this.submitForm();
          } else {
            let x = document.getElementsByClassName("field-danger")[0];
            if(x !== undefined){
              x.scrollIntoView({behavior: "smooth"});
            }
          }
        }, 50);
      }
    },

    /**
     * The respective function helps in setting the data format which is reuired by the backend,
     * @param {Array} parameter
     * @param {String} type
     * @return {void}
     *
     */
    nodefunction(parameter, type) {
      return parameter.filter( param => {
        if(!boolean(param.field)){ // if field is empty, we don't append that to array
          return false;
        }
        return param;

      }).map(param => {
        if (param[type].length > 0) {
          return {
            field: param.field,
            value: param.value,
            category: param.category ? param.category : null,
            relation: param.relation ? param.relation : null,
            id: param.id ? param.id : null,
            [type]: _.flattenDepth(
              param[type].map(param1 => {
                if (param1 != undefined && Array.isArray(param1.node)) {
                  return param1.node.map(res => {
                    return {
                      field: res.unique,
                      value: res.value,
                      // giving this same as parent relation
                      relation: param.relation,
                      id: this.editformcall == true ? getValueFromNestedArray(param1.editdata,res.unique,"id"): null,
                      [type]: _.compact(
                        _.flattenDepth(this.filternode(res.options, type, param1.editdata, param.relation),1)
                      )
                    };
                  });
                }
              }),
              1
            )
          };
        } else {
          if (type == "actions" && boolean(param.action_email)) {
            if (param.action_email.hasOwnProperty("users")) {
              param.action_email.user_ids = extractOnlyId(
                param.action_email.users
              );
            } else {
              param.action_email.user_ids = extractOnlyId(
                param.action_email.user_ids
              );
            }
          }
          return param;
        }
      });
    },

    /**
     * The Filternode function helps to check if there are any nested nodes in custom fields,
     * if there are nestednode then the given function would transform the data that in the
     * backend format as required
     * @param {Array} data;
     * @param {String} type;
     * @param {Array} y; // these are editActionvalue || editRulevalue
     * @param {String} relation   relation of the parent ('equal','not_equal' etc)
     */
    filternode(data, type, y, relation) {
      if (boolean(data) && data.length > 0) {
        return data.map(value => {
          if (value.nodes.length > 0) {
            return value.nodes.map(res => {
              return {
                field: res.unique,
                value: res.value,
                relation : relation,
                id:
                  this.editformcall === true
                    ? getValueFromNestedArray(y, res.unique, "id")
                    : null,
                [type]: _.compact(
                  _.flattenDepth(this.filternode(res.options, type), 1)
                )
              };
            });
          }
        });
      } else {
        return data;
      }
    },

    async getFormData(){
      this.loading = true;
      this.hasDataPopulated = false;

      // call edit form API only when userForm and ticketForm is resolved
      Promise.all([
        this.getTicketFormData(),
        this.getUserFormData(),

        // should only make API calls only in listener mode
        this.getDependency("priorities"),
        this.getDependency("types"),
        this.getDependency("statuses"),
        this.getDependency("help-topics"),
        this.getDependency("agents"),
        this.getDependency("departments"),

      ]).then(res => {
        this.loading = false;
        this.hasDataPopulated = true;
        if(this.editId){
          this.editform(this.editId);
        }
      });
    },

    getTicketFormData() {
      return axios.get("api/get-form-data", {params:{category:'ticket', mode:'workflow-listener'}}).then(res => {
        assignLabel(res.data.data.form_fields, this.currentLanguage, 'agent');
        this.$store.dispatch('setTicketFormFields', res.data.data.form_fields)
      }).catch(error=>{
        errorHandler(error, 'workflow');
      });
    },

    getUserFormData() {
      return axios.get("api/get-form-data", {params:{category:'user', mode:'workflow-listener'}}).then(res => {
        assignLabel(res.data.data.form_fields, this.currentLanguage, 'agent');
        this.$store.dispatch('setUserFormFields', res.data.data.form_fields)
      }).catch(error=>{
        errorHandler(error, 'workflow');
      });
    },

    /**
     * NOTE : this method is only required for listeners.
     * REASON : current code is written in a way that it accept All dependency in one
     * only in case of events in listeners. It will require `additional` jugaad to make it
     * work with existing components because child fields of helptopic and department should
     * not come there. Plus, Any option should come in events.
     *
     * Though it is inefficient approach but keeping the code debuggable is the priority
     * at this point.
     * @param  {String} dependency
     * @return {Promise}
     */
    getDependency(dependency){
      if(this.category == 'workflow'){
        // returning an empty, as these API calls are not required in workflow mode
        return;
      }

      return axios.get("api/dependency/" + dependency, {params: {limit: 1000}}).then(res => {
        let key = (dependency == 'help-topics') ? 'help_topics' : dependency;
        this.$store.dispatch('setDependency', {key:key, value: res.data.data[key]})
      }).catch(error=>{
        errorHandler(error, 'workflow');
      });
    },

    /**
     * Submit method of the form
     */
    submitForm() {
      this.count++;
      if (this.count == 1) {
        this.submitData = Object.assign({}, this.obj);
        this.submitData["rules"] = this.nodefunction(this.ruleList, "rules");
        this.submitData["actions"] = this.nodefunction(this.actionList,"actions");

        // submitting only those events which are non-empty
        this.submitData["events"] = this.eventList ? this.eventList.filter(event => event.field != "") : []

        this.loading = true;
        axios.post("api/post-enforcer", {type: this.category, data: this.submitData})
          .then(res => {
            successHandler(res, "workflow");
            this.redirect('/'+this.category);
          })
          .catch(err => {
            errorHandler(err, "workflow");
          }).finally(res => {
            this.loading = false;
            this.count = 0;
          });
      }
    },

    editform(id) {
      this.ruleList = [];

      this.editformcall = true;

      this.loading = true;
      this.hasDataPopulated = false;

      axios
        .get("api/get-enforcer/" + this.category + "/" + id)
        .then(res => {
          this.editData = res.data.data[this.category];
          this.obj.name = this.editData.name;
          // this.obj.description = this.editData.description;
          this.obj.status = boolean(this.editData.status);
          this.obj.matcher = this.editData.matcher;
          this.obj.triggered_by = this.editData.triggered_by;
          this.obj.internal_notes = this.editData.internal_notes;
          this.obj.id = this.editData.id;
          this.editRuleValues = _.cloneDeep(this.editData.rules);
          this.editActionValues = _.cloneDeep(this.editData.actions);
          this.actionList = this.editData.actions;
          this.ruleList = this.editData.rules;
          this.eventList = this.editData.events;

          this.loading = false;
          this.hasDataPopulated = true;

        })
        .catch(err => {
          this.loading = false;
          this.hasDataPopulated = true;
        });
    }
  }
};
</script>
