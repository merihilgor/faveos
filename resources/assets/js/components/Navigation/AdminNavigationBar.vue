<template>
  <div id="admin-navigation-container">
    <loader v-if="loading"></loader>
    <navigation-layout  v-show="!loading">
      <ul id="admin-navigation" class="faveo-navigation sidebar-menu tree"
        v-for="(navigationCategory, index) in navigationArray">
          <!-- for loop from here itself -->
          <li class="header">
            {{navigationCategory.name.toUpperCase()}}
          </li>

          <!-- add profile pic and other stuff here -->
          <!-- Add loader here -->
          <navigation v-show="!loading" v-for="(navigation, index) in navigationCategory.navigations"
            :key="index"
            :name="navigation.name"
            :count="navigation.count"
            :hasCount="navigation.hasCount"
            :iconClass="navigation.iconClass"
            :redirectUrl="navigation.redirectUrl"
            :routeString="navigation.routeString"
            :hasChildren="navigation.hasChildren"
            :children="navigation.children"
            :index="index"
            >
          </navigation>
        </ul>
      </navigation-layout>
  </div>
</template>

<script>

import axios from 'axios';
import {errorHandler} from 'helpers/responseHandler';
import NavigationLayout from './NavigationLayout';

export default {
  name : 'admin-navigation-bar',

  description : 'Admin Navigation Bar on admin panel',

  data(){
    return {
      navigationArray : [],
      loading: false,
     }
  },

  created() {
    window.eventHub.$on('update-sidebar', this.refreshSidebar);
  },

  beforeMount(){
      this.loading = true;
      this.getDataFromServer();
      
  },

  methods:{

    /**
     * Gets data from server and populate in the component state
     * NOTE: Making it a diffent method to improve readablity
     * @return {Promise}
     */
    refreshSidebar(){
      this.getDataFromServer();
    },

    /**
     * Gets data from server and populate in the component state
     * @return {Promise}
     */
    getDataFromServer(){
      axios.get("/api/admin/navigation").then(res => {

        this.navigationArray = res.data.data

      }).catch(err => {

        errorHandler(err);

      }).finally(res => {

        this.loading = false;
      })
    }
  },

  components: {
    'navigation' : require('components/Navigation/Navigation'),
    'navigation-layout' : NavigationLayout,
    'loader': require('components/Extra/Loader'),
  },
}
</script>

<style>
  #admin-navigation-container .fulfilling-bouncing-circle-spinner {
    margin-top : 300px !important;
    margin-left : 80px !important;
    position: fixed !important;
  }
</style>
