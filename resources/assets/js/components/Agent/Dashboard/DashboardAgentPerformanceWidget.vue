<template>
  <div class="box box-primary dashboard-widget-box">
    <div class="box-header with-border">
      <i class="fa fa-star-o" aria-hidden="true"></i>
      <span v-if="hasDataFetched">
        <h3 class="box-title">{{topAgentData.title}}</h3>
        <dashboard-help :helplink="topAgentData.helpLink" :description="topAgentData.description" />
        <sup style="font-size: 14px;"><span class="label label-info">Beta</span></sup>
      </span>
      <i class="fa fa-refresh pull-right" aria-hidden="true" @click="refreshData()" :title="lang('refresh')"></i>
    </div>
    <div class="box-body scrollable-area" v-if="hasDataFetched">
      <div class="col-md-12" style="padding-bottom: 0.8rem;" v-for="(agentData, index) in topAgentData.data" :key="index">
        <div class="progress-group" :title="agentData.description">
          <span class="progress-text">{{agentData.key}}</span>
          <span class="progress-number"><b>{{agentData.value ? agentData.value + '%' : ''}}</b></span>
          <div class="progress sm">
            <div :class="getProgressBarClass(agentData.value)" :style="getProgressBarStyle(agentData.value)"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';

	export default {
		
    name : 'dashboard-agent-performance-widget',
  
		data: () => {
			return {
        topAgentData: null,
        hasDataFetched: false
      }
    },

    beforeMount() {
      this.getDataFromServer();
    },
    
    methods: {
			getDataFromServer() {
        this.$store.dispatch('startLoader', this.$options.name);
        axios.get('api/agent/dashboard-report/agent-performance-widget')
        .then(response => {
          this.topAgentData = response.data.data;
          this.hasDataFetched = true;
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page');
        })
        .finally(() => {
          this.$store.dispatch('stopLoader', this.$options.name);
        })
      },

      getProgressBarClass(value) {
        if(value >= 90) {
          return 'progress-bar progress-bar-green';
        } else if(value >= 70) {
          return 'progress-bar progress-bar-aqua';
        } else if(value >= 50) {
           return 'progress-bar progress-bar-yellow';
        } else {
          return 'progress-bar progress-bar-red';
        }
      },

      getProgressBarStyle(value) {
        if(value === null) {
          value = 0;
        }
        return { width: value + '%' }
      },

      refreshData() {
        this.getDataFromServer();
      }
    },
    
    components: {
      'dashboard-help': require('./DashboardHelp')
    }
	};
</script>

<style type="text/css" scoped>
</style>