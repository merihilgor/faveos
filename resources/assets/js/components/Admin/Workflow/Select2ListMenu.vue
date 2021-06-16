<style scoped>
</style>
<template>
<div>
  <select  :class="{'form-control':true,'faveo-field-danger':errors.has(name=clasName+index) }" :name="clasName+index"   v-model="field.field" class="form-control">
          <option value="">{{lang(selectName)}}</option>
          <option v-for="(field,index) in customForm" :value="field.unique" :disabled="shallDisable(field.unique)">
            <span> {{field.label}} </span>
          </option>
          <option v-for="(field,index) in listMenu1" :value="field.value" :disabled="shallDisable(field.unique)">{{lang(field.name)}}</option>
          <option v-for="(field,index) in listMenu2" :value="field.value" :disabled="shallDisable(field.unique)">{{lang(field.name)}}</option>
          <option v-for="(field,index) in listMenu3" :value="field.value" :disabled="shallDisable(field.unique)">{{lang(field.name)}}</option>
   </select>
</div>

</template>
<script>
export default {
  props: {

    field: {type: Object, default: null },

    selectName: {type: String, default: ""},

    clasName: { type: String, default: ""},

    index: { type: Number, default: null},

    customForm: { type: Array, default: null},

    listMenu1: {type: Array, default: null},

    listMenu2: {type: Array, default: null},

    listMenu3: {type: Array,default: null},

    editvalue: {type: Array,default: () => []},

    fieldChange: {type: Function,default: () => []},

    disabledFields: {type: Array,default: () => []},
  },
  data() {
    return {
      select2class: this.clasName + this.index
    };
  },
  methods: {
    shallDisable(field){
      // if the field name exists in disabled array
      return this.disabledFields.includes(field);
    }
  },
  watch: {
    "field.field"(newvalue) {
      this.fieldChange(newvalue);
    }
  },

  created() {
    $(() => {
      $("#" + this.select2class).select2();
    });
    /**
     * Here we are force mouting the select2 plugin while the edit api is being called
     */
    if (this.editvalue.length > 0) {
      $(() => {
        $("." + this.select2class).select2();
      });
    }
  },

  mounted() {
    $(() => {
      $("." + this.select2class)
        .select2()
        .on("change", event => {
          console.log(event, "events called hererere");
          var values = []; // copy all option values from selected

          $(event.currentTarget)
            .find("option:selected")
            .each((i, selected) => {
              values[i] = $(selected).val();
            });
          // this.field.field = values[0];
          this.$emit("fieldChange", values[0], this.index);

          if (this.clasName == "action_menu") {
            window.eventHub.$emit("updatedFieldSelect2");
          }
        });
    });
  }
};
</script>
