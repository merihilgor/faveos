<template>
  <div class="box box-danger dashboard-widget-box">
    <div class="box-header with-border">
      <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
      <span v-if="ticketData.title">
        <h3 class="box-title">{{ticketData.title}}</h3>
        <dashboard-help :helplink="ticketData.helpLink" :description="ticketData.description" />
        <sup style="font-size: 14px;"><span class="label label-info">Beta</span></sup>
      </span>
       <i class="fa fa-refresh pull-right" aria-hidden="true" @click="refreshData()" :title="lang('refresh')"></i>
    </div>
    <div class="box-body scrollable-area" v-if="ticketData.data.length > 0">
      <ul class="products-list product-list-in-box">
        <li class="item" v-for="(ticket, aindex) in ticketData.data" :key="aindex">
          <div class="product-info">
            <div class="product-title">
              <strong v-html="ticket.title"></strong>
              <div class="pull-right">
              <div v-for="(attribute, index) in ticket.attributes" :key="index" class="pull-right">
                <div>{{attribute.key}}: <span v-html="attribute.value"></span></div>
              </div>
            </div>
            </div>
          </div>
        </li>
        <infinite-loading @infinite="getDataFromServer">
          <div slot="spinner"></div>
          <div slot="no-results"></div>
           <div slot="no-more"></div>
        </infinite-loading>
      </ul>
    </div>
    <div v-if="hasDataFetched && ticketData.data.length === 0" class="no-data-section">{{lang('no_data_available')}}</div>
  </div>    
</template>

<script type="text/javascript">
import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';
import FaveoBox from 'components/MiniComponent/FaveoBox';

	export default {
		
    name : 'dashboard-require-immediate-action',
  
		data: () => {
			return {
        ticketData: {
          title: '',
          data: []
        },
        page: 1,
        hasDataFetched: false
      }
    },
    
    beforeMount() {
      this.getDataFromServer();
    },
		
		methods: {
			getDataFromServer($state, isRefresh) {
        this.$store.dispatch('startLoader', this.$options.name);
        axios.get('api/agent/dashboard-report/require-immediate-action', { params: { page: this.page } })
        .then(response => {
          this.updateData(response.data.data, $state, isRefresh);
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page');
        })
        .finally(() => {
          $state && $state.loaded();
          this.$store.dispatch('stopLoader', this.$options.name);
        })
      },

      updateData(responseData, $state, isRefresh) {
        this.ticketData.title = responseData.title;
        this.ticketData.description = responseData.description;
        this.ticketData.helplink = responseData.helpLink;
        if(isRefresh) {            
          this.ticketData.data = responseData.data;
        } else {
          this.ticketData.data.push(...responseData.data);
        }
        if(responseData.data.length == 0) {
          // mark infinite loader as complete if data length is 0
          $state && $state.complete();
        } else {
          this.page += 1;
        }
        this.hasDataFetched = true;
      },

      refreshData() {
        this.page = 1;
        this.hasDataFetched = false;
        this.getDataFromServer(undefined, true);
      }
    },

    components: {
      'dashboard-help': require('./DashboardHelp')
    }
	};
</script>

<style type="text/css" scoped>
.products-list .product-info {
  margin-left: 0px !important;
}
.scrollable-area {
  margin: 0.7rem;
}
</style>