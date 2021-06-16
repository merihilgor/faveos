<template>

  <transition v-if="currentStatus" name="modal">

    <div class="modal-mask" :class="{rtl : language === 'ar'}">

      <div class="modal-wrapper " :class="classname">

        <div class="modal-container" :style="containerStyle">

          <div class="modal-header">

            <slot name="alert"></slot>

            <button :class="{left: lang_locale == 'ar'}" type="button" @click="onClose()" class="close">

            <span aria-hidden="true" id="modal_close">&times;</span>

          </button>

            <slot name="title"></slot>

          </div>

          <div class="modal-body modal-body-spacing" >
        
            <slot name="fields"></slot>
        
          </div>

          <div class="modal-footer justify-content-between">

            <button type="button" id="clos" :class="{right: lang_locale == 'ar'}" class="btn btn-default btn-light pull-left" 
              @click="onClose()">
              <i class="fa fa-times">&nbsp;&nbsp;</i>{{lang('close') }}
            </button>
          
            <slot name="controls"></slot>

          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
<script>
export default {
  name: "modal",

  description: "Modal popup Component",

  props: {
    /**
     * state of modal popup
     * @type {Object}
     */
    showModal: { type: Boolean, default: false },

    /**
     * method which will call on click on close button
     * @type {Function}
     */
    onClose: { type: Function },

    /**
     * contains css styles fro the container
     * @type {Object}
     */
    containerStyle: { type: Object },

    /**
     *Class name helps to matain the size of the modal as per the reuirement
     * for eg if modal-lg is set if large modal size is required
     */
    classname: {
      type: String,
      default: "modal-md"
    },

    language : { type : String, default : ''},
  },
  data() {
    return {
      currentStatus: this.showModal,

      /**
       * for rtl support
       * @type {String}
       */
      lang_locale: ""
    };
  },

  created() {
    // getting locale from localStorage
    this.lang_locale = localStorage.getItem("LANGUAGE");
  }
};
</script>
<style>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  font-weight: 400;
  font-size: 14px;
  /*transition: opacity 0.5s ease !important;*/
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 800px;
  margin: 0px auto;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.5s ease !important;
}

.modal-header {
  font-weight: 500;
  font-size: 20px;
  padding: 15px 15px 0 15px;
}

.modal-header h3 {
  margin-top: 0;
}

.modal-default-button {
  float: right;
}

.modal-body-spacing {
  padding: 0px;
  margin-top: 1rem;
  margin-bottom: 1rem;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}

.right {
  float: right !important;
}
.rtl {
  direction: rtl;
}

.modal-header, .modal-footer {
  display: flow-root !important;
}

#modal_close{
  font-size: 1.5rem !important;
}

.modal-header h4 { margin-top: 0px !important; }
</style>
