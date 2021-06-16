<style scoped>
.col-sm-130 {
  width: 70%;
}
.col-sm-12 {
  margin-top: 10px;
  display: flex;
  flex-wrap: wrap;
}
.mandatory {
  color: red;
}
.clear {
  clear: both;
  width: inherit;
}
.ver-label {
  display: block;
  margin-bottom: 0px;
}
</style>
<template>
	    <div class="row">
            <!-- horizantal label with form fields -->
            <div :id="'form-field-'+index" class="col-sm-12" v-for="(form,index) in customForm"  :key="index" v-if="isDisplay(form)&&formType=='horizantal'">

               <!-- FORM LABEL -->
               <div class="col-sm-2" :id="'form-field-label-'+index">

                <label :title="form['label']">{{form['label'] == 'Captcha' ? '' : form['label']}}

                   <span class="mandatory" v-if="isRequired(form) && form['label'] != 'Captcha'">*</span>
                   <tool-tip v-if="form['label'] != 'Captcha' && form.description && form.description.trim().length !== 0" :message="form.description" size="small"></tool-tip>
                </label>
                  
               </div>
               <!-- FORM LABEL END -->

               <!-- FORM COMPONENT BASED ON CATEGORY -->
               <div class="col-sm-10">
                 <span v-if="category=='user' || category=='organisation'">
                  <!-- user form fields components -->
                  <user-form-fields v-if="category=='user'" :node="form" :person="person" :objIndex="index" v-on:updateForm="setNewObject" :formType="formType" :nodesArray="customForm"></user-form-fields>
                  <!-- organisation form fields components -->
                  <organisation-form-fields v-if="category=='organisation'" :node="form" :person="person" :objIndex="index" v-on:updateForm="setNewObject" :formType="formType" :nodesArray="customForm"></organisation-form-fields>
                 </span>
                 <span v-else>
                   <ticket-form-fields :node="form" :person="person" :objIndex="index" v-on:updateForm="setNewObject" :formType="formType" :nodesArray="customForm" :usedby="usedby"></ticket-form-fields>
                 </span>
               </div>
               <!-- FORM COMPONENT BASED ON CATEGORY END -->


               <!-- NESTED FIELDS -->
               <div class="clear" v-for="option in form.options" 
               v-if="option.nodes && option.nodes.length!=0&&form.value&&(form.title=='Select'||form.title=='Radio' || form.title=='Text Field')">
                      <label-with-formfield :category="category" v-if="option.labels==form.value" :formType="'horizantal'" :customForm="option.nodes" :person="person"></label-with-formfield>
                </div>
               <!-- NESTED FIELDS END -->

                <!-- ONLY FOR NESTED CHECKBOX -->
               <div class="clear" v-if="form.title =='Checkbox' && (form.value)" v-for="option in form.options" :key="option.id" >
                 <label-with-formfield :category="category" v-if="form.value.includes(option.labels)" :formType="'horizantal'" :customForm="option.nodes" :person="person"></label-with-formfield>
               </div>
               <!-- ONLY FOR NESTED CHECKBOX END -->

               <!-- ONLY FORTYPE MULTISELECT -->
               <div class="clear" v-for="option in form.options" :key="option.id" v-if="option.nodes && option.nodes.length!=0 && form.value && form.type=='api'">
                      <label-with-formfield :category="category" v-if="option.value ==form.value || option.id ==form.value" :formType="'horizantal'" :customForm="option.nodes" :person="person"></label-with-formfield>
                </div>
               <!-- ONLY FORTYPE MULTISELECT END -->
            </div>
            <!-- vertical label with form fields -->
            <div class="col-sm-12" v-if="isDisplay(form)&&formType=='vertical'" v-for="(form,index) in customForm" :key="index">
                <label class="ver-label"  v-if="displayLabel">{{form['label']}} <span class="mandatory" v-if="form['required_for_'+person]&&form['label']">*</span></label>
                <!-- ticket form fields components -->
                <div class="col-md-12">
                  <span v-if="category=='user' || category=='organisation'">
                    <!-- user form fields components -->
                    <user-form-fields v-if="category=='user'" :node="form" :person="person" :objIndex="index" v-on:updateForm="setNewObject" :formType="formType" :nodesArray="customForm"></user-form-fields>
                    <!-- organisation form fields components -->
                    <organisation-form-fields v-if="category=='organisation'" :node="form" :person="person" :objIndex="index" v-on:updateForm="setNewObject" :formType="formType" :nodesArray="customForm"></organisation-form-fields>
                  </span>
                  <span v-else>
                    <ticket-form-fields :node="form" :person="person" :objIndex="index" v-on:updateForm="setNewObject" :formType="formType" :nodesArray="customForm" :usedby="usedby" :category="category"></ticket-form-fields>
                  </span>
                </div>
                 <!-- nested form fields -->

               <div class="clear"  v-for="option in form.options" v-if="option.nodes && option.nodes.length!=0&&form.value&&(form.title=='Select'||form.title=='Radio' || form.title=='Text Field')">
                      <label-with-formfield :category="category" v-if="option.labels==form.value" :formType="'vertical'" :customForm="option.nodes" :person="person"></label-with-formfield>
                </div>

                <div class="clear" v-if="form.title =='Checkbox' && (form.value)" v-for="option in form.options" :key="option.id" >
                    <label-with-formfield :category="category" v-if="form.value.includes(option.labels)" :formType="'vertical'" :customForm="option.nodes" :person="person"></label-with-formfield>
                 </div>

                 <!-- nested form fields -->
               <!-- <div class="clear"  v-for="(option,index) in form.options" :key="index" v-if="form.value && form.type==='api'"> -->
                      <!-- <label-with-formfield :category="category" v-if="option.id==form.value" :formType="'vertical'" :customForm="option.nodes" :person="person"></label-with-formfield> -->
                      <!-- <label-with-formfield :category="category" v-if="option.value==form.value" :formType="'vertical'" :customForm="option.nodes" :person="person" :fetchValues="fetchValues"></label-with-formfield>
                </div> -->

                 <div class="clear" v-for="option in form.options" :key="option.id" v-if="option.nodes && option.nodes.length!=0 && form.value && form.type=='api'">
                      <label-with-formfield :category="category" v-if="option.value ==form.value || option.id ==form.value" :formType="'vertical'" :customForm="option.nodes" :person="person"></label-with-formfield>
                </div>
            </div>
        </div>

