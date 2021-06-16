<style>
.select2-font {
  font-size: 14px;
}

.fa-warning {
  color: red;
  font-size: 14px;
}
.select2-container {
  display: inherit !important;
}

.input-group-btn {
      white-space: inherit !important;
}


.select2-container--default .select2-selection--multiple {
  border-radius: 0px !important;
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
  border-color: #3c8dbc!important;
  box-shadow: none;
}

</style>

<template>

   <div class="select2-font">
     <select v-model="selectModal" class="form-control select2-font" :name="fieldName" :id="fieldId" multiple="isMultiple" v-validate="validate"></select>
     <span v-show="errors.has(fieldName)" class="help is-danger">
      {{ errors.first(fieldName) }}
     </span>
   </div>
</template>
<script>

import { filterArrayWithKey } from "helpers/extraLogics.js";
import { mapGetters } from 'vuex';

export default {
  props: [
    "isMultiple",
    "api",
    "objKey",
    "fieldModel",
    "selectId",
    "selectionLength",
    "fieldName",
    "validate",
    "node"
  ],

  created() {
    $(() => {
      $("#" + this.fieldId).select2();
    });
  },
  data() {
    return {
      selectModal: [],
      excuteFunction: true,
    };
  },
  watch: {
    "node.value"(newvalue) {

      // NOTE: not sure why excuteFunction variable was used.
      if (this.excuteFunction) {
        let updatedValues = newvalue;
        this.bindDatafunction(updatedValues);
      } else {
        if (newvalue == "" || newvalue == undefined) {
          $(() => {
            $("#" + this.fieldId)
              .select2("destroy")
              .val("")
              .select2();
          });
        }
      }
    }
  },

  computed: {
    ...mapGetters({mode: 'getFormMode'}),

    fieldId(){
      // if mode is workflow-listner, we don't need to link organisation_department with organization
      if(this.mode == 'workflow-listener') {
        // changing the id so that it doesn't conflict in the dom when it is mounted multiple times
        return Math.random().toString(36).substring(7);
      } else {
        return this.selectId;
      }
    }
  },

  mounted() {

    this.bindDatafunction(this.node.value);

    var select2Api = window.axios.defaults.baseURL + "/" + this.api;

    $(() => {
      $("#" + this.fieldId)
        .select2({
          minimumInputLength: 1,
          maximumSelectionLength: this.selectionLength,
          placeholder: "Search/select",
          ajax: {
            url: select2Api,
            dataType: "json",
            type: "GET",
            data: term => {

              // if company is not given
              if (this.fieldId == "orgdept-id") {
                term["company[]"] = $("#org-id").val();
              }

              if(this.mode == 'workflow-listener') {
                term["company"] = 'all';
              }

              return term;
            },
            processResults: function(data) {
              return {
                results: $.map(data, function(value) {
                  return {
                    text: value.optionvalue,
                    id: value.id
                  };
                })
              };
            }
          }
        })
        .on("change", event => {
          var values = []; // copy all option values from selected
          $(event.currentTarget)
            .find("option:selected")
            .each(function(i, selected) {
              values[i] = $(selected).val();
            });
          this.selectModal = values;
          this.$emit("getModel", this.objKey, values);
        });
    });
  },

  methods: {
    bindDatafunction(data) {

      if (data != undefined && Array.isArray(data) && data.length > 0) {
        data.map(res => {
          if (res.id != undefined) {
            this.selectModal.push(res.id);
            $("#" + this.fieldId).append(
              "<option value='" +
                res.id +
                "' selected>" +
                res.name +
                "</option>"
            );
            this.$emit("getModel", this.objKey, filterArrayWithKey(data, "id"));
          }
        });
      }
    }
  }
};
</script>
