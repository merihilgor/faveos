<style scoped>
.fa-warning {
  color: red;
  font-size: 14px;
}
.text-field-with-validation input{
  margin-bottom : 0px;
}

</style>
<template>
      <div class="text-field-with-validation">
        <input :name="unique" v-model="fieldModel" v-validate="validate"
          :class="{'form-control': true, 'field-danger': errors.has(unique) }"
          type="text" placeholder="Enter a value" :disabled="disableField">
        <span v-show="errors.has(unique)" class="help is-danger">
          {{errors.first(unique)}}
        </span>
      </div>
</template>

<script>

import {mapGetters} from 'vuex';

export default {
  props : {

    /**
     * The unique key
     */
    unique : {type: String, required: true},

    /**
     * Name of the field
     */
    fieldName : {type : String, required: true},

    /**
     * Value of the field
     */
    fieldValue : {type : String | Number, default:''},

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

  data() {
    return {
      model: this.fieldValue,
      errorFromServer : false,
    };
  },
  mounted() {
    if (this.validate.hasOwnProperty("regex")) {
      const messages = { custom: {} };
      (messages.custom[this.unique] = {
        regex: this.validationMessage
      }),
      this.$validator.localize("en", messages);
    }
  },
  watch: {
    fieldValue(newValue) {
      this.model = newValue;
    },

    validationErrors(newValue){
      if(newValue.hasOwnProperty(this.unique)){
        this.$validator.errors.add({field: this.unique, msg: newValue[this.unique]});
        this.errorFromServer = true;
      }

    }
  },
  computed: {

    ...mapGetters({validationErrors: 'getValidationErrors'}),

    fieldModel: {
      get() {
        return this.model;
      },
      set(value) {

        if(this.errorFromServer){
          this.$validator.reset();
          this.errorFromServer = false;
        }

        this.model = value;
        this.$emit("assignToModel", this.objKey, this.model);
      }
    },
  }
};
</script>
