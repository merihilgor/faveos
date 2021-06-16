<style scoped>
.domain-rule {
  color: #a3a3a3;
  margin-top: 5px;
  margin-bottom: 5px;
}
</style>
<template>
	  <div>
          <!-- text field with validation-->
          <text-field  v-if="node.type=='text'||node.type=='number'||node.type=='date'||node.type=='email'" :unique="unique" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue"  v-on:assignToModel="setModel" :objKey="'value'" :disableField="false" :validationMessage="node.labels_for_validation"></text-field>
          <!-- text area with validation -->
          <text-area v-if="node.type=='textarea'" :validate="validation" :fieldName="fieldName" :unique="unique" :fieldValue="fieldValue" v-on:assignToModel="setModel" :validationMessage="node.labels_for_validation" :objKey="'value'"></text-area>

          <!-- select with validation -->
          <select-field v-if="node.type=='select'&&node.default==0" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :valueFor="'label'" :apiEndpoint="apiEndpoint"></select-field>

          <!-- checkbox field  -->
          <checkbox-field v-if="node.type=='checkbox'" :node="node" :category="category" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'" :person="person" :formType="formType"></checkbox-field>

          <!-- radio button -->
          <radio-field  v-if="node.type=='radio'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options" :objKey="'value'"></radio-field>

          <recaptcha-field v-if="recaptchaDisplay" :node="node" category="organisation" v-on:assignToModel="setModel">
            
          </recaptcha-field>

           <!-- file-upload for logo-->
           <file-upload v-if="node.title=='Organisation Logo'" :isMultiple="false" v-on:assignToModel="setModel" :objKey="'value'" :fieldName="fieldName" :validate="validation" :node="node" :unique="node.unique" accept="image/*"></file-upload>

          <select2-tag v-if="node.title=='Organisation Domain Name'" :isMultiple="true"  v-on:getModel="setModel" :objKey="'value'" :fieldModel="fieldValue" :selectId="'orgdomain-id'" :selectionLength="100" :validate="validation" :fieldName="fieldName" :node="node" :checkCreateTag="true"  :unique="node.unique"></select2-tag>

           <div v-if="node.title=='Organisation Domain Name'" class="domain-rule">{{lang('put_domain_name_without_http://,https://')}}&nbsp;&nbsp;&nbsp;&nbsp;Example:-example.com</div>

          <select2-tag  v-if="node.title=='Organisation Department' || node.title=='Department'" :isMultiple="true"  v-on:getModel="setModel" :objKey="'value'" :fieldModel="fieldValue" :selectId="'orgdept-id'" :selectionLength="100" :validate="validation" :fieldName="fieldName" :node="node" :checkCreateTag="false" :unique="node.unique"></select2-tag>
	  </div>
</template>
<script>
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
      category: "organization",
      multiRequest: false,
      linking: this.$store.state.formBuilder.linkModule
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

    if(this.node.title == 'Organisation Department'){
      this.node['unique'] = 'organisation_department';
    }
  },

  beforeDestroy(){
    // as soon as a component is unmounted, its value should be gone
    this.node.value = "";
  },

  computed: {
    //validation obj
    validation() {
      var obj = {};
      if (this.node.type == "text" || this.node.type == "textarea") {
        obj['max'] = 2000;
        if (this.node.pattern != "" && this.node.pattern != null) {
          obj["regex"] = this.node.pattern.replace(/^"(.*)"$/, "$1");
        }
        obj["required"] = this.node["required_for_" + this.person] == 1;
      } else if (this.node.type == "number") {
        obj["numeric"] = true;
        obj["max"] = 15;
        obj["required"] = this.node["required_for_" + this.person] == 1;
      } else if (this.node.type == "email") {
        obj["email"] = true;
        obj["required"] = this.node["required_for_" + this.person] == 1;
      } else if (this.node.title === "Organization Domain Name") {
        obj["url"] = true;
        obj["required"] = this.node["required_for_" + this.person] === 1;
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
      return this.node["label"];
    },

		unique(){
			return this.node.unique;
    },
    
    apiEndpoint: function () {
			return FORM_BUILDER_UTILS.getApiInfoObj(this.node.api_info).url;
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
    "select2-tag": require("./Select2CustomTagCreate.vue")
  },
  methods: {
    //set model value
    setModel(x, y) {
      console.log(x, y, "value in x and y");

      if (typeof y == "object" && y != null && x == "assigned_to") {
        this.node[x] = y[0];
      } else {
        // console.log(this.node.value, "elese");
        this.node[x] = y;
        // this.$set(this.node, "value", y);
      }
      this.$emit("updateForm", this.node, this.objIndex);
    }
  }
};
</script>
