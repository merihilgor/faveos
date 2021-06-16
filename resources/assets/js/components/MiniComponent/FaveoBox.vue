<template>
  <div class="">

    <div :class="['box', boxClass]">
      <div class="box-header with-border faveo-box-header">
        <h4 class="box-title" id="box-title" v-if="title">{{title}}</h4>

        <slot name="customActions"></slot> 
        
        <slot name="headerTooltip"></slot>

        <slot name="headerMenu"></slot>
      </div>

      <div class="box-body">
        <slot></slot>
      </div>

      <div class="box-footer margin-left-10 faveo-box-footer" v-if="hasFooterSlot">
        <slot name="actions"></slot>
      </div>

    </div>
  </div>

</template>

<script>
  export default {
    name: 'faveo-box',

    description: 'Bootstrap box -- theme AdminLTE',

    props: {
      /**
       * Box title */
      title: {
        type: String,
        default: ''
      },
      /**
       * Box class
       * e.g. `box-primary`, `box-default`, etc
       */
      boxClass: {
        type: String,
        default: 'box-primary'
      }
    },

    computed: {
      hasFooterSlot() {
        return !!this.$slots.actions;
      }
    }
  }
</script>

<style scoped>
.faveo-box-footer {
  border-top: 0px !important;
}
.faveo-box-header {
  padding-left: 0.5rem !important;
}
</style>