</template>
<script>
import { boolean } from "helpers/extraLogics";

export default {

  name : 'label-with-formfield',

  props: {

    /**
     * variable used to define how the form  layout should look , ie-horizntal or vertical
     */
    formType: { type: String, default: "horizantal"},

    /**
     * array of custom field which is being get through an api call
     */
    customForm: { type: Array, default: []},

    /**
     *variable used to define form is for which particular user
     ie- it would be either user,agent,admin
     */
    person: {type: String, default: ""},

    /**
     * variable used to define ,form is based on which category
     * ie - itwould be either ticketfields, userfields,organisationfields
     */
    category: {type: String,default: ""},

    /**
     *
     */
    usedby: {type: String,default: ""},

    /**
     * methodshelps to keep a watch if there is customForm has been changed ,
     * then it would return the new value
     */
    fetchValues: { type: Function, default: function() {return []},},

    displayLabel: {type: Boolean, default: true},
  },
  data() {
    return {

      language : localStorage.getItem('LANGUAGE'),

      labelClass : '',

      iconClass : '',
    };
  },

  components: {
    "ticket-form-fields": require("./TicketFormFields.vue"),
    "user-form-fields": require("./UserFormFields.vue"),
    "organisation-form-fields": require("./OrganisationFormFields.vue"),
    "tool-tip": require("components/MiniComponent/ToolTip"),
  },
  methods: {

    setNewObject(obj, index) {
      // this.fetchValues(this.customForm);
      // console.log(this.customForm, obj, index, "Setnewobject");
      this.customForm.splice(index, 1, obj);
    },

    /**
     * Checks if field is required by the person or not
     * @return {Boolean} [description]
     */
    isRequired(formData) {
      return (
        boolean(formData["required_for_" + this.person]) &&
        boolean(formData['labels_for_form_field'])
      );
    },
    /**Check if field is required to be displayed for theperson or not
     * @return {Boolean} [description]
     */
    isDisplay(value) {
      return boolean(value["display_for_" + this.person]);
    }
  },
  watch: {
    customForm(newvalue, oldval) {
      this.fetchValues(newvalue);
    }
  }
};
</script>
