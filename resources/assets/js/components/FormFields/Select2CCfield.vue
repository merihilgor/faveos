<style scope>
.fa-warning {
  color: red;
  font-size: 14px;
}
</style>
<template>
	<div>
	  <select  class="form-control" :id="selectId" :name="fieldName" multiple="true" v-model="selectModal" v-validate="validate" :disabled="disableField" ></select>
	  <span v-show="errors.has(fieldName)" class="help is-danger">
      {{ errors.first(fieldName) }}
    </span>
	</div>
</template>
<script>
import { filterArrayWithKey, boolean } from "../../helpers/extraLogics.js";
export default {
  props: [
    "api",
    "objKey",
    "checkCreateTag",
    "selectId",
    "selectionLength",
    "holder",
    "fieldName",
    "validate",
    "purpose",
    "node",
    "ccValues",
    "disableField",
    "dependency",
  ],
  data() {
    return {
      selectModal: [],
      showccValue: [],
      stopWatch: false
    };
  },

  created() {
    $(() => {
      $("#" + this.selectId).select2();
    });
  },
  watch: {
    "node.value"(newvalue) {
      this.initialiseSelectField(newvalue)
    }
  },

  mounted() {
    this.initialiseSelectField(this.node.value)

    //select2 initialize
    var select2Api =
      window.axios.defaults.baseURL + "/api/dependency/" + this.dependency +"?meta=true";
    $(() => {
      $("#" + this.selectId)
        .select2({
          minimumInputLength: 1,
          maximumSelectionLength: this.selectionLength,
          placeholder: "Search/select a " + this.holder,
          tags: true,
          ajax: {
            url: select2Api,
            dataType: "json",
            type: "GET",
            data: function(term) {
              // console.log(term, "terms value values");
              return term;
            },
            processResults: data => {
              return {
                results: $.map(data.data[this.dependency], value => {
                  //checking for agent dropdown/cc dropdown
                  if (this.holder == "requester cc") {
                    value["id"] = value.id;
                  }
                  if (value.name != "") {
                    return {
                      image: value.profile_pic,
                      text: value.name,
                      id: value.id,
                      email: value.email
                    };
                  } else {
                    return {
                      image: value.profile_pic,
                      text: value.email,
                      id: value.id,
                      email: value.email
                    };
                  }
                })
              };
            },
            cache: true
          },
          //create own tag with conditions
          createTag: this.getTag,
          //getting dropdown options with profile picture
          templateResult: this.templateState
        })
        .on("change", event => {
          console.log(event, "event changed");

          var values = []; // copy all option values from selected
          $(event.currentTarget)
            .find("option:selected")
            .each(function(i, selected) {
              values[i] = $(selected).val();
              // console.log(selected, "CCCC VALUES");
            });
          this.selectModal = values;
          //if cc means pass values to createtocket component
          this.stopWatch = true;
          if (this.holder == "requester cc") {
            // window.Event = new Vue();
            // window.eventHub.$emit("UpdateCC", values);
            this.$emit("getModel", this.objKey, this.selectModal);
          } else {
            //else send parent component
            this.$emit("getModel", this.objKey, this.selectModal);
          }
        });
    });
  },
  methods: {

    initialiseSelectField(newvalue){
        if (typeof newvalue === "object" && this.holder !== "requester cc") {
          this.selectModal.push(newvalue.id);

          $("#" + this.selectId).append(
            "<option value='" + newvalue.id +"' selected>" + newvalue.name +"</option>"
          );

          this.$emit("getModel", this.objKey, this.selectModal);

        } else if (newvalue == "") {

          $("#" + this.selectId).html("");

        } else {

          // only if value is diffrent, bindCC has to be called, to avoid infinite loop of object mutation
          if (!this.stopWatch && (this.showccValue != newvalue)) {
            this.showccValue = newvalue;
            this.bindCC(this.showccValue);
          }
        }
    },

    bindCC(data) {
      if (data != undefined && data.constructor === Array) {
        data.map(res => {
          if (res !== undefined && res !== null && boolean(res.id)) {
            this.selectModal.push(res.id);
            $("#" + this.selectId).append(
              "<option value='" +
                res.id +
                "' selected>" +
                res.name +
                "</option>"
            );
          }
        });
        if(boolean(this.selectModal)){
          this.$emit("getModel", this.objKey, this.selectModal);
        }
      }
    },
    //show dropdown menu
    templateState(state) {
      if (!state.id) {
        return state.text;
      }
      if (state.image) {
        var $state = $(
          '<li><div><div style="width: 8%;display: inline-block;"><img src=' +
            state.image +
            ' width="35px" height="35px" style="vertical-align:bottom"></div><div style="width: 90%;display: inline-block;"><b style="font-weight:600;font-size:13px">' +
            state.text +
            '</b><p style="margin:0px">' +
            state.email +
            "</p></div></div></li>"
        );
        return $state;
      } else {
        var $state = $(
          '<div style="width: 90%;display: inline-block;"><b style="font-weight:600;font-size:13px">' +
            state.text +
            "</b></div>"
        );
        return $state;
      }
    },
    //create tag with condition
    getTag(params) {
      if (this.checkCreateTag) {
        //add new cc with validation
        var term = $.trim(params.term);
        function validateEmail(term) {
          var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return re.test(term);
        }
        if (validateEmail(term)) {
          return {
            id: term,
            text: term,
            newTag: true // add additional parameters
          };
        }
      } else {
        return null;
      }
    }
  }
};
</script>
