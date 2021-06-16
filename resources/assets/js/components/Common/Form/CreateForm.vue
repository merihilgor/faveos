 <style>
#custom-form-submit-button {
  background-color: white;
  padding: 16px 16px 0px 16px;
  text-align: right;
  margin-bottom: 15px;
  padding-top: 15px;
}

.client-loader .center-of-page {
  left: 12% !important;
}
</style>
 <template>
    <div  class="min-height-300">
          <!-- ALERT COMPONENT -->
          <div class="col-xs-12">

            <alert :componentName="'createTicket'+category"/>
          </div>
          <!-- ALERT COMPONENT END -->

          <div :class="{'client-loader': person == 'user'}">
            <custom-loader v-if="!hasDataPopulated || loading" :color="color"></custom-loader>
          </div>

          <div v-if="hasDataPopulated">

            <!-- TIME PERIOD -->
            <time-period :recur="recur" v-if="usedby=='recur'"></time-period>
            <!-- TIME PERIOD END -->

            <div class="top-bottom">
              <!-- LOADER -->
              <!-- LOADER END -->

              <!-- LABEL_WITH_FORM_FIELDS -->
               <label-with-formfield v-if="formArray.length!=0" :formType="'horizantal'" :customForm="formArray" :person="person" :category="category" :usedby="usedby"></label-with-formfield>
              <!-- LABEL_WITH_FORM_FIELDS END -->

                <!-- BUTTON -->
                <div id="custom-form-submit-button">
                    <button type="button" class="btn btn-primary" id="submitForm" ref="submitForm"  @click="validateForm()"
                    :style="buttonStyle">
                      <span :class="submitButtonIconClass"></span>&nbsp;{{lang(submitButtonText)}}</button>
                </div>
                <!-- BUTTON END -->
            </div>
          </div>


    </div>
</template>
<script>
import { changeKeyNames, boolean, getIdFromUrl } from "helpers/extraLogics.js";
import { errorHandler, successHandler } from "helpers/responseHandler";
import { store } from "store";
import { mapGetters } from "vuex";
import { setTimeout } from "timers";
import axios from 'axios';
import _ from 'lodash-core';
import $ from 'jquery';
import {assignLabel} from 'helpers/assignCustomFieldLabel';

