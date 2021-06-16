<style scoped>
.req-button {
  z-index: 10;
  margin-right: 0px;
  border-radius: 0px;
  height: 34px;
}
.tel-code,
.res-alert,
label {
  font-size: 14px;
}
.mt_40{ margin-top: 40px; }
</style>
<template>
	<div class="inline">
    <!-- Add New Requester / Register -->
		<button id="add-requester-button" class="btn btn-default req-button" type="button" data-toggle="modal" data-target="#requester-form-container" @click="showRequesterModal()">{{trans(labelKey)}}</button>

		<!-- modal form -->
      <div id="requester-form-container" class="modal fade mt_40" role="dialog" v-show="showRequester">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="box-container">
                <div class="box-header with-border">
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidd="" en="true">×</span></button>
                   <h3 class="box-title">{{trans(labelKey)}}</h3>
                </div>
                <!-- Adding v-if to avoid validation trigger even if component is available but with visibility as none               -->
                  <div class="box-body" v-if="showRequester">
                  <create-form
                    :person="person"
                    :category="'user'"
                    submitApiEndpoint="/user/create/api/"
                  ></create-form>
                </div>
              </div>
            </div>
          </div>
      </div>
	</div>
</template>
<script>

import { lang } from "helpers/extraLogics";

export default {
  props: {
    /**
     * props used to check wether the modal is being called through agentrequestor or userrequestor
     */
    person: { type: String, default: null },
    labelKey: {type: String, default: "register"}
  },
  data() {
    return {
      showRequester: false,
    };
  },

  created(){
    window.eventHub.$on('userFormSubmitted', this.setRequester)
  },

  methods: {
    setRequester(data){
      this.$emit('setRequester', data);

      setTimeout(()=>{
        this.showRequester = false;
      }, 1000);

      //TODO: remove this line. Some script is injecting javascript from outside which is injecting modal-open class to body which is blocking scroll
      document.body.className = document.body.className.replace("modal-open","");
    },

    /**
     * method used to show the requestor modal
     */
    showRequesterModal() {
      this.showRequester = true;
    },
  }
};
</script>
<style>
#requester-form-container .box-container {
  margin: 0 !important;
}
.modal-backdrop.in {
    display:  none !important;
}
.fade.in {
    opacity: 1 !important;
}
.modal {
    background: rgba(0,0,0,.3) !important;
}
</style>
