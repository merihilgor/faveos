<style scoped>
.fa-warning {
  color: red;
  font-size: 14px;
}
</style>
<template>
	<div>

    <ckeditor 
      tag-name="textarea"
      v-model="model"
      v-validate="validate"
      :name="fieldName" 
      id="reply_content" 
      :editor="editor" 
      :config="editorConfig" 
      v-on:input="getEditorValue(model, fieldName)"
    >
    </ckeditor>

    <span v-show="errors.has(fieldName)" class="help is-danger"> {{ errors.first(fieldName) }}</span>
	</div>
</template>
<script>
// import { Ckeditor } from "../directive/ckeditor.js";
export default {
  props: ["fieldName", "validate", "fieldValue", "objKey", "node"],

  data() {
    return {

      model: this.fieldValue,
      
      csrf : '',

      editor: ClassicEditor,
  
      editorConfig: {

        ckfinder: {

          uploadUrl: ''
        },

        language: 'en',
      }
    };
  },

  created() {

    this.getValues();
  },

  mounted() {

    this.model = this.fieldValue;
  },

  watch: {
    fieldValue(newvalue) {
     this.model = newvalue;
    },
    "node.value"(newvalue) {
      if (newvalue == "") {
        this.model = '';
      }
    }
  },
  methods: {

    getValues(){

       this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

       this.editorConfig.ckfinder.uploadUrl = this.basePath()+'/img_upload?_token='+this.csrf;

    },

    //get editor value
    getEditorValue(x) {
      this.$emit("assignToModel", this.objKey, x);
    }
  }
};
</script>
<style>
  .ck.ck-editor__main>.ck-editor__editable {
    border-color: var(--ck-color-base-border);
    min-height: 200px;
    max-height: 200px;
  }

  .table{
    overflow-x: auto;
  }

  figure>table,figure>table td,figure>table th {  
    border: 1px solid #ddd;
    text-align: left;
  }

  table {
    border-collapse: collapse;
    width: 100%;
  }

  figure>table th, figure>table td {
    padding: 15px;
  }
</style>