<template>
  <faveo-box :title="lang('service-desk-permissions')">
    <tool-tip slot="headerTooltip" :message="lang('view_is_mandatory_to_perform_all_actions')" size="medium"></tool-tip>
    <div class="row">
      <div class="col-sm-3" id="assign_groups" style="font-w" v-for="item in permissionsListSD" :key="item.id">
        <checkbox :name="item.id" :value="item.checked" :label="item.id" :onChange="checkBoxOperationsSD" :id="item.id"/>
      </div>
    </div>
  </faveo-box>
</template>

<script type="text/javascript">

import axios from "axios"
import {flatten} from "helpers/extraLogics";
import {errorHandler, successHandler} from "helpers/responseHandler";
import FaveoBox from 'components/MiniComponent/FaveoBox';


	export default {
		name: 'sd-agent-permission',
    description: 'Service desk permission list box component',
    
		data: () => ({
			permissionsListSD: [], // permission list for service desk
    }),

    props:{
      /**
       * it is passed as date with the event fired.
       * it contains selected sd permission list for a specfic agent
       * typeof of data passed is string, so need to parse this
       */
      data:{ type: String },
    },

    beforeMount() {
      // Getting service desk permission
      this.getpermissonListApi()   
    },

    methods: {
      /**
       * Get permission list by axios call
       * add new property `checked` to each element
       */
      getpermissonListApi() {
        axios.get('service-desk/get/permissions', {})
        .then(res => {
          this.permissionsListSD = flatten(res.data.permissions);
          this.permissionsListSD.forEach(function(element) {
            element.checked = false ;
          });
          if(this.data != "undefined") {
            this.selectCheckboxInEdit();
          }
        }).catch(err=>{
          errorHandler(err)
        })
      },
      /**
       * tick checkboxes if it is selected
       * `this.data` will be "undefined" in case of create agent
       */
      selectCheckboxInEdit(){
        let that = this;
        let dataObj = JSON.parse(this.data);
        dataObj.forEach(function(element){
          that.checkBoxOperationsSD(true, element.id)
        })
      },
      /**
       * Handle `checked` property of elements with any change event
       * Fire an event with selected id's list
       */
      checkBoxOperationsSD(value, selectedId) {
        this.permissionsListSD.forEach(function(element) {
          if(element.id == selectedId) {
            element.checked = value;
            return;
          }
        });
          let selectedPermIds = this.permissionsListSD.filter(perm => perm.checked).map(p => p.id)
          window.eventHub.$emit("selected-permission-list", selectedPermIds);
      }
    },
  
		components:{
			checkbox: require("components/MiniComponent/FormField/Checkbox"),
      "tool-tip": require("components/MiniComponent/ToolTip"),
      'faveo-box': FaveoBox,
		}
	};
</script>

<style type="text/css" scoped>

</style>