export default {

  name : 'create-form',

  props: {

    /**
     * Person who is accessing the component ('agent', 'user', 'admin')
     */
    person: {type: String},

    /**
     * NOTE: for recur a seperate component has to be build, so that this component can be cleaned
     * If used by recur/editTicket/forkTicket
     */
    usedby : {type: String},

    /**
     * 'ticket', 'user' or 'organisation'
     */
    category: {type: String, required: true},

    /**
     * the endpoint from which edit data has to be fetched
     */
    editDataApiEndpoint : {type: String, default: null},

    /**
     * The endpoint to which data has to be submitted
     */
    submitApiEndpoint : {type: String, default: null},

    buttonClass : { type : String, default : 'btn btn-primary'},

    buttonStyle : { type : Object, default : ()=>{}},

    color : { type : String, default : '#1d78ff'},

    alertComponent : { type : String, default : '' },

    submitButtonIconClass: { type: String, default: 'fa fa-save'},

    submitButtonText: { type: String, default: 'submit' },
  
    editFormId: { type: Number | String, default: '' }
  },

  data() {
    return {
      /**
       * Data property used to store thecustom fields array
       */
      formArray: [],

      /**
       * Dta property used to check an maintain the vlidation count
       */
      validationArray: [],

      /**
       * Child count for nested child fields and helpful during the validation
       */
      childrenCount: 0,
      /**
       * Variable use to make sure the submit buton is being clicked only once
       */
      count: 0,

      formData: new FormData(),

      /**
       * Data property used to store the attachments
       */
      attachments: [],

      /**
       * Store the inline attachments
       */
      inline: [],
      /**
       * Store the CC
       */
      cc: [],

      /**
       * Initalize the recur object when usedby is recur,ie mainly used for recur ticket
       */
      recur: {
        interval: "",
        delivery_on: "",
        start_date: null,
        end_date: null,
        end_value: "",
        id: 0
      },

      /**
       * Store the editCustomfield values when received from te backend
       */
      editCustomfieldvalue: {},

      loading: false,

      firstTimeRendered: false,

      hasDataPopulated: false,

      alertComponentName : "createTicket"+this.category,
      attachmentIds: []
    };
  },
  created() {
    
    this.alertComponentName = this.alertComponent ? this.alertComponent : "createTicket"+this.category;

    window.eventHub.$on("UpdateAttchment", this.updateAttachment);
    window.eventHub.$on('updateAttachmentIds', this.updateAttachmentIds)
    window.eventHub.$on("UpdateInline", this.updateInline);
    window.eventHub.$on("UpdateCC", this.updateCC);
    this.getCustomFields();

    this.firstTimeRendered = true;
  },
  components: {
    "time-period": require("../../Admin/Recurring/TimePeriod.vue"),
    alert: require("../../MiniComponent/Alert"),
    "custom-loader": require("components/MiniComponent/Loader"),
    'label-with-formfield': require('components/FormFields/LabelWithFormField.vue')
  },

  computed: {

    ...mapGetters({getUserData :"getUserData", batchTicketEnabled: 'getBatchTicketEnabled', formMode:'getFormMode'}),

    mode(){

      // TODO: these logics has to be moved to parent
      if(this.currentPath().indexOf("edit") >= 0 || this.usedby == 'edit-ticket' || this.usedby == 'fork-ticket'){
        return 'edit';
      }
      return 'create';
    },

  },

  methods: {

    /**
     * Sets batch ticket status
     * NOTE: currently we don't have batch ticket in recur, once that is implemented, this method should be updated
     */
    setBatchTicketStatus(status){
      if(this.usedby != 'recur' && this.person == 'agent'){
        this.$store.dispatch('setBatchTicketEnabled', boolean(status));
      }
    },

    getCustomFields() {

      if(!this.firstTimeRendered){
        this.loading = true;
      }

      let params = {};

      params.category = this.category;

      params.mode = this.formMode;

      axios.get("api/get-form-data", {params: params}).then(resp => {
        this.loading = false;
        this.reRenderForm();
        this.$store.dispatch('setMetaFormData', resp.data.data);
        this.setBatchTicketStatus(resp.data.data.batch_ticket_status);
        let formFields = resp.data.data.form_fields;
        assignLabel(formFields, resp.data.data.language, this.person),
        this.formArray = formFields;
        this.attachments = [];
        this.inline = [];
        this.editformMethod();
      }).catch(err => {
        errorHandler(err,this.alertComponentName)
      });
    },

    /**
     * Re-renders the form
     * @return {undefined}
     */
    reRenderForm(){
      this.hasDataPopulated = false;
      setTimeout(()=>{
        this.hasDataPopulated = true;
      }, 0)
    },

    //valdation happening here
    validateForm() {
      this.$refs.submitForm.disabled = true;
      this.childrenCount = 0;
      this.validationArray = [];
      $.each(this.$children, this.nestedChildValidation);
      this.scrollToErrorBlockIfNeeded()
    },
    //check validation in children components
    nestedChildValidation(key, value) {
      // console.log(key, value, "inside nested validation");
      this.childrenCount++;
      value.$validator.validateAll().then(result => {
        if (result) {
          this.validationArray.push(result);
        }
      });
      if (value.$children.length != 0) {
        // console.log(this.childrenCount, "value of the children");
        $.each(value.$children, this.nestedChildValidation);
      } else {
        setTimeout(() => {
          if (this.validationArray.length == this.childrenCount) {
            this.submitForm();
          } else {
              console.log(value.$validator.errors.items, "after the validation");
            this.$refs.submitForm.disabled = false;
          }
        }, 50);
      }
    },
    //set editor value
    setEditorValue(editorValue) {

      return editorValue;
    },


    /**
     * Appends attachments/inlines to the state by type. So if type is passed as 'inline', this[type] means this.inline
     * @param  {String} type  'attachments' or 'inline'
     * @return {undefined}
     */
    appendAttachments(type){
      //set inline poster
      if (this[type].length != 0) {
        for (let i in this[type]) {
          this[type][i]["poster"] = (type == "inline") ? "INLINE" : "ATTACHMENT";
          if (typeof this[type][i] == "object" && this[type][i] != null) {
            delete this[type][i].base_64;
            for (let k in this[type][i]) {
              this.formData.append(
                type+"[" + i + "][" + k + "]",
                this[type][i][k]
              );
            }
          }
        }
      }
    },

    //submitForm
    submitForm() {
      this.count++;
      if (this.count == 1) {
        for (let i in this.formArray) {
          if (this.formArray[i].title == "Description") {
            this.formArray[i].value = this.setEditorValue(
              this.formArray[i].value
            );
          }
        }

        // NOTE: currently there is only one API which handles both agent and client panel
        // registrations with organisation and organisation department. This is a workaround
        // for the time being and has to be changed after first version is released
        let panel = this.person == 'user' ? 'client' : 'agent';
        this.formData.append('panel', panel);

        $.each(this.formArray, this.appandToFormdata);

        this.appendAttachments('attachments');
        this.appendAttachments('inline');

        if(this.mode === 'edit') {
          for (let i in this.attachmentIds) {
            this.formData.append("attachment_ids[" + i + "]", this.attachmentIds[i]);
          }
        }

        //append cc emails
        if (this.cc.length != 0) {
          for (let i in this.cc) {
            this.formData.append("cc[" + i + "]", this.cc[i]);
          }
        }

        //if component used in reccurring page
        if (this.usedby === "recur") {
          for (let i in this.recur) {
            // we have to allow id index. As id can be null in case of a new recur
            if (i != "end_value" && (boolean(this.recur[i]) || i == 'id')) {
              this.formData.append("recur[" + i + "]", this.recur[i]);
            }
          }
        }

        /**
         * Edit form case
         * Append form_id to update the form// not to create a new form
         */
        if(boolean(this.editFormId)) {
          this.formData.append('id', this.editFormId)
        }

        this.submitDataToServer();
      }
    },

    submitDataToServer(){
      let headers = { "Content-Type": "multipart/form-data" };
      this.loading = true;
      axios.post(this.submitApiEndpoint, this.formData, { headers }).then(resp => {
        this.afterSuccessActions(resp)
      }).catch(error => {
        this.afterFailureActions(error)
      });
    },

    //appending values to form data
    appandToFormdata(index, value) {
      if (typeof value == "object" && value != null) {

        // WARNING : This line was written by an illegal son of Albert Einstein, when he was at
        // peak of his logical wisdom. Touch this line at your own risk
        if (value["display_for_" + this.person] == 1 && value.id != undefined && value.value != undefined && value.value !== null) {

          if (typeof value.value == "object") {

            // if values are array (not object)
            if (Array.isArray(value.value)) {

              let fieldKey = boolean(value.unique) ? value.unique : changeKeyNames(value.title);

              //This bullshit has been added so that a workaround can be made to make organisation department work
              if(value.title == 'Organisation Department' && this.category == 'user'){
                // organisation
                value.value = boolean(value.value) ? value.value[0] : null;
                this.formData.append(value.unique, value.value);
              }

              else {
                for (let i in value.value) {
                  // appending all array values
                  this.formData.append(fieldKey + "[" + i + "]",value.value[i]);
                }
              }
            } else {
              if(value.type === 'date') {
                this.formData.append(value.unique, value.value);
              } else {
                // when values are object (for eg. status_ids: {id: 1, name:'open'}, form sending value should be 1)
                this.formData.append(value.unique, value.value.id);
              }
            }
          } else if (value.unique) {
            // when value is non-object/non-array
            this.formData.append(value.unique, value.value);
          } else {

            //NOTE: not sure about this part of the code
            if (value.value === undefined && this.mode == 'edit') {
              this.formData.append(changeKeyNames(value.title), "");
            } else {
              if (value.hasOwnProperty("value")) {
                this.formData.append(changeKeyNames(value.title), value.value);
              } else {
                // this.formData.append(changeKeyNames(value.title), "");
              }
            }
          }
        }
        $.each(value, this.appandToFormdata);
      }
    },

    updateAttachmentIds(array) {
      this.attachmentIds = array;
    },
    //update editor attachment array
    updateAttachment(array) {
      this.attachments = array;
    },
    //update ediotr iinline image array
    updateInline(array) {
      this.inline = array;
    },
    //update requester cc array
    updateCC(array) {
      this.cc = array;
    },

    /**
     * Edit Api call method , all the custom form fields , once the data edited fields are fetched,
     * from the backend  the same data is being properyl binded in the formarry this is being carried out thorugh
     * unique and the title property
     */
    editformMethod() {
      if (this.mode == 'edit') {

        this.loading = true;

        axios.get(this.editDataApiEndpoint).then(resp => {
            this.editCustomfieldvalue = resp.data.data;
            this.recur = this.editCustomfieldvalue.recur;

            // formArray will update in this loop for Helptopic and department child fields
            // it has to be looped again so that those child fields can be binded
            this.formArray.map(formField => {

                if (!boolean(formField.default)) {
                  formField.value = this.editCustomfieldvalue[formField.unique];

                } else {

                  // TODO: check if this seperate code is required
                  if (this.editCustomfieldvalue["cc"] !== "") {
                    this.$set(formField, "cc", this.editCustomfieldvalue["cc"]);
                  }
                  formField.value = this.editCustomfieldvalue[changeKeyNames(formField.title)];
                }

                this.bindNestedCustomFields(formField);
            });

            // making it asynchronous so that child fields (which comes in edit API call), can
            // be binded becuase those fields won't be updated in formArray until helptopic and
            // department is populated
            this.bindHelptopicDepartmentNestedFields();

            this.loading = false;
          })
          .catch(err => {            
            errorHandler(err, this.alertComponentName);
          });
      }
    },

    /**
     * Binds helptopic and department nested values
     * @return {Promise}
     */
    async bindHelptopicDepartmentNestedFields(){

      // NOTE: this can be removed once department and helptopic form is implemented succesfully

      //looping over again to bind helptopic and department asynchronously, so that formArray gets
      //enough time to get updated by child fields for helptopic and department because helpTopic
      //and department child fields comes in the edit API, which also contains the values of those
      //fields
      await this.sleep(0);
      this.formArray.map(formField => {
        this.bindNestedCustomFields(formField);
      });
    },

    /**
     * if any custom fields has a nested fields, the below function helps to bind the nested values
     */
    bindNestedCustomFields(formField) {
      // if form field exists and formField.options is an array, bind child fields
      if(boolean(formField) && boolean(formField.options)){
        this.bindNestedCustomFieldsOptions(formField.options);
      }
    },

    /**
     * if the nested field itself have another nested component then , then below
     * function would help to bind the data
     * below function is a recursive function which would keep on checking unless
     * option.nodes.length is equal to 0
     */
    bindNestedCustomFieldsOptions(options) {
      options.map(option => {
        if (option.nodes && option.nodes.length > 0) {
          option.nodes.map(param => {
            // console.log(param, "param value");
            if (!boolean(param.default)) {
              let customFieldValue = this.editCustomfieldvalue["custom_" + param.id];
              param.value = customFieldValue != undefined ? customFieldValue : "";
            }
            this.bindNestedCustomFieldsOptions(param.options);
          });
        }
      });
    },

    afterSuccessActions(res){
      successHandler(res, this.alertComponentName);
      this.loading = false;
      this.formData = new FormData;

      // emitting the information that which form is submited
      window.eventHub.$emit(this.category +'FormSubmitted', res.data.data);

      this.count = 0;
      this.$refs.submitForm.disabled = false;

      if(this.mode == 'create'){
        this.getCustomFields();
      }
    },

    afterFailureActions(err){
      this.loading = false;
      errorHandler(err, this.alertComponentName);
      this.formData = new FormData;
      this.count = 0;
      this.$refs.submitForm.disabled = false;
      this.scrollToErrorBlockIfNeeded();
      window.eventHub.$emit(this.category +'FormError');
    },

    /**
     * Scrolls to error block
     * @return {undefined}
     */
    scrollToErrorBlockIfNeeded(){
      // scrolling to error danger
      setTimeout(()=>{
        let x = document.getElementsByClassName("is-danger")[0];
        if(x !== undefined){
          x.scrollIntoView({behavior: "smooth", block: 'end'});
        }

        // there are few places where validation is shown with class is-danger and few classes with field-danger
        let y = document.getElementsByClassName("field-danger")[0];
        if(y !== undefined){
          y.scrollIntoView({behavior: "smooth", block: 'end'});
        }
      }, 50)
    },
  }
};
</script>