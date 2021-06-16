<style scoped>
</style>
<template>
	<div>
		<faveo-box :title="lang('perform_these_actions')">
    <!-- MENU LAYOUT COMPONENT -->
      <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
             <menu-layout :list="actionList" :addButtonLabel="'add_new_action'" :listType="'action'" :category="category" :customForm="customForm" :editActionValues="editActionValues"></menu-layout>
        </div>
      </div>
    <!-- MENU LAYOUT COMPONENT END -->
		</faveo-box>
	</div>
</template>
<script>
import FaveoBox from 'components/MiniComponent/FaveoBox';

export default {
  props: {
    /**
     * vlaue helps us to dstinguish between 2 category ie:Workflow and listner
     */
    category: {
      type: String,
      default: ""
    },
    /**
     * Array of objects which is being received from the prent component and for storeing,
     * new values
     */
    actionList: {
      type: Array,
      default: null
    },
    /**
     * value for storing the custom field values
     */
    customForm: {
      type: Array,
      default: null
    },

    /**
     * Values received from the backedn when the edit api is being called
     */
    editActionValues: {
      type: Array,
      default: null
    }
  },
  data() {
    return {};
  },
  created() {
    window.eventHub.$on("updatedFieldSelect2", this.checkDisabled);
  },
  components: {
    "menu-layout": require("./MenuLayout.vue"),
		"faveo-box": FaveoBox,
  },
  methods: {
    //check disabled fields in select dropdown
    checkDisabled() {
      //remove disabled options
      $(".action_menu option").each(function(key, el) {
        $(el).removeAttr("disabled");
      });
      //check and disabled selected options
      setTimeout(() => {
        for (var i in this.actionList) {
          if (this.actionList[i].field != "") {
            $(
              ".action_menu option[value=" + this.actionList[i].field + "]"
            ).prop("disabled", "disabled");
          }
        }
        $(".action_menu")
          .select2("destroy")
          .select2();
      }, 100);
    }
  }
};
</script>
