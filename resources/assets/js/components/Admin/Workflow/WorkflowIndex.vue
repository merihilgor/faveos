<template>
  <div>
    <alert componentName="dataTableModal" />

	<div class="box box-primary workflow-index-zero-padding">
		<div class="box-header with-border">
			<div class="row">
				<div class="col-md-4">
					<h2 class="box-title">{{lang(title)}}</h2>
				</div>
				<div class="col-md-8">
					<a id="workflow-index-create" class="btn btn-primary workflow-index-right" :href="base+'/workflow/create'"><span class="glyphicon glyphicon-plus"> </span> {{lang('create_workflow')}}</a>
					<a v-if="showTable && total_records > 1" class="btn btn-primary workflow-index-right" href="javascript:void:;" @click="reorderMethod"><span class="fa fa-reorder"> </span> &nbsp;{{lang('reorder')}}</a>
				</div>
			</div>
		</div>

    <div class="box-body">
		  <data-table v-if="apiUrl !== '' && showTable" :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
		<workflow-reorder v-if="!showTable" :onClose="onClose" :url="apiUrl+'?meta=true&sort=order&sort_order=asc'" 
      reorder_type="workflow">
      
    </workflow-reorder>

    </div>
	</div>
</div>
</template>

<script type="text/javascript">
import { lang } from "helpers/extraLogics";

import axios from "axios";

import moment from "moment";

import momentTimezone from "moment-timezone";
import {mapGetters} from "vuex";

export default {
  name: "workflow-index",

  description: "Workflow table component",

  data: () => ({
    /**
     * base url of the application
     * @type {String}
     */
    base: window.axios.defaults.baseURL,

    columns: [],

    options: {},

    /**
     * api url for ajax calls
     * @type {String}
     */
    apiUrl: "api/get-enforcer-list/",

    title: "list_of_ticket_workflows",

    showTable: true,

    total_records : 0,
  }),

  beforeMount() {

    const self = this;
    /**
     * columns required for datatable
     * @type {Array}
     */
    this.columns = [
      "name",
      "status",
      "order",
      "created_at",
      "updated_at",
      "action"
    ];

    this.options = {
      
      texts: {
        filter: "",
        limit: ""
      },
      headings: {
        name: "Name",
        status: "Status",
        order: "Order",
        created_at: "Created",
        updated_at: "Updated",
        action: "Action"
      },

	  columnsClasses : {
		  name: "workflow-index-name",
		  status: "workflow-index-status",
		  order: "workflow-index-order",
		  created_at: "workflow-index-created",
		  updated_at: "workflow-index-updated",
		  action: "workflow-index-action"
	  },

      templates: {
        
        action: "data-table-actions",

        status: function(createElement, row) {
            
          let span = createElement('span', {
            
            attrs:{
              
              'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
            }
          }, row.status ? 'Active' : 'Inactive');
                  
          return createElement('a', {
                    
          }, [span]);
        },

        created_at(h, row) {
            return self.formattedTime(row.created_at)
        },
        updated_at(h, row) {
			return self.formattedTime(row.updated_at)
        },
      },
      sortable: [
        "name",
        "status",
        "order",
        "created_at",
        "updated_at"
      ],
      filterable: ["name", "created_at", "updated_at"],
      pagination: { chunk: 5, nav: "scroll" },
      requestAdapter(data) {
        return {
          type: "workflow",
          sort: data.orderBy ? data.orderBy : "order",
          sort_order: data.ascending ? "asc" : "desc",
          search: data.query.trim(),
          page: data.page,
          limit: data.limit
        };
      },
      responseAdapter({ data }) {  

        self.total_records = data.data.total;      
        
        return {
          data: data.data.data.map(data => {
            data.edit_url =
              window.axios.defaults.baseURL + "/workflow/edit/" + data.id;
            data.delete_url =
              window.axios.defaults.baseURL +
              "/api/delete-enforcer/workflow/" +
              data.id;
            return data;
          }),
          count: data.data.total
        };
      }
    };
  },

  computed:{
	  ...mapGetters(['formattedTime'])
  },

  methods: {
    reorderMethod() {
      this.showTable = false;
      this.title = "reorder";
    },
    onClose() {
      this.showTable = true;
      this.title = "list_of_ticket_workflows";
    }
  },

  components: {
    "data-table": require("components/Extra/DataTable"),
    "workflow-reorder": require("components/Admin/Workflow/Reorder.vue"),
    'alert' : require('components/MiniComponent/Alert'),
  }
};
</script>

<style type="text/css">

.box.workflow-index-zero-padding {
  padding: 0 !important;
}
.workflow-index-right {
  float: right;
}
#workflow-index-create {
  margin-right: 0px !important;
}

.workflow-index-name {
	width:25% !important;
	word-break: break-all;
}

.workflow-index-created {
	width:23% !important;
	word-break: break-all;
}

.workflow-index-updated {
	width:23% !important;
	word-break: break-all;
}

.workflow-index-status {
	width:10% !important;
	word-break: break-all;
}

.workflow-index-action {
	width:10% !important;
	word-break: break-all;
}

.workflow-index-order {
	width:9% !important;
	word-break: break-all;
}

</style>
