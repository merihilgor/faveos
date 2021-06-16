<style scoped>

</style>
<template>
    <faveo-box :title="lang('when_an_action_performed_by')">

          <!-- ACTION PERFORMED BY AGENT -->
            <div class="col col-md-3">
               <input type="radio" name="action_performed" v-model="obj.triggered_by" value="agent" @change="actionMade(obj.triggered_by)">&nbsp;{{lang('agent')}}
            </div>
         <!-- ACTION PERFORMED BY AGENT END -->

         <!-- ACTION PERFORMMED BY REQUESTOR -->
            <div class="col col-md-3">
                <input type="radio" name="action_performed" value="requester" v-model="obj.triggered_by" @change="actionMade(obj.triggered_by)">&nbsp;{{lang('requester')}}
              </div>
         <!-- ACTION PERFORMMED BY REQUESTOR END -->

         <!-- ACTION PERFORMED BY AGENT -->
            <div class="col col-md-3">
              <input type="radio" name="action_performed" value="agent_requester" v-model="obj.triggered_by" @change="actionMade(obj.triggered_by)">&nbsp;{{lang('agent_or_requester')}}
            </div>

  </faveo-box>
</template>
<script>
import FaveoBox from 'components/MiniComponent/FaveoBox';

export default {
  props: ["obj"],
  data() {
    return {
      performed_by: ""
    };
  },
  methods: {
    //action perfomed changes
    actionMade(x) {
      window.eventHub.$emit("changeEventReply", x);
    }
  },
  components: {
    "faveo-box": FaveoBox,
  }
};
</script>
