<template>
	  <div>
          <!-- text field with validation-->
          <text-field  v-if="(node.type=='text'|| node.type=='number'|| node.type=='email') &&  node.title!='Requester'" :validate="validation" :unique="unique" :fieldName="fieldName" :fieldValue="fieldValue"  v-on:assignToModel="setModel" :objKey="'value'" :node="node" :disableField="disableValue" :validationMessage="node.labels_for_validation" ></text-field>

          <date-picker v-if="node.type=='date'" v-model="selectedDateTime" lang="en" type="datetime" :time-picker-options="timeOptions" format="MMMM DD YYYY HH:mm" input-class="form-control col-xs-12" v-on:input="onDateChange" :clearable="true" :editable="true" :popup-style="{ top: '100%', left: 0}" @click.native="handleDatePickerClick" :confirm="true"></date-picker>


          <!-- text area with validation -->
          <text-area v-if="node.type=='textarea'&&(node.title!='Description' || formType =='vertical')" :validate="validation" :unique="unique" :disableField="disableValue" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :validationMessage="node.labels_for_validation" :objKey="'value'" ></text-area>
          <!-- select with validation -->
          <select-field v-if="node.type=='select' && node.default==0 && !node.api_info" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :valueFor="'label'" :apiEndpoint="apiEndpoint" :disableField="disableValue"></select-field>

          <!-- SELECT BOX FOR API FORM FIELD -->
          <select-field-with-api v-if="node.type=='select' && node.default==0 && node.api_info" v-on:getValue="setApiFieldValue" :elements="apiFieldOptions" :holder="fieldName" :reference="node.title" :objKey="'value'" :node="node" :validate="validation" :disableField="disableValue" :multiple="multiple" :key-to-bind="keyToBindForAPIField"></select-field-with-api>

          <!-- select field with api search -->
          <select-field-with-api v-if="(node.type=='select' || node.type=='api') && node.default==1 && node.title!='Captcha' && node.title !== 'Help Topic' && node.title !=='Department'" v-on:getValue="setVueSelect" :api="apiEndpoint" :holder="fieldName" :reference="node.title" :objKey="'value'" :node="node" :validate="validation" :disableField="disableValue" :multiple="multiple"></select-field-with-api>

          <!-- ONLY FOR NEW HELPTOPIC AND DEPRTMENT  -->
          <select-field-with-api v-if="(node.title=='Help Topic'|| node.title==='Department')&&node.default==1&&node.title!='Captcha'&&node.title!='Assigned' && node.title !='CC'"  v-on:getValue="setVueSelect" :api="apiEndpoint" :holder="fieldName" :reference="node.title" objKey='value' :node="node" :validate="validation" :person="person" :disableField="disableValue"></select-field-with-api>
          <!-- ONLY FOR NEW HELPTOPIC AND DEPRTMENT  END -->


          <!-- multiselect linking option -->
          <select-field v-if="node.type=='multiselect' && node.title !=='Help Topic' && node.title!=='Department'"   :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :valueFor="'id'" :titleName="node.title" :nodesArray="nodesArray" :person="person" :disableField="disableValue"></select-field>
          <!-- checkbox field  -->
          <checkbox-field v-if="node.type=='checkbox'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :person="person" :formType="formType" :node="node" :disableField="disableValue" :category="category"></checkbox-field>
          <!-- radio button -->
          <radio-field  v-if="node.type=='radio'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :person="person" :formType="formType" :node="node" :disableField="disableValue"></radio-field>
          <!-- recaptcha field -->
          <recaptcha-field v-if="recaptchaDisplay" :node="node" category="ticket" v-on:assignToModel="setModel">
            
          </recaptcha-field>
          <!-- media gallery attachment -->

          <!-- description can simply be a text field too in case of workflow-listener -->
          <media-gallery v-if="formType!='vertical'&&node.title=='Description'&&node.media_option&&person=='agent'&&node.type=='textarea'" v-on:getAttach="getAttachment" v-on:inlineAttach="getInline" :editorName="'reply_content'" :inlineFiles="inline" :attachmentFiles="attchments" page="ticket"></media-gallery>
          <!-- description ckeditor -->
          <ckeditor v-if="formType!='vertical'&&node.title=='Description'&&node.type=='textarea'" :fieldName="fieldName" :node='node' :validate="validation" :fieldValue="fieldValue" :objKey="'body'" v-on:assignToModel="setModel"></ckeditor>

          <!-- editor attachment-->
          <div id="file_details">
              <div  id='hidden-attach' contenteditable='false' v-for="(attachment,index) in attchments">{{attachment.filename}}({{attachment.size}} bytes)<i class='fa fa-times close-icon' aria-hidden='true' @click='removeAttachment(index)'></i></div>
          </div>
          <!-- ticket requester component only for agent/usr ticket create -->
          <agent-requester v-if="node.title=='Requester'&&person=='agent'&&!multiRequest" :node="node" :person="person" v-on:getValue="setVueSelect" :usedby="usedby" :validate="validation" :api="apiEndpoint"></agent-requester>
          <!-- muliple requester file upload -->
          <file-upload v-if="node.title=='Requester'&&person=='agent'&&multiRequest" :isMultiple="false" v-on:assignToModel="setModel" :objKey="'requester'" :node="node" :fieldName="fieldName" :validate="validation" accept=".csv,.xlxs" :unique="node.unique"></file-upload>
          <!-- user requester -->
          <user-requester  v-if="node.title=='Requester'&&person=='user'" :node="node" v-on:assignToModel="setModel" :person="person" :disableField="disableValue"></user-requester>
          <!-- multiple requester option via checkbox-->
          <div v-if="node.title=='Requester' && getBatchTicketEnabled">
              <span class="pull-right is-danger" style="margin-right:150px;">Note: Only .csv and xlxs format are supported</span>
              <label class="checkbox-inline"><input type="checkbox" name="multirequester" v-model="multiRequest">{{lang('upload_multi_requester')}}</label>
          </div>
          <!-- file-upload -->
          <file-upload v-if="node.title=='Attachments'" :isMultiple="true" v-on:assignToModel="setModel" :objKey="'value'" :fieldName="fieldName" :validate="validation" :node="node" :unique="node.unique">
            <div slot="attachmentList" v-if="attachmentList && attachmentList.length > 0">
              <div class="upload-files" contenteditable="false" v-for="(attachment,index) in attachmentList" :key="index">
                {{attachment.value}} ({{attachment.size}})
                <i class="fa fa-times file-actions" aria-hidden="true" @click="spliceAttachment(index)"></i>
                <a class="file-actions" @click="downloadAttachment(attachment.link)" target="_blank" rel="noopener noreferrer"><i class="fa fa-download" aria-hidden="true"></i></a>
              </div>
            </div>
          </file-upload>
	  </div>
