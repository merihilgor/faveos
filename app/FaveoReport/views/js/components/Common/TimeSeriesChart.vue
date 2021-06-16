<template>
  <div class="row" id="chart-container">
    <div class="col-md-12">
      <div class="panel panel-default" id="chart-panel-main">

        <!-- Panel heading -->
        <div class="panel-heading row">
          <h3 class="panel-title col-md-6 chart-panel-heading">{{chartApiData ? chartApiData.name : ''}}</h3>

          <!-- Category dropdown -->
          <div class="btn-group pull-right" v-if="categories.length">
            {{lang(categoryPrefix)}} <label> {{ lang(selectedCategory)}}</label>
            <i class="dropdown-toggle rpt_icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
              id="category-dropdown">
              <span class="caret"></span>
              <span class="sr-only"></span>
            </i>
            <ul class="dropdown-menu" aria-labelledby="category-dropdown">
              <li v-for="(item, index) in categories" :class="selectedCategory === item ? 'active' : ''" :key="index" @click="onCategoryChange(item)">
                <a href="javascript:void(0);">{{lang(categoryPrefix) + lang(item)}}</a>
              </li>
            </ul>
          </div>

        </div>

        <!-- Panel body -- chart area -->
        <div class="panel-body">

          <!-- Loader -->
          <relative-loader v-if="isLoading" />

          <div v-else>
            <!-- data widget section if present -->
            <section v-if="dataWidgetApi">
              <data-widget :data-widget-data="dataWidgetData" />
            </section>

            <!-- section for chart -->
            <faveo-chart :chart-data="chartData" :chart-type="'line'" />
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script>

import axios from 'axios';
import { getTimeLabels, parseTimeSeriesChartData } from 'ChartFactory/utils';
import { boolean } from 'helpers/extraLogics';
import { errorHandler } from 'helpers/responseHandler';
import { getValidFilterObject } from '../../helpers/utils';

export default {

  name: 'time-series-chart',

  components: {
    'faveo-chart': require('ChartFactory/FaveoChart'),
    'relative-loader': require('components/Extra/Loader'),
    'data-widget': require('components/Common/DataVisualization/DataWidget')
  },

  props: {

    // Api endpoint to fetch chart data
    chartDataApi: {
      type: String,
      required: true
    },

    // Api endpoint to fetch data widget data 
    dataWidgetApi: {
      type: String,
      default: () => undefined
    },

    // Category array 
    categories: {
      type: Array,
      default: () => []
    },

    // Default category option
    defaultCategory: {
      type: String,
      default: () => ''
    },

    /**
     * string to show in the category dropdown
     * used as key for fetching data from server basis of category
     */
    categoryPrefix: {
      type: String,
      default: () => 'view_by'
    },

    // Defualt filter field value objec
    filterParams: {
      type: Object,
      default: () => {}
    },

    /**
     * Report array index
     */
    reportIndex: {
      type: Number,
      required: true
    }
  },

  data: () => {
    return {
      chartApiData: null,
      isLoading: true,
      chartData: null,
      selectedCategory: '',
      dataWidgetData: null
    }
  },

  beforeMount() {

    // Assign defualt category to selected category
    this.selectedCategory = this.defaultCategory;

    // Make api call to fetch chart data
    this.getDataFromServer();
  },

  methods: {

    // Fetch widget/chart data when category changes
    onCategoryChange(value) {
      this.selectedCategory = value;
      this.$emit('updateChangedValue', this.selectedCategory, this.reportIndex, 'selected_view_by');
      this.getDataFromServer();
    },

    getDataFromServer() {
      this.isLoading = true;

      // Fetch data widget data if datawidget api endpoint provided 
      if(boolean(this.dataWidgetApi)) {
        axios.get(this.dataWidgetApi, { params: this.getUrlParams() })
        .then(res => {
          this.dataWidgetData = res.data.data;
        })
        .catch(err => {
          errorHandler(err, 'category-based-report');
        })
      }

      // Fetch chart data
      axios.get(this.chartDataApi, { params: this.getUrlParams() })
      .then(res => {
        this.chartApiData = res.data.data;
        this.processChartData();
      })
      .catch(err => {
        errorHandler(err, 'category-based-report');
        this.isLoading = false;
      })
    },

    processChartData() {
      try {
        // Sorted(basis of time) list of time labels
        const timeSeriesLabels = getTimeLabels(this.chartApiData.data);

        // Chart object
        this.chartData = {
          data: parseTimeSeriesChartData(this.chartApiData.data, timeSeriesLabels),
          labels: timeSeriesLabels,
          categoryLabel: this.chartApiData.categoryLabel,
          dataLabel: this.chartApiData.dataLabel,
          panelTitle: this.chartApiData.name
        }
      } catch (error) {
        console.error('TimeSeriesChart | processChartData ', error);
      }

      this.isLoading = false;
    },

    /**
     * get Url parameters
     * may contain slected filter values, selected category limit
     */
    getUrlParams() {
      let params = getValidFilterObject(this.filterParams);
      params[this.categoryPrefix] = this.selectedCategory;
      return params;
    },

  },

  watch: {
    filterParams(){
      this.getDataFromServer();
    }
  }
}
</script>

<style lang="css" scoped>
.panel-heading {
  padding: 6px 15px;
}
.panel-heading.row {
  margin: 0;
}
.rpt_icon {
  cursor: pointer;
}
.chart-panel-heading {
  padding-left: 0;
  padding-top: 0.5rem;
}
.chart-list {
  display: flex;
  flex-wrap: wrap;
}
#chart-container {
  margin: 1rem -5px 1rem -5px;
}

</style>