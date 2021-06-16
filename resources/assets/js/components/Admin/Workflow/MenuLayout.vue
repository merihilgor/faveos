<style scoped>
.unoreder-list {
  background-color: #fff;
  border: 1px solid gainsboro;
  width: 100%;
  margin-top: 10px;
  list-style-type: none;
}
.trash-placement {
  top: 6px;
}

.mtb15 {
  /* margin: 15px 0px; */
}
.row {
  margin: 10px 0px;
}

.inline-block {
  display: inline-block;
}
#add_btn{
  margin-top: 10px;
}
</style>
<template>
	<div>
    <div class="">

      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!-- MENU COMPONENTS -->
            <!-- for new elements, id is null and for exuisting elements key is undefined, so if key is undeinfed we check for id-->
            <div class="col-sm-12" v-for="(item, index) in list" :key="item.key != undefined ? item.key : item.id">
                <!-- event list with function -->
                <event-list v-if="listType=='event'" :event='item' :index="index" :category="category" :list="list" :obj="obj"></event-list>
                <!-- rule list with function -->
                <rule-list v-if="listType=='rule'" :rule="item" :index="index" :customForm="customForm" :list="list" :category="category" :editRuleValues="editRuleValues"></rule-list>
                <!-- action list with function -->
                <action-list v-if="listType=='action'" :action="item" :index="index" :category="category" :list="list" :customForm="customForm" :editActionValues="editActionValues"></action-list>
            </div>
            <!-- MENU COMPONENTS END -->
        </div>
        <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12" id="add_btn">
          <button  @click="addNewCondtion(listType)" :title="'Add New' + listType" type="button" class="btn btn-default form-control"><i class="fa fa-plus" aria-hidden="true"></i>  &nbsp; Add New {{listType}}</button>
      </div>
      </div>
    </div>




    </div>
</template>
<script>

export default {
  props: {
    /**
     * Array of object which are sent as per the listtype
     * ie if listType is equal to rule the ruleList from  parent component(AllMenu.vue) is received ,
     * and passed ahead
     */
    list: {
      type: Array,
      default: null
    },

    /**
     * Add button text
     */
    addButtonLabel: {
      type: String,
      default: "add_new_connection"
    },
    /**
     * ListType is which task need to be performed there are 3 task ie: rule,action,events
     */
    listType: {
      type: String,
      default: ""
    },
    /**
     * Object which contains the whole Data
     */
    obj: {
      type: Object,
      default: null
    },
    /**
     * Variable for the custom form fields
     */
    customForm: {
      type: Array,
      default: null
    },

    /**
     * edit Data for the Action menu component , these data is received from the backend when the edit,
     * api is being called
     */
    editActionValues: {
      type: Array,
      default: null
    },
    /**
     *edit Data for the Rule menu component , these data is received from the backend when the edit,
     * api is being called
     */
    editRuleValues: {
      type: Array,
      default: null
    },

    /**
     *
     */
    category: {
      type: String,
      require: true
    }
  },
  data() {
    return {
      formFields: [],
    };
  },
  mounted() {

  },
  components: {
    "event-list": require("./EventListWithFunction.vue"),
    "rule-list": require("./RuleListWithFunction.vue"),
    "action-list": require("./ActionListWithFunction.vue")
  },
  methods: {

    getUniqueKey(){
        return Math.random().toString(36).substring(7);
    },

    /**
     * Add New rule/action/event
     * method helps us to add the new rule/action/event on click o the button
     * @param {String} type helps us to distingush between diffrenet listTypes
     * @returns {void}
     */
    addNewCondtion(type) {
      let obj = {};
      obj["id"] = null;
      // so that each element can be unique
      obj["key"] = this.getUniqueKey();
      obj["field"] = "";
      obj["value"] = "";
      obj[type + "s"] = [];
      if (type === "rule") {
        obj["category"] = "ticket";
      }
      if (type === "event") {
        obj["from"] = 0;
        obj["to"] = 0;
      }
      if (this.listType == "action") {
        obj["action_email"] = {
          id: null,
          subject: "",
          body: "",
          user_ids: []
        };
      }
      this.list.push(obj);
      if (this.listType == "action") {
        window.eventHub.$emit("updatedFieldSelect2");
      }
    },

    getRandomKey(){
      return Math.random().toString().replace('0.', '');
    },

    //after dropped
    handleDrop(data) {
      const { index, list, item } = data;
      //copy old id
      let id = item.id;
      //assign null to id
      // why i am assign null to id here?because when you drop new object to array it will make duplicate error..
      item.id = null;
      list.splice(index, 0, item);
      //replace old id
      setTimeout(() => {
        for (var i in list) {
          if (list[i].id == null) {
            list[i].id = id;
          }
        }
      }, 100);
    }
  }
};
</script>
