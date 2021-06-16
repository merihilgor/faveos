<style scoped>
  .fa-warning{
     color: red;
     font-size: 14px;
  }

  .text-area-with-validation textarea {
    margin-bottom: 0px;
  }

</style>
<template>
      <div class="text-area-with-validation">
            <textarea :name="fieldName" v-model="fieldModel" v-validate="validate" :class="{'form-control': true, 'field-danger': errors.has(fieldName) }"  placeholder="Enter a value" :disabled="disableField"></textarea>
            <span v-show="errors.has(fieldName)" class="help is-danger">
              {{ errors.first(fieldName) }}
            </span>
      </div>
</template>

<script>
export default {
  props : {
    /**
     * Name of the field
     */
    fieldName : {type : String, required: true},

    /**
     * Value of the field
     */
    fieldValue : {type : String, default:''},

    /**
     * Object for validation
     */
    validate : {type : Object, required: true},

    /**
     * Key for which value has to be assigned in form field object
     */
    objKey : {type: String, required: true},

    /**
     * If field has to be disabled
     */
    disableField : {type: Boolean, default: false},

    /**
     * Validation message that has to be passed with regex validation
     */
    validationMessage : {type: String|Array, default: ''},
  },

  data () {
    return {
        model:this.fieldValue,
    }
  },
  watch:{
     fieldValue(newValue){
          this.model=newValue;
     }
  },
  mounted(){
      if(this.validate.hasOwnProperty('regex')){
          const messages={custom:{}};
          messages.custom[this.fieldName]={
                regex: this.validationMessage,
          },
          this.$validator.localize('en', messages);
      }
  },
  computed:{
        fieldModel:{
            get(){
                return this.model;
            },
            set(value){
                this.model=value;
                this.$emit('assignToModel',this.objKey,this.model);
            }
        }
  },
}
</script>
