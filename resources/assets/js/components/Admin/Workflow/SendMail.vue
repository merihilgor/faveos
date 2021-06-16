<style scoped>
.sendEmail {
  margin-top: 30px;
  margin-bottom: 20px;
}
</style>
<template>
    <div class="col-sm-12 sendEmail" style="margin-top: 30px;">
            <div v-if="action.field=='mail_agent'">
                <div class="col-sm-2">
                    <label>{{lang('agent')}}<span style="color:red">*</span></label>
                </div>
                <div class="col-sm-10">
                    <!-- <div class="col-sm-10">
                        <select class="" :class="{'form-control':true,'faveo-field-danger':errors.has(name='Agent') }" name="Agent" v-model="action.action_email.user_ids" v-validate="{required:true}">
                            <option value="">Select agent</option>
                            <option v-for="(agents,index) in agentList" :key="index" :value="agents.id">{{agents.user_name}}</option>
                        </select>
                    <div v-show="errors.has(name='Agent')" class="help faveo-danger-text col-sm-12">
                        <i v-show="errors.has(name='Agent')" class=""></i> {{ errors.first(name='Agent') }}
                    </div>
                    </div> -->
                    <dynamic-select   :label="'Agent'"  :multiple="true" :name="'agent-' + index" :labelStyle='labelStyle'   
                            classname="col-xs-10" apiEndpoint="/api/dependency/agents"   :value="users" :onChange="onAgentChange">
                    </dynamic-select>
                    
                </div>
            </div>
            <div class="col-sm-2">
                <label>Subject<span style="color:red">*</span></label>
            </div>
            <div class="col-sm-10">
                <div class="col-sm-10">
                    <input type="text" class="m-clear" :class="{'form-control':true , 'faveo-field-danger':errors.has(name='subject')}" name="subject"  placeholder="Enter a subject" v-model="action.action_email.subject" v-validate="{required:true}">
                </div>
                 <div v-show="errors.has(name='subject')" class="help faveo-danger-text col-sm-12">
                        <i v-show="errors.has(name='subject')" class=""></i> {{ errors.first(name='subject') }}
                </div>
            </div>
            <div class="col-sm-2">
                <label>Body<span style="color:red">*</span></label>
            </div>
            <div class="col-sm-10">
                <div class="col-sm-10">
                    <!-- <pre>{{ errors.has(body) }}</pre> -->
                    <textarea  :class="{'form-control':true}" name="body" v-model="action.action_email.body"  v-ckeditor="getEditorValue" ></textarea>
                     <div v-show="errors.has(name='body')" class="help faveo-danger-text col-sm-12">
                        <i v-show="errors.has(name='body')" class=""></i> {{ errors.first(name='body') }}
                    </div>
                </div>
            </div>
        </div>
</template>
<script>
import { Ckeditor } from "../../directive/ckeditor.js";
import { extractOnlyId } from "../../../helpers/extraLogics.js";
export default {
  props: ["action", "index"],
  mounted() {
    if (this.action.field == "mail_agent") {
      axios.get("api/dependency/" + "agents").then(res => {
        this.agentList = res.data.data.agents;
      });
    }
  },
  data() {
    return {
      agentList: [],
      //   body: this.action.action_email.body,
      name: "",
      users: this.action.action_email.users
        ? this.action.action_email.users
        : this.action.action_email.user_ids,
      labelStyle: {
        display: "none"
      }
    };
  },
  directives: {
    Ckeditor
  },
  methods: {
    getEditorValue(x) {
      this.action.action_email.body = x;
    },

    onAgentChange(value, name) {
      this.action.action_email.user_ids = extractOnlyId(value);
      this.action.action_email.users = value;
    }
  },
  components: {
    "dynamic-select": require("../../MiniComponent/FormField/DynamicSelect.vue")
  }
};
</script>
