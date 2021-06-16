<style scoped>
.title {
  margin-bottom: 10px;
  font-size: 21px;
  font-weight: 200;
  line-height: 24px;
}
.top-space {
  margin-top: 25px;
}
</style>
<template>
	    <div>
        <faveo-box :title="lang('involves_any_of_these_events')">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <menu-layout :list="eventList" :addButtonLabel="'add_new_event'" :listType="'event'" :obj="obj">
  		        </menu-layout>
            </div>
          </div>
        </faveo-box>
		</div>
</template>
<script>
import FaveoBox from 'components/MiniComponent/FaveoBox';

export default {
  props: {
    /**
     * Aray of event which we are receving form theparent components
     */
    eventList: {
      type: Array,
      default: null
    },
    /**
     * Complete Object Data
     */
    obj: {
      type: Object,
      default: null
    }
  },
  components: {
    "menu-layout": require("./MenuLayout.vue"),
    "faveo-box": FaveoBox,
  }
};
</script>
