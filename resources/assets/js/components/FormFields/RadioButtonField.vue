<style scoped>

.fa-warning {
  color: red;
  font-size: 14px;
}

.form-check-input {
    position: absolute;
    margin-top: 0.45rem !important;
    margin-left: -1.25rem;
}
.radio-inline{
  padding-left: 20px !important;
}
</style>
<template>
	  <div>
      <div v-for="(option,index) in options">
  	    <label class="radio-inline">
          <input :name="unique" :data-vv-as="fieldName" v-validate="validate" v-model="fieldModel"  type="radio" :value="option.labels" :disabled="disableField" class="form-check-input">{{option.labels}}
         </label>
      </div>
      <span v-show="errors.has(unique)" class="help is-danger"></textarea>
        {{ errors.first(unique) }}
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
    options : {type: Array, required: true},
  },

  data() {
    return {
      model: this.fieldValue,
      unique: this.fieldName + "_" + new Date().getTime()
    };
  },
  watch: {
    fieldValue(newValue) {
      this.model = newValue;
    }
  },
  computed: {
    fieldModel: {
      get() {
        return this.model;
      },
      set(value) {
        this.model = value;
        this.$emit("assignToModel", this.objKey, this.model);
      }
    }
  }
};
</script>
