<style scoped>
.input-group {
  width: 100%;
}

.input-group input{
  width: 101%;
}
</style>
<template>
  <div class="input-group">
        <form-dynamic-select v-on:getValue="getValue" :api="api" holder="requester"  :reference="node.title" :objKey="'value'" :node="node" :validate="validate"></form-dynamic-select>

        <div class="input-group-btn" style="vertical-align: top;">
            <add-new-requester v-on:setRequester="selectRequester" :person="person" labelKey="create_user"></add-new-requester>
        </div>
  </div>
</template>
<script>

export default {
  props: {

    api : {type : String, required: true},

    /**
     * Node is an object specific to its customfield which is being fetched through the formarray.
     * @type {Object}
     */
    node: { type: Object, default: null },

    /**
     *person is to check wether it is a user or agent
     * @type {String}
     */
    person: { type: String, default: null },

    /**
     * validation object
     * @type {Object}
     */
    validate: {type: Object, default: null}
  },

  components: {
    "add-new-requester": require("./NewRequester.vue"),
    "form-dynamic-select": require("../Agent/tickets/filters/FormDynamicSelect.vue"),
  },

  methods: {

    /**
     * selectRequester is used for the selection of the value present in an requestorEmail Array and
     * while submitting send only username
     */
    selectRequester(x) {
        // user object in dependency api doesn't have a thing called name. So we are replacing it with meta name
        x.name = x.meta_name;
        this.node.value = x;
    },

    getValue(x) {
      this.$emit("getValue", x);
    }
  }
};
</script>

<style>

.input-group-btn{
  font-size: inherit;
}
.modal-dialog{
  width : 800px !important;
}
</style>
