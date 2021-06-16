<style scoped>

.select-field-with-validation select{
  margin-bottom: 0px !important;
}

</style>
<template>
	    <div class="select-field-with-validation">

            <select :name="fieldName" v-model="fieldModel" v-validate="validate" :class="{'form-control': true, 'field-danger': errors.has(fieldName) }" :disabled="disableField">
                   <option value="">Select {{fieldName}}</option>
                   <option v-if="!boolean(apiEndpoint) && valueFor=='label'" v-for="option in options" :value="option.labels">{{option.labels}}</option>
                   <option v-if="!boolean(apiEndpoint) && valueFor=='id_with_name'" v-for="option in options" :value="option.id">{{option.name}}</option>

                   <!-- this can be modified to take custom keys -->
                   <option v-if="boolean(apiEndpoint)" v-for="option in availableOptions" :value="option.id">{{option.name}}</option>
            </select>

            <span v-show="errors.has(fieldName)" class="help is-danger">
              {{ errors.first(fieldName) }}
            </span>
        </div>
</template>
<script>

import axios from 'axios';
import {boolean} from 'helpers/extraLogics';

export default {

  props : {
    /**
     * Name of the field
     */
    fieldName : {type : String, required: true},

    /**
     * Value of the field
     */
    fieldValue : {type : String|Object, default:''},

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
     * If API endpoint is present, options will be obtained from there instead of passed options
     */
    apiEndpoint : {type: String, default :''},

    /**
     * Options that are required to be visible here
     * NOTE: Option should be passed in format [{id:1, labels: 'test_label' when valueFor is label,}]
     */
    options : {type : Array, default : ()=>[]},

    /**
     * key name for which value has to be updated.
     * NOTE : it can have values either `label` or `id_with_name`
     * For eg. {id: 1, name:'test'}, if valueFor is id_with_name, then value will be one.
     *  {id: 1, labels: 'test'} and valueFor is label, value will be test
     */
    valueFor:{type: String, default: 'label'},
  },

  data() {
    return {
      model: this.fieldValue,
      linking: true,
      dontEmit: true,
      availableOptions : this.options
    };
  },

  mounted(){
    //updating options prop if required
    this.updateOptions();
  },

  methods: {
    updateOptions(){
      if(boolean(this.apiEndpoint)){
        axios.get(this.apiEndpoint).then(res => {
          if(!this.validateApiResponse(res.data)){
            throw "invalid API response";
          }
          this.availableOptions = res.data;
        }).catch(err => {
          this.availableOptions = [];
        })
      }
    },

    /**
     * Validates API response for the format that is acceptable by the component
     * @param  {Array} list
     * @return {Boolean}
     */
    validateApiResponse(list){
      if (list.constructor !== Array){
        return false;
      }

      for(let i=0; i < list.length; i++){
        if(!list[i].hasOwnProperty('id') || !list[i].hasOwnProperty('name')){
          return false;
        }
      }
      return true;
    }

  },

  watch: {
    fieldValue(newValue) {
      if (typeof newValue === "object") {
        this.model = newValue.id;
      } else {
        this.model = newValue;
      }
      if (this.dontEmit) {
        this.$emit("assignToModel", this.objKey, this.model);
      }
    },
  },
  computed: {

    fieldModel: {
      get() {
        return this.model;
      },
      set(value) {
        this.model = value;
        this.dontEmit = false;
        this.$emit("assignToModel", this.objKey, this.model);
      }
    }
  }
};
</script>
