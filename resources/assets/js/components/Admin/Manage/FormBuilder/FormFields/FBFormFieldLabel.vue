<template>
  <div class="form-field-label">
    <img v-if="isChild" id="enter-arrow" width="16" height="16" :src="basePath() + imageUrl" alt="" />
    <input type="text" :class="{'field-danger': errors.has(getUniqueKey) }" :title="lang('tap_to_edit')" v-validate="{required: true}" :name="getUniqueKey" v-model.trim="fieldData.labels_for_form_field[0].label" />
    <div>
      <small v-show="errors.has(getUniqueKey)" class="help is-danger">
        {{errors.first(getUniqueKey)}}
      </small>
    </div>
  </div>
</template>

<script>

import {mapGetters} from 'vuex';

export default {

  props: {

    fieldData: {
      type: Object,
      required: true
    },

    // `true` if a node is not a root node
    isChild: {
      type: Boolean,
      default: () => false
    }

  },

  data: () => {
    return {
      imageUrl: '/themes/default/client/images/enterarrow.png'
    }
  },

  computed: {
    ...mapGetters({validationErrors: 'getValidationErrors'}),

    getUniqueKey: function() {
      return this.fieldData.title + '_' + this.fieldData.id;
    },
  },

  watch: {
    validationErrors(newValue){
      if(newValue.hasOwnProperty(this.fieldData.title)){
        this.$validator.errors.add({field: this.getUniqueKey, msg: newValue[this.getUniqueKey]});
      }
    }
  }

}
</script>

<style scoped>
.form-field-label > input {
  font-weight: 600;
  padding: 5px;
  width: 85%;
  outline: none;
  border: none;
  transition: 0.5s;
  -webkit-transition: 0.5s;
}
.form-field-label > input:focus {
  border: 1px solid #555;
}
.field-danger {
  border: 1px solid #d73925 !important;
}
</style>