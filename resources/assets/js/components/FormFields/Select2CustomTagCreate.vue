<style>
.select2-font {
  font-size: 14px;
}
.fa-warning {
  color: red;
  font-size: 14px;
}
</style>
<template>
    <div :class="{'select2-font': true, 'field-danger': errors.has(unique)}">
       <select v-model="selectModal"
        class="form-control select2-font"
        :id="selectId" multiple="isMultiple" :name="unique" v-validate="validate">
       </select>
       <span v-show="errors.has(unique)" class="help is-danger">
          {{ errors.first(unique) }}
      </span>
    </div>
</template>
<script>

import {mapGetters} from 'vuex';

export default {
  props: [
    "isMultiple",
    "objKey",
    "node",
    "fieldModel",
    "selectId",
    "selectionLength",
    "fieldName",
    "validate",
    "checkCreateTag",
    "unique",
  ],
  data() {
    return {
      selectModal: [],
      excutedFunction: true,
      errorFromServer : false,
    };
  },

  computed: {
    ...mapGetters({validationErrors: 'getValidationErrors'}),
  },
  watch: {
    validationErrors(newValue){
      if(newValue.hasOwnProperty(this.unique)){
        this.$validator.errors.add({field: this.unique, msg: newValue[this.unique]});
        this.errorFromServer = true;
      }
    },

    fieldModel: {
      handler(newvalue) {

        if (typeof newvalue === "object") {

          if(this.errorFromServer){
              this.$validator.reset();
              this.errorFromServer = false;
          }

          this.selectModal = newvalue;
          if (this.excutedFunction) {
            newvalue.map(res => {
              this.appendDataToSelectTag(res, newvalue);
            });
          }
        }
      },
      immediate: true
    },

    "node.value"(newvalue) {
      if (newvalue == "") {
        // resetting the state
        this.selectModal = [];
        this.excutedFunction = true;
      }
    }
  },

  created() {
    $(() => {
      $("#" + this.selectId).select2();
    });
  },
  mounted() {
    $(() => {
      $("#" + this.selectId)
        .select2({
          minimumInputLength: 1,
          maximumSelectionLength: this.selectionLength,
          placeholder: "Add " + this.fieldName,
          tags: true,
          //create own tag with conditions
          createTag: this.getTag
        })
        .on("change", event => {
          var values = []; // copy all option values from selected
          $(event.currentTarget)
            .find("option:selected")
            .each(function(i, selected) {
              values[i] = $(selected).val();
            });
          this.selectModal = values;
          //else send parent component
          this.excutedFunction = false;
          this.$emit("getModel", this.objKey, _.uniq(values));
        });
    });
  },
  methods: {
    //create tag with condition
    getTag(params) {
      if (this.checkCreateTag) {
        var term = $.trim(params.term);
        var domain1 = "http://";
        var domain2 = "https://";

        if (
          term.indexOf(domain1.toLowerCase()) != 0 &&
          term.indexOf(domain2.toLowerCase()) != 0
        ) {
          return {
            id: term,
            text: term,
            newTag: true // add additional parameters
          };
        }
      } else {
        var term = $.trim(params.term);
        return {
          id: term,
          text: term,
          newTag: true // add additional parameters
        };
      }
    },
    appendDataToSelectTag(value, updatedValue) {
      var $newOption = $("<option></option>")
        .val(value)
        .text(value);
      $("#" + this.selectId).append($newOption);
      this.$emit("getModel", this.objKey, updatedValue);
    }
  }
};
</script>
