<template>
	  <div>
          <!-- text field with validation-->
          <text-field  v-if="node.type=='text'||node.type=='number'||node.type=='date'||node.type=='email'" :validate="validation" :unique="unique" :fieldName="fieldName" :fieldValue="fieldValue"  v-on:assignToModel="setModel" :objKey="'value'" :disableField="false" :validationMessage="node.labels_for_validation"></text-field>
          <!-- text area with validation -->
          <text-area v-if="node.type=='textarea'" :validate="validation" :unique="unique" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :validationMessage="node.labels_for_validation" :objKey="'value'"></text-area>

          <!-- select with validation -->
          <select-field v-if="node.type=='select'&&node.default==0" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :valueFor="'label'" :apiEndpoint="apiEndpoint"></select-field>

          <!-- checkbox field  -->
          <checkbox-field v-if="node.type=='checkbox'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :person="person" :formType="formType" :node="node"></checkbox-field>

          <!-- radio button -->
          <radio-field  v-if="node.type=='radio'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'"></radio-field>

          <!-- recaptcha field -->
          <recaptcha-field v-if="recaptchaDisplay" :node="node" category="user" v-on:assignToModel="setModel">
            
          </recaptcha-field>

          <!-- file-upload -->
          <file-upload v-if="node.title=='Attachments'" :isMultiple="true" v-on:assignToModel="setModel" :objKey="'value'" :fieldName="fieldName" :validate="validation"></file-upload>

          <!-- organisation field -->
          <select2-field v-if="node.title=='Organisation'" :isMultiple="true" :api="'ticket/form/dependancy?dependency=company'" v-on:getModel="setModel" :objKey="'company'" :fieldModel="fieldValue" :selectId="'org-id'" :selectionLength="100" :validate="validation" :node="node" :fieldName="fieldName"></select2-field>

          <!-- micro organisation field  -->
          <select2-field v-if="node.title=='Organisation Department' && mode != 'workflow-listener'" :isMultiple="true" :api="'ticket/form/dependancy?dependency=org_dept'" v-on:getModel="setModel" :objKey="'org_dept'" :fieldModel="fieldValue" :selectId="'orgdept-id'" :selectionLength="1" :validate="validation" :node="node" :fieldName="fieldName"></select2-field>

					<!-- for workflow listener -->
					<select-field-with-api v-if="node.title=='Organisation Department' && mode == 'workflow-listener'" v-on:getValue="setVueSelect" api="api/dependency/organisation-departments" :holder="fieldName" :reference="node.title" :objKey="'value'" :node="node" :validate="validation"></select-field-with-api>
	  </div>
</template>
<script>

import {mapGetters} from 'vuex';
import { FORM_BUILDER_UTILS } from 'helpers/extraLogics';

export default {
  props: ["node", "person", "objIndex", "formType", "nodesArray"],
  data() {
    return {
      recaptchaDisplay: false,
      mediaOption: false,
      test: "",
      inline: [],
      attchments: [],
      multiRequest: false,
    };
  },
  mounted() {
    //captch fields showing
    if (
      this.node.title == "Captcha" &&
      this.node["display_for_" + this.person] &&
      (this.person == "agent" || this.person == "user")
    ) {
      this.recaptchaDisplay = true;
    }
  },

	beforeDestroy(){
    // as soon as a component is unmounted, its value should be gone
    this.node.value = "";
  },

  computed: {
    ...mapGetters({mode: 'getFormMode'}),
    
    apiEndpoint: function () {
			return FORM_BUILDER_UTILS.getApiInfoObj(this.node.api_info).url;
		},

		//validation obj
    validation() {
      var obj = {};
      if (this.node.type == "text" || this.node.type == "textarea") {
        obj['max'] = 2000;
        if (this.node.pattern != "" && this.node.pattern != null) {
          obj["regex"] = this.node.pattern;
        }
        obj["required"] = this.node["required_for_" + this.person] == 1;
      } else if (this.node.type == "number") {
        obj["numeric"] = true;
				obj["max"] = 15;
        obj["required"] = this.node["required_for_" + this.person] == 1;
      } else if (this.node.type == "email") {
        obj["email"] = true;
        obj["required"] = this.node["required_for_" + this.person] == 1;
      } else if (this.node.type == "date") {
        obj["date_format"] = "DD/MM/YYYY";
        obj["required"] = this.node["required_for_" + this.person] == 1;
        this.node["labels_for_validation"] =
          "Enter a valid date format dd/mm/yyyy ";
      } else {
        obj["required"] = this.node["required_for_" + this.person] == 1;
      }
      return obj;
    },
    fieldName() {
      return this.node['label'];
    },

		unique(){
			return this.node.unique;
		},

    fieldValue() {
      if (!this.node.hasOwnProperty("value")) {
        this.node.value = "";
      }
      if (this.node.value == null || this.node.value == undefined) {
        this.node.value = "";
      }
      return this.node.value;
    }
  },
  components: {
    "text-field": require("./TextFieldWithValidation.vue"),
    "text-area": require("./TextAreaWithValidation.vue"),
    "select-field": require("./SelectFieldWithValidation.vue"),
    "checkbox-field": require("./CheckboxField.vue"),
    "radio-field": require("./RadioButtonField.vue"),
    "recaptcha-field": require("./RecaptchaField.vue"),
    "file-upload": require("./FileUpload.vue"),
    "select2-field": require("./Select2Field.vue"),
		"select-field-with-api": require("../Agent/tickets/filters/FormDynamicSelect.vue"),
  },
  methods: {
    //set model value
    setModel(x, y) {
      if (typeof y == "object" && y != null && x == "assigned_to") {
        this.node["value"] = y[0];
      } else {
        // console.log(this.node.value, "elese");
        this.node["value"] = y;
        // this.$set(this.node, "value", y);
      }
      this.$emit("updateForm", this.node, this.objIndex);
    },

		//set vue select  value
    setVueSelect(x) {
      if (x !== null) {
        this.node["value"] = x.id;
      } else {
        this.node["value"] = x;
      }
      this.$emit("updateForm", this.node, this.objIndex);
    },
  }
};
</script>
