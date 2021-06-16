<template>
  <div>
    <!-- Alert -->
    <alert componentName="report-home-page" />

    <custom-loader v-if="isLoading" :duration="4000"></custom-loader>

    <div v-if="reportList.length > 0">
      <div class="box box-primary" v-for="reportCategory in reportList" :key="reportCategory.id">
        <div class="box-header">
          <h3 class="box-title">{{reportCategory.category}}</h3>
        </div>
        <div class="box-body">

          <div class="table-responsive">
            <table class="table">
              <tbody>
                <tr class="Default" v-for="report in reportCategory.reports" :key="report.id">
                  <td class="col-md-1">
                    <span class="fa-stack fa-2x">
                      <i :class="report.icon_class"></i>
                    </span>
                  </td>
                  <td class="col-md-8">
                    <dl>
                      <dt class="text-uppercase">
                        <a :href="basePath() + '/' + report.view_url">
                        {{report.name}}</a>
                      </dt>
                      <dd class="text-overflow">{{report.description}}</dd>
                    </dl>
                  </td>
                  <td class="text-right" style="position:relative">
                    <div class="label label-default" v-if="report.is_default">{{lang('default')}}</div>
                    <button v-else class="btn btn-danger" :title="lang('delete')" @click="deleteCustomReport(report.id)">
                      <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                    <div style="position: absolute; bottom: 0.3rem; right:1rem;">
                      <small>{{lang('last_modified_on')}}: <strong>{{formattedTime(report.updated_at)}}</strong></small>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

</template>

<script>

import axios from 'axios';
import { errorHandler, successHandler } from 'helpers/responseHandler';
import {mapGetters} from "vuex"

export default {

  name: 'report-home-page',

  components: {
    'custom-loader': require('components/MiniComponent/Loader'),
    'alert': require("components/MiniComponent/Alert"),
  },

  data: () => {
    return {
      reportList: [],
      isLoading: false,
    }
  },

  computed: {
    ...mapGetters(["formattedTime"])
  },

  beforeMount() {
    this.getReportList();
  },

  methods: {
    getReportList() {
      this.isLoading = true;
      axios.get('api/agent/report-list')
      .then(res => {
        this.reportList = res.data.data;
      }).catch(err => {
        errorHandler(err, 'report-home-page');
      }).finally(res => {
        this.isLoading = false;
      });
    },

    deleteCustomReport(reportId) {
      const isConfirmed = confirm('Are you sure you want to delete this report?')
      if(isConfirmed) {
        this.isLoading = true;
        axios.delete('api/report/' + reportId)
        .then(res => {
          this.getReportList();
          successHandler(res, 'report-home-page');
        }).catch(err => {
          errorHandler(err, 'report-home-page');
          this.isLoading = false;
        })
      }
    },

  }
}
</script>

<style lang="css" scoped>

</style>