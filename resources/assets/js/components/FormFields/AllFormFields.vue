<template>
	  <div>
          <!-- text field with validation-->
          <text-field  v-if="node.type=='text'||node.type=='number'||node.type=='date'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue"  v-on:assignToModel="setModel" :objKey="'value'" :disableField="false" :validationMessage="node.labels_for_validation"></text-field>
          <!-- text area with validation -->
          <text-area v-if="node.type=='textarea'&&node.title!='Description'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :validationMessage="node.labels_for_validation"></text-area>
          <!-- select with validation -->
          <select-field v-if="node.type=='select'&&node.default==0 && node.title!='Api'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options"></select-field>
          <!-- select field with api search -->
          <select-field-with-api v-if="(node.type=='select'||node.type=='multiselect')&&node.default&&node.title!='Captcha'&&node.title!='Assigned'"  v-on:getValue="setModel" :api="Api" :holder="fieldName" :reference="node.title"></select-field-with-api>
          <!-- checkbox field  -->
          <checkbox-field v-if="node.type=='checkbox'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options"></checkbox-field>
          <!-- radio button -->
          <radio-field  v-if="node.type=='radio'" :validate="validation" :fieldName="fieldName" :fieldValue="fieldValue" v-on:assignToModel="setModel" :options="node.options"></radio-field>
          <!-- recaptcha field -->
          <recaptcha-field v-if="recaptchaDisplay"></recaptcha-field>
          <!-- media gallery attachment -->
          <media-gallery v-if="node.title=='Description'&&node.media_option"></media-gallery>
          <!-- description ckeditor -->
          <ckeditor v-if="node.title=='Description'" :editorWidth="'100%'"></ckeditor>
          <!-- ticket requester component only for agent/usr ticket create -->
          <agent-requester v-if="node.title=='Requester'&&person=='agent'"  :node="node"></agent-requester>
          <!-- user requester -->
          <user-requester  v-if="node.title=='Requester'&&person=='user'" :node="node"></user-requester>
          <!-- file-upload -->
          <file-upload v-if="node.title=='Attachments'" :isMultiple="true"></file-upload>
	  </div>
</template>
<script>	
    export default{
    	props:['node','person'],
    	data(){
    		return{
               recaptchaDisplay:false,
               mediaOption:false,
    		}
    	},
    	mounted(){
            //captch fields showing
            if(this.node.title=='Captcha'&&this.node['display_for_'+this.person]&&(this.person=='agent'||this.person=='user')){
            	this.recaptchaDisplay=true;
            }
            //set media option enable or disable
            if(this.node.title=='Description'&&this.node.media_option&&this.person=='agent'){
                this.mediaOption=true;
            }
    	},
    	computed:{
    		//validation obj
    	   validation(){
    	   	   var obj={};
               if(this.node.type=='text'||(this.node.type=='textarea'&& this.node.title!='Description')){
               	    if(this.node.pattern!=""&&this.node.pattern!=null){
               	   	  obj['regex']=this.node.pattern;
               	    }
               	    obj['required']=this.node['required_for_'+this.person];
               }
               else if(this.node.type=='number'){
               	   	obj['regex']='^([0-9]+)$';
               	    obj['required']=this.node['required_for_'+this.person];
               }
               else if(this.node.type=='date'){
               	    obj['date_format']='DD/MM/YYYY';
               	    obj['required']=this.node['required_for_'+this.person];
               }
               else{
               	   obj['required']=this.node['required_for_'+this.person];
               }
               return obj;
    	    },
    	    fieldName()
    	    	return this.node['label'];
    	    },
    	    fieldValue(){
            if(!this.node.hasOwnProperty('value')){
                  this.node['value']="";
            }
    	    	return this.node.value;
    	    },
          //set api for default select menus
          Api(){
              if(this.node.title=="Status"){
                  return "statuses";
              }
              else if(this.node.title=="Priority"){
                  return "priorities";
              }
              else if(this.node.title=="Location"){
                  return "locations";
              }
              else if(this.node.title=="Type"){
                  return "types";
              }
              else if(this.node.title=="Department"){
                  return "departments";
              }
              else if(this.node.title=="Help Topic"){
                  return "help-topics";
              }
          }
    	},
    	components: {
               'text-field': require('./TextFieldWithValidation.vue'),
               'text-area': require('./TextAreaWithValidation.vue'),
               'select-field': require('./SelectFieldWithValidation.vue'),
               'select-field-with-api': require('../Agent/tickets/filters/FormDynamicSelect.vue'),
               'checkbox-field': require('./CheckboxField.vue'),
               'radio-field': require('./RadioButtonField.vue'),
               'recaptcha-field':require('./RecaptchaField.vue'),
               'agent-requester':require('./AgentRequester.vue'),
               'ckeditor': require('../Common/CkeditorNormal.vue'),
               'media-gallery':require('../Common/MediaGallery.vue'),
               'user-requester':require('./UserRequester.vue'),
               'file-upload':require('./FileUpload.vue'),
    	},
    	methods:{
    		//set model value
             setModel(x,y){
                this.node[x]=y;
             },
    	}
    }
</script>