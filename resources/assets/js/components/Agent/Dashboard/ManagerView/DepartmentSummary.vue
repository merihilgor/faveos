<template>
  <div class="box box-primary dashboard-widget-box">
    <div class="box-header with-border">
      <i class="fa fa-sitemap" aria-hidden="true"></i>
      <span v-if="hasDataFetched">
        <h3 class="box-title">{{departmentSummary.title}}</h3>
        <dashboard-help :helplink="departmentSummary.helpLink" :description="departmentSummary.description" />
      </span>
      <i class="fa fa-refresh pull-right" aria-hidden="true" @click="refreshData()" :title="lang('refresh')"></i>
    </div>
    <div class="box-body scrollable-area" v-if="departmentSummary && departmentSummary.data && departmentSummary.data.length > 0">
      <div class="box-widget widget-user-2" v-for="(department, aindex) in departmentSummary.data" :key="aindex">
        <div class="widget-user-header" style="padding: 0px;">
          <h4 class="widget-title">{{department.title}}</h4>
        </div>
        <div class="box-footer">
          <ul class="nav nav-stacked">
            <li style="padding: 0.5rem;" v-for="(attribute, index) in department.attributes" :key="index">{{attribute.key}}<span class="pull-right" v-html="attribute.value"></span></li>
          </ul>
        </div>
      </div>
    </div>
    <div v-if="hasDataFetched && departmentSummary && departmentSummary.data && departmentSummary.data.length === 0" class="no-data-section">{{lang('no_data_available')}}</div>
  </div>
</template>

<script type="text/javascript">
import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';

	export default {
		
    name : 'dashboard-department-summary',
  
		data: () => {
			return {
        departmentSummary: null,
        hasDataFetched: false
      }
    },
    
    beforeMount() {
      this.getDataFromServer();
    },
		
		methods: {
			getDataFromServer() {
        this.$store.dispatch('startLoader', this.$options.name);
        axios.get('api/agent/dashboard-report/manager/department-analysis')
        .then(response => {
          this.departmentSummary = response.data.data;
          this.hasDataFetched = true;
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page', this.$options.name);
        })
        .finally(() => {
          this.$store.dispatch('stopLoader', this.$options.name);
        })
      },

      refreshData() {
        this.getDataFromServer();
      }
		},

    components: {
      'faveo-image-element': require('components/Common/FaveoImageElement'),
      'dashboard-help': require('../DashboardHelp')
    }
	};
</script>

<style type="text/css" scoped>
.products-list .product-info {
  margin-left: 0px !important;
}
.attribute-value-text {
  font-size: 16px;
}
</style>