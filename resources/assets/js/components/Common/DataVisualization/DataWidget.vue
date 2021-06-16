<template>
  <div v-if="dataWidgetData && dataWidgetData.length" class="row">
    <div :class="widgetClass" v-for="(item, index) in dataWidgetData" :key="index">

      <a :href="item.redirectTo ? item.redirectTo : 'javascript:void(0)'" :target="item.redirectTo ? '_blank' : ''" rel="noopener" class="data-widget" :style="{cursor: item.redirectTo ? 'pointer': 'auto'}">
        <div :class="[ item.icon_class ? 'info-box' : 'info-box-without-icon', 'center', 'data-widget-box']">
          <span v-if="item.icon_class" class='info-box-icon' :style="{background: item.icon_color}"><i :class="item.icon_class"></i></span>
          <div :class="item.icon_class ? 'info-box-content': 'info-box-content-without-icon'" :title="item.key + ': ' + item.value">
            <span class="info-box-text data-widget-text">{{item.key}}</span>
            <span class="info-box-number data-widget-value">{{item.value}}</span>
          </div>
        </div>
      </a>

    </div>
  </div>
</template>

<script>
export default {

  name: 'data-widget',

  props: {

    // Array of widget object 
    dataWidgetData: {
      type: Array,
      default: () => []
    },

    widgetClass: {
      type: Array,
      default: () => ['col-md-4', 'col-sm-6', 'col-xs-12']
    }
  },

  data: () => {
    return {
    }
  },

}
</script>

<style lang="css" scoped>
.data-widget-box {
  transition: box-shadow 280ms cubic-bezier(.4,0,.2,1);
  background: #fff;
  color: rgba(0,0,0,.87);
  box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
}
a > .data-widget-box:hover {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
.data-widget-value {
  display: block;
  font-weight: bold;
  font-size: 20px;
}
.data-widget-text {
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-transform: uppercase;
}
.info-box {
  min-height: 75px;
}
.info-box-without-icon {
  display: block;
  min-height: 75px;
  background: #FFFFFF;
  width: 100%;
  border-radius: 2px;
  margin-bottom: 15px;
}
.info-box-icon {
  color: #FFFFFF;
  height: 75px;
  width: 75px;
  font-size: 35px;
  line-height: 75px;
}
.info-box-content {
  margin-left: 75px;
}
.info-box-content-without-icon {
  padding: 1rem;
}
</style>