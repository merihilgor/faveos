<style scoped>
.listEvent {
  width: 100%;
}
label {
  margin-top: 8px;
  float: right;
}
.trash-placeement {
  position: relative;
  top: 5px;
}
</style>
<template>
	<div>
    <!-- MODAL -->
      <modal v-if="deletePopup" :showModal="deletePopup" :onClose="onCloseModal">
        <!-- SLOT TITLE -->
        <div slot="title">
          <div class="container">
            <div class="row">
              <div class="col-sm-12">
                <h4 class="text-left">{{lang('are_you_sure_you_want_to_delete')}}</h4>
              </div>
            </div>
          </div>
        </div>
        <!-- SLOT TITLE END -->

        <!-- SLOT CONTROL -->
        <div slot="controls">
             <button type="button" @click = "onSubmitDelete()" class="btn btn-success"><i :class="iconClass" aria-hidden="true"></i> Submit</button>
        </div>
        <!-- SLOT CONTROLEND -->
      </modal>
      <!-- MODAL END -->

      <!-- DELETE BUTTON -->
      <div class=" pull-left margin-top-8" :title="'Delete Action'">
        <i  :id="'delete-rule-button-'+index" class="fa fa-trash faveo-trash trash-placeement" @click="deleteEvent(event.id,index)">
        </i>
      </div>
      <!-- DELETE BUTTON END -->

      <!-- SELECT COMPONET -->
	    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 margin-top-8">
			  <select2-list :listMenu1='eventList' :listMenu2='[]' :listMenu3='[]' :selectName="'select_event'" :field="event" :clasName="'event_menu'" v-on:fieldChange="fieldChanged"  :index="index" :customForm="customForm"></select2-list>
      </div>
      <!-- SELECT COMPONET END -->

      <!-- SHOWEVENTS -->
      <!-- SHOWEVENTS END -->
      <div v-if="showEvents">
          <!-- FROM -->
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 margin-top-8">
                  <label>{{lang('from')}}</label>
          </div>
          <!-- FROM END -->
          <!-- <div> -->

          <!-- FROM EVENT -->
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 margin-top-8">
              <select id="start" class="form-control" :ref="'old-'+index" v-model="event.from" @change="setEventValue(event.from,'new-'+index)">
                  <option v-for="(evento,index) in events" :value="evento.id" :key="index">{{evento.name}}</option>
              </select>
          </div>
          <!-- FROM EVENT END -->

            <!-- TO  -->
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 margin-top-8">
                   <label>{{lang('to')}}</label>
            </div>
            <!-- TO  END -->

            <!-- TO EVENT END -->
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 margin-top-8">
              <select id="end"  class="form-control" :ref="'new-'+index" v-model="event.to" @change="setEventValue(event.to,'old-'+index)">
                  <option v-for="(evento,index) in events"
                    :selected="evento.name == 'Any' ? 'selected': false"
                    :value="evento.id"
                    :key="index">
                    {{evento.name}}
                  </option>
              </select>
            </div>
            <!-- TO EVENT END END -->
        </div>
     </div>
</template>
<script>

import {errorHandler, successHandler} from 'helpers/responseHandler';
import {mapGetters} from 'vuex';

