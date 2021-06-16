<style scoped>
.field-danger {
  border-color: red;
}

</style>

<template>
  <div >
    <!-- CONTAINER -->
    <div id="header-menu" class="container-fluid">
        <div class="row">
                <div class="row margin-top-bottom-10">
                    <!-- NAME LABEL FIELD -->
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                      {{lang('name')}}<span style="color:red">*</span>
                    </div>
                    <!-- NAME LABEL FIELD END -->

                    <!-- TEXT FIELD -->
                    <div class="col-lg-10 col-col-sm-10 col-xs-12">
                      <text-field :validate="{required:true}" :fieldName="'name'" :fieldValue="obj.name"  v-on:assignToModel="setModel" :objKey="'name'" :disableField="false" unique="name"></text-field>
                    </div>
                    <!-- TEXT FIELD END -->
                </div>

                <div class="row margin-top-bottom-10">
                  <!-- STATUS SWITCH LABEL-->
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        	          {{lang('status')}}
                  </div>
                  <!-- STATUS SWITCH END -->

                  <!-- STATUS SWITCH FIELD -->
                  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                    <status-switch name="status"  :value="obj.status" :onChange="onChangeswitch" :bold="true">
                    </status-switch>
                  </div>
                  <!-- STATUS SWITCH FIELD END -->
                </div>
            </div>
        </div>
     <!-- CONTAINER END-->
</div>


</template>
<script>
import axios from "axios";

import { findObjectByKey } from "../../../helpers/extraLogics.js";
export default {
  props: ["category", "obj"],
  data() {
    return {
      source: "",
      onText: "",
      offText: "",
      descriptionValidation: {},
      targetValue: this.obj.target,
      labelStyle: {
        display: "none"
      }
    };
  },

  mounted() {
    if (this.category == "listener") {
      this.onText = "ON";
      this.offText = "OFF";
    } else {
      this.onText = "Active";
      this.offText = "Inactive";
    }
    if (this.obj.target) {
      this.getSelected(this.obj.target);
    }
  },
  components: {
    "text-field": require("../../FormFields/TextFieldWithValidation.vue"),
    "status-switch": require("components/MiniComponent/FormField/Switch"),
  },
  methods: {
    //get source
    getSelected(x) {
      this.obj.target = x.id;
    },
    //set model value
    setModel(x, y) {
      this.obj[x] = y;
    },
    onChangeswitch(value, name) {
      this.obj[name] = value;
    },
  }
};
</script>
