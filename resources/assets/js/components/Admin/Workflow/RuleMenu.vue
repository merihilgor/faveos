<style >
</style>
<template>
	<div>
    <!-- CONATINER -->
		<faveo-box :title="lang('when_ticket_satisfies_these_condtions')">
			<div class="container-fluid">
	      <div class="row">
	        <!-- MATCH ANY RADIO -->
	        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
	            <label class="radio-inline">
	            	<input type="radio" name="match" v-model="obj.matcher" value="any">&nbsp;
	                <span> {{lang('match_any_of_below')}}  </span>
	            </label>
	        </div>
	        <!-- MATCH ANY RADIO END -->

	        <!-- MATCH ANY BELOW -->
	        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
	            <label class="radio-inline">
	             	  <input type="radio" name="match" v-model="obj.matcher" value="all">&nbsp;
	                 <span>{{lang('match_all_of_below')}}</span>
	            </label>
	        </div>
	        <!-- MATCH ANY BELOW END -->
	      </div>
	      <!-- RADIO BUTTON END -->
	    </div>
	    <!-- CONATINER END -->
	      <!-- MENU LAYOUT COMPONENT -->
	      <div class="row">
	        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	            <menu-layout :list="ruleList" :category="category" :addButtonLabel="'add_new_condtion'" :listType="'rule'" :customForm="customForm" :editRuleValues="editRuleValues"></menu-layout>
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
     * helps to fetch the label from the lan file
     */
    addButtonLabel: {
      type: String,
      default: "add_new_condtion"
    },

    /**
     * rule array
     */
    ruleList: {
      type: Array,
      default: null
    },

    /**
     * the complete object
     */
    obj: {
      type: Object,
      default: null
    },

    /**
     * the customform fields variable
     */
    customForm: {
      type: Array,
      default: null
    },

    /**
     * the values which we recive from backend on the eit apicall
     */
    editRuleValues: {
      type: Array,
      default: null
    },
    /**
     * the value is used to define which form you are in ie Workflow or Listner
     */
    category: {
      type: String,
      require: true
    }
  },
  data() {
    return {};
  },
  components: {
    "menu-layout": require("./MenuLayout.vue"),
		"faveo-box": FaveoBox,
  }
};
</script>
