<template>

  <!-- add active class only when href matches the url OR it is  clicked it was inactive before-->
  <!--  -->
  <li :id="getUniqueId('navigation-element')" :class="['treeview', 'faveo-navigation-element',{'faveo-navigation-element-rtl' : isRtlLayout }]">
    <div>
      <!-- if redirect url is null, it should not redirect -->
      <a :href="getLink()" :id="getUniqueId('navigation-button')" :class="['faveo-navigation-anchor',{'active-navigation-element' : active}]"
        @click="onNavigationClick" :style="getStyleForLink()">

        <span :id="getUniqueId('icon')" :class="iconClass"></span>

        <span :title="name" :id="getUniqueId('name')">{{(hasCount) ? subStr(name,16) : name}}</span>

        <small v-if="boolean(hasCount)" :id="getUniqueId('count')" class="label pull-right bg-green">
          {{count}}
        </small>

        <i :id="getUniqueId('navigation-child-list-indicator')" v-if="boolean(hasChildren)" :class="getClassForAngle()"></i>

      </a>
    </div>
    <!-- if it hasChildren -->
    <!-- toggle all the children -->
    <ul :id="getUniqueId('children-container')" v-if="boolean(hasChildren)" :class="['treeview-menu', 'faveo-treeview-menu', menuOpened ? 'menu-opened': 'menu-collapsed']">

        <!-- all the elements of the children should be coming in the toggle -->
        <!-- this should come in toggle -->
        <navigation v-for="(child, index) in children"
          :key="index"
          :name="child.name"
          :count="child.count"
          :hasCount="child.hasCount"
          :iconClass="child.iconClass"
          :redirectUrl="child.redirectUrl"
          :routeString="child.routeString"
          :hasChildren="child.hasChildren"
          :children="child.children"
          :index="index"
          :toggleParent="toggleActive"
          >
        </navigation>

    </ul>
  </li>
</template>

<script>

import axios from "axios";
import { boolean,getSubStringValue } from 'helpers/extraLogics';

export default {

  name : 'navigation',

  props:{
    name : {type : String, required : true},
    count : {type : Number, required : true},
    hasCount : {type : Boolean, required : true},
    iconClass : {type : String, required : true},
    redirectUrl : {type : String|null, required : true},
    routeString : {type : String, required : true},
    hasChildren : {type : Boolean, required : true},
    children : {type : Array, required : true},
    index : {type : Number, required:true},
    toggleParent : {type : Function, default : ()=>{} }
  },

  data(){
    return {
      menuOpened : false,
      active : false,
    }
  },

  mounted(){
    this.markNavigationActiveIfRequired()
  },

  methods : {

    /**
     * Marks active and menuOpened as true.
     * NOTE: this method is passed to children so that chilren can call this method and mark parent as active
     * @return {undefined}
     */
    toggleActive(){
      this.active = true;
      this.menuOpened = true;
      this.toggleParent();
    },

    /**
     * Converts name to lowercase and replace space with undersore so that it can be used as a valid for an element
     * @param  {Number} index [description]
     * @param {String} field  field for which Id is required
     * @return {string}
     */
    getUniqueId(field){
      return this.name.toLowerCase().replace(/ /g,"-")+'-'+field+'-'+ this.index;
    },

    /**
     * Redirects if has no child elements
     * @param  {String} redirectUrl
     * @return {String}
     */
    onNavigationClick(){

      // for logout
      if(this.routeString == 'auth/logout'){

        axios.post('/auth/logout').then(res=>{

           window.location.replace(res.data.data);

        }).catch(error=>{})
      }
      // if hasChildren is false, then redirect
      if(boolean(this.hasChildren)){
        this.menuOpened = !this.menuOpened;
      }
    },

    /**
     * Gets redirect link for the anchor tag
     * @return {String}
     */
    getLink(){
      if(!boolean(this.hasChildren)){
        return this.redirectUrl;
      }
      return 'javascript:void(0);';
    },

    /**
     * Checks if navigation is active, if yes mark that as active
     * @return {undefined}
     */
    markNavigationActiveIfRequired(){
      // const route = this.getRelativeRouteUrl();

      if(this.getCurrentRouteUrl() == this.redirectUrl){
        this.active = true;
        this.toggleParent();
      }
    },

    /**
     * Gets current url
     * @return {String}
     */
    getCurrentRouteUrl(){
      return window.location.href;
    },

    /**
     * gets style for anchor tags in the navigation
     * @return {undefined}
     */
    getStyleForLink(){
      if(this.active){
        let style = {"color" : "white", "font-weight" : 600};

        if(!boolean(this.hasChildren)){

          if(boolean(this.isRtlLayout)){
            style["border-right"] = "2px solid " + this.headerColor;
          }else{
            style["border-left"] = "2px solid " + this.headerColor;
          }
        }
        return style;
      }
    },

    /**
     * Gets class for angle which comes next to navigation which has children
     * @return {String}
     */
    getClassForAngle(){
      let classNames = boolean(this.menuOpened) ? "fa fa-angle-down" : "fa fa-angle-left";
      classNames = boolean(this.isRtlLayout) ? (classNames + " pull-left") : (classNames + " pull-right");
      return classNames;
    },

    subStr(name,count) {
      return getSubStringValue(name,parseInt(count));
    }
  },

  watch : {
    active(newVal){
      if(newVal){
        // waiting for the DOM to render completely so that active-navigation-element can be present
        setTimeout(()=>{

          let activeElements = document.getElementsByClassName('active-navigation-element');
          if(activeElements !== undefined){
            activeElements[activeElements.length - 1].scrollIntoView({behavior: "smooth"});
          }
        },10)
      }
      return newVal;
    }
  }
}

</script>

<style scoped>

.faveo-navigation-element .menu-collapsed {
  display: block;
  max-height: 0px !important;
  transition: max-height 0.5s ease-out;
  overflow: hidden;
}

.faveo-navigation-element .menu-opened {
  display: block;
  max-height: 3000px !important;
  transition: max-height 0.5s ease-in;
  overflow: hidden;
}

.faveo-navigation-element a {
  background: none!important;
  border: 0px;
  color: #b8c7ce;
  width: 100%;
  border-left: 3px;
  padding: 8px 12px 8px 15px;
  display: block;
}

.faveo-navigation-element>div>a:hover{
  background-color: #1a2226 !important;
}


.faveo-navigation-element {
  padding-left : 12px;
}

.faveo-navigation-element-rtl {
  padding-left : 0px !important;
  padding-right : 12px !important;
}

.treeview-menu {
  padding : 0px !important;
}

.sidebar-menu>li>.treeview-menu{
  background-color : #232d32 !important;
}
</style>