</template>
<script>
import { boolean, FORM_BUILDER_UTILS } from "helpers/extraLogics";
import { mapGetters } from "vuex";
import DatePicker from 'vue2-datepicker';
import moment from 'moment';

export default {
  props: [
    "node",
    "person",
    "objIndex",
    "formType",
    "nodesArray",
    "usedby",
    "category"
  ],
  data() {
    return {
      recaptchaDisplay: false,
      mediaOption: false,
      inline: [],
      attchments: [],
      multiRequest: false,
      fieldValue : '',
      timeOptions : { start: '00:00', step: '00:30', end: '23:30' },//date picker time options
      selectedDateTime: '',
      attachmentList: [],
      apiFieldOptions: []
    };
  },

  watch : {
      "node.value"(newValue){
        this.updateFieldValue(newValue)
      },

      multiRequest(value){
        // so that API endpoint can be changed according to it. Batch ticket has different API endpoint
        window.eventHub.$emit('batchRequesterStatus', value)
      }
  },

  beforeMount() {

    // Make api call to fetch options for the API form field
    if(this.node.type=='select' && this.node.default == 0 && this.node.api_info) {
      axios.get(this.apiEndpoint)
      .then((response) => {
        this.apiFieldOptions = response.data
      })
      .catch((error) => {
        this.apiFieldOptions = [];
        console.error(error)
      })
    }
  },

  mounted() {

    // NOTE: this can be removed once department and helptopic form is implemented succesfully
    // making it async for so that child fields of helptopic and department gets enough time to
    // update their child fields
    setTimeout(()=>{
      this.updateFieldValue(this.node.value);

      /** update selectedDateTime in case of edit*/
      if(this.node.type === 'date') {
        this.selectedDateTime = this.node.value ? this.formattedTime(this.node.value) : undefined;
        this.onDateChange();
      }
      if(this.node.type === 'file') {
        this.attachmentList = this.node.value;
        this.updateAttachmentIds();
      }
    },1000)

    if (this.node.title == "Captcha" && this.node["display_for_" + this.person]) {
      this.recaptchaDisplay = true;
    }
    //set media option enable or disable
    if (this.node.title == "Description" && this.node.media_option && this.person == "agent") {
      this.mediaOption = true;
    }
  },

  beforeDestroy(){
    // as soon as a component is unmounted, its value should be gone
    this.node.value = "";
  },

  computed: {
    ...mapGetters(['getBatchTicketEnabled', 'formattedTime']),

    multiple(){
      if(this.node.title == 'CC' || this.node.title == "Tags" || this.node.title == "Labels"){
        return true;
      }
      return false;
    },

    //validation obj
    validation() {
      var obj = {};

      if(this.node.type === 'email') {
        obj['email'] = true;
      }

      if (this.node.type == "text" || (this.node.type == "textarea" && this.node.title != "Description")) {
        // add a max length validation
          obj['max'] = 2000;
          if (this.node.pattern != "" && this.node.pattern != null) {
          obj["regex"] = this.node.pattern;
        }
      } else if (this.node.type == "number") {
        obj["numeric"] = true;
      }

      // disable this validation
      obj["required"] = boolean(this.node["required_for_" + this.person]);
      // console.log(obj, "validation object ", this.node.title);

      return obj;
    },
    fieldName() {
      return this.node['label'];
    },

		unique(){
			return this.node.unique;
		},

    disableValue() {
      return boolean(this.node.is_locked);
    },

    apiEndpoint() {
      return FORM_BUILDER_UTILS.getApiInfoObj(this.node.api_info).url;
    },

    keyToBindForAPIField() {
      return FORM_BUILDER_UTILS.getApiInfoObj(this.node.api_info).key;
    }
  },
  components: {
    "text-field": require("./TextFieldWithValidation.vue"),
    "text-area": require("./TextAreaWithValidation.vue"),
    "select-field": require("./SelectFieldWithValidation.vue"),
    "select-field-with-api": require("../Agent/tickets/filters/FormDynamicSelect.vue"),
    "checkbox-field": require("./CheckboxField.vue"),
    "radio-field": require("./RadioButtonField.vue"),
    "recaptcha-field": require("./RecaptchaField.vue"),
    "agent-requester": require("./AgentRequester.vue"),
    "user-requester": require("./UserRequester.vue"),
    "file-upload": require("./FileUpload.vue"),
    "select2-cc-field": require("./Select2CCfield.vue"),
    "media-gallery": require("components/Common/ChunkUpload/ChunkUpload.vue"),
    ckeditor: require("./CkeditorWithValidation.vue"),
    DatePicker
  },
  methods: {

    onDateChange() {
      if(this.selectedDateTime) {
        this.node.value = moment(this.selectedDateTime).format('YYYY-MM-DD+HH:mm:ss');
      } else {
        this.node.value = "";
      }
      this.$emit('updateForm', this.node, this.objIndex);
    },

    /** This will assign current date time in date picker if clicked and have empty field */
    handleDatePickerClick() {
      if(!this.selectedDateTime) {
        this.selectedDateTime = new Date();
        this.onDateChange();
      }
    },

    spliceAttachment(index) {
      const isConfirmed = confirm('Are you sure you want to delete this attachment?');
      if(!isConfirmed) {
        return;
      }
      this.attachmentList.splice(index, 1);
      this.updateAttachmentIds();
    },

    updateAttachmentIds() {
      if(!this.attachmentList) {
        return;
      }
      const attachmentIds = this.attachmentList.map(element => element.id)
      window.eventHub.$emit('updateAttachmentIds', attachmentIds);
    },

    updateFieldValue(value){
      if(!boolean(value)) {
        this.node.value = "";
        this.fieldValue = "";
      } else if(this.node.type === 'date') {
        this.fieldValue = value ? this.formattedTime(value) : undefined;
      } else {
        this.fieldValue = value;
      }
    },

    setApiFieldValue(value) {
      const key = FORM_BUILDER_UTILS.getApiInfoObj(this.node.api_info).key;
      this.node['value'] = value[key];
      this.$emit("updateForm", this.node, this.objIndex);
    },

    //set model value
    setModel(x, y) {
      if (typeof y == "object" && y != null && x == "assigned_to") {
        this.node["value"] = y[0];
      } else {
        this.node["value"] = y;
      }
      this.$emit("updateForm", this.node, this.objIndex);
    },
    //set vue select  value
    setVueSelect(x) {
      //  if array, get list of ids
      if (x !== null) {
        if(Array.isArray(x)){
        this.node["value"] =  x.map(y => {
            if(y.id != undefined){
              return y.id;
            }
          })
          // this.node["value"] = x.map(element => element.id);
        } else {
          this.node["value"] = x.id;
        }
      } else {
        this.node["value"] = null;
      }
      this.$emit("updateForm", this.node, this.objIndex);
    },
    //get Attchment
    getAttachment(x) {
      console.log(x, "TICKET FORM MODULES");
      this.attchments.push(x);
      window.eventHub.$emit("UpdateAttchment", this.attchments);
    },
    //remove attachment
    removeAttachment(x) {
      this.attchments.splice(x, 1);
      window.eventHub.$emit("UpdateAttchment", this.attchments);
    },
    //get inline image
    getInline(x) {
      this.inline.push(x);
      window.eventHub.$emit("UpdateInline", this.inline);
    },

    downloadAttachment(link) {
      window.open(this.basePath() + link);
    }
  }
};

</script>

<style scoped>
#hidden-attach {
  background-color: #f5f5f5;
  border: 1px solid #dcdcdc;
  font-weight: bold;
  margin-top: 9px;
  overflow-y: hidden;
  padding: 4px 4px 4px 8px;
  max-width: 448px;
}
.close-icon {
  float: right;
  cursor: pointer;
}
</style>
<style>
  .mx-input{
      border-radius: 0 !important;
    }
    .mx-shortcuts-wrapper .mx-shortcuts {
      text-transform: capitalize;
    }
    .mx-calendar-content {
      width: 224px !important;
    }
    .mx-datepicker{
      width: 100% !important;
    }
    .mx-datepicker-range {
      width: 100% !important;
    }
    .mx-input-wrapper input {
      background-color: transparent !important;
    }
    .mx-calendar-icon{
      height: auto !important;
    }
    .mx-input-append{
      background-color: transparent !important;
    }
    .mx-input-wrapper .mx-clear-wrapper {
      right: 1.8rem;
    }
</style>