export default {
  props: ["event", "index", "obj", "list"],
  data() {
    return {
      eventList: [
        { name: "priority_is_changed", value: "priority_id" },
        { name: "type_is_changed", value: "type_id" },
        { name: "status_is_changed", value: "status_id" },
        { name: "helptopic_is_changed", value: "help_topic_id" },
        { name: "department_is_changed", value: "department_id" },
        { name: "agent_is_changed", value: "assigned_id" },
        { name: "note_is_added", value: "note" },
        { name: "reply_is_sent", value: "reply" },
        { name: "duedate_is_changed", value: "duedate" }
      ],
      anyEvent: "",
      showEvents: false,
      events: [],
      showReply: false,
      customForm: [],
      deletePopup: false,

      iconClass: "fa fa-save",

      eventIdValue: ""
    };
  },
  components: {
    "select2-list": require("./Select2ListMenu.vue"),
    modal: require("../../Common/Modal.vue")
  },
  created() {
    window.eventHub.$on("changeEventReply", this.changeReply);
  },

  watch: {
    field(newvalue) {
      // console.log("VALUES IS BEING CANGES");
    }
  },
  computed: {
    ...mapGetters(['getDependency']),

    field() {
      this.switchField(this.event.field);
    }
  },
  methods: {

    /**
     * Delete Event Helps to delete an event from an array
     * @param {String} idvalue
     * @param  {Number} index
     * @returns {void}
     */
    deleteEvent(idvalue, index) {
      this.deletePopup = true;
      if (idvalue != "" && idvalue != null) {
        this.eventIdValue = idvalue;
      }
    },

    onSubmitDelete() {
      if (this.eventIdValue) {
        axios.delete("api/delete-enforcer/event/" + this.eventIdValue)
          .then(res => {
            successHandler(res, "workflow");
            this.list.splice(this.index, 1);
            this.onCloseModal();
          })
          .catch(res => {
            errorHandler(res, "workflow");
          })
          .finally(res => {
            this.deletePopup = false;
          });
      } else {
        this.list.splice(this.index, 1);
        this.onCloseModal();
      }
    },

    onCloseModal() {
      this.deletePopup = false;
    },

    /**
     * Field Change is called only when the the field of the select box is being changed,
     * @param {String }field
     */
    fieldChanged(field) {
      this.showEvents = false;
      this.showReply = false;
      this.event.field = field;
      this.event.from = 0;
      this.event.to = 0;
    },

    /**
     * switch method is used to call either the its dependencey api or to showthe customfields,
     * for eg if field is equal to status_id then the dependency api is called and action value is
     * set to true
     * @param {String} field
     * @returns {void }
     */
    switchField(field) {
      switch (field) {
        case "priority_id":
          this.getEventOptions("priorities");
          this.showEvents = true;
          this.anyEvent = "Any";
          break;
        case "type_id":
          this.getEventOptions("types");
          this.showEvents = true;
          this.anyEvent = "Any";
          break;
        case "status_id":
          this.getEventOptions("statuses");
          this.showEvents = true;
          this.anyEvent = "Any";
          break;
        case "help_topic_id":
          this.getEventOptions("help-topics");
          this.showEvents = true;
          this.anyEvent = "Any";
          break;
        case "assigned_id":
          this.getEventOptions("agents");
          this.showEvents = true;
          this.anyEvent = "Any";
          break;
        case "department_id":
          this.getEventOptions("departments");
          this.showEvents = true;
          this.anyEvent = "Any";
          break;
        case "note":
          this.event.value = "added";
          this.event.from = "";
          this.event.to = "";
          break;
        case "duedate":
          this.event.value = "changed";
          this.event.from = "";
          this.event.to = "";
          break;
        case "reply":
          this.showReply = true;
          if (this.obj.triggered_by == "agent") {
            this.event.value = "support";
          } else if (this.obj.triggered_by == "requester") {
            this.event.value = "requester";
          } else if (this.obj.triggered_by == "agent_requester") {
            this.event.value = "support_requester";
            this.showReply = false;
          }
          break;
        default:
          break;
      }
    },

    //change event reply
    changeReply(performer) {
      if (this.event.field == "reply") {
        if (performer == "agent") {
          this.event.value = "support";
          this.showReply = true;
        } else if (performer == "requester") {
          this.event.value = "requester";
          this.showReply = true;
        } else {
          this.event.value = "support_requester";
          this.showReply = false;
        }
      }
    },
    //change event old/new values
    setEventValue(value, el) {

      // var element = this.$refs[el];
      // var $otherSelect = $(element);
      // if (value != "0") {
      //   $otherSelect.find("option").show()
      //   .filter(function() {
      //     return this.value == value;
      //   }).hide();
      //
      // } else {
      //   $otherSelect.find("option").show();
      // }
    },

    /**
     * method helps to get the result of the dependency passed to it,
     * @param {String} dependency,
     * @returns {void}
     */
    getEventOptions(dependency) {
      this.events = _.cloneDeep(this.getDependency(dependency));
      this.events.unshift({id: 0, name:'Any'});
    }
  }
};
</script>
