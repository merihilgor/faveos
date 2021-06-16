<template>
  <div id="main-div">
    <alert componentName="dataTableModal" />
    <div class="box box-primary main-box" id="box-primary">
      <div class="box-header with-border">
        <div class="row">
          <div class="col-md-4">
            <h4 class="box-title" id="box-title-1">
              {{ lang('form_group_list') }}
            </h4>
          </div>
          <div class="col-md-8">

            <span id="form-group-create-button">{{formGroupListMounted()}}</span>

            <a id="add-fg-link" class="btn btn-primary right" :href="basePath() + '/form-group/create'">
              <span style="padding-right: 5px;" class="fa fa-plus" aria-hidden="true"></span>
              {{ lang('new_ticket_form_group') }}
            </a>
          </div>
        </div>
      </div>
      <div class="box-body">
        <data-table url="api/form-group-list" :dataColumns="columns" :option="options"></data-table>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
import axios from "axios";
import { mapGetters } from 'vuex'

export default {
  name: "form-group-list",
  description: "List down the form groups",

  components: {
    "data-table": require("components/Extra/DataTable")
  },

  data: () => ({

    /**
     * columns required for datatable
     * @type {Array}
     */
    columns: ["name", "created_at", "group_type", "action"],

    options: {}
    
  }),

  beforeMount() {

    let that = this;

    this.options = {
      headings: {
        name: "Name",
        created_at: "Creation Date",
        group_type: "Form Group Type",
        action: "Action"
      },

      texts: {
        filter: "",
        limit: "",
        filterPlaceholder: "Search"
      },

      templates: {
        action: "data-table-actions"
      },

      sortable: ["name", "created_at", "group_type"],
      filterable: ["name", "created_at", "group_type"],
      pagination: {
        chunk: 5,
        nav: "fixed",
        edge: true
      },

      requestAdapter(data) {
        return {
          "sort-field": data.orderBy ? data.orderBy : "created_at",
          "sort-order": data.ascending ? "asc" : "desc",
          "search-query": data.query,
          page: data.page,
          limit: data.limit
        };
      },

      responseAdapter({ data }) {

        return {
          data: data.data.data.map(data => {

            data.edit_url =
              that.basePath() + data.edit_url;

            data.delete_url =
              that.basePath() + '/api/form-group/' + data.id;

            data.created_at = that.formattedTime(data.created_at);

            return data;
          }),
          count: data.data.total
        };
      }
    }
  },

  computed:{
			...mapGetters(['formattedTime'])
    },
  
  methods : {

    formGroupListMounted() {

      window.eventHub.$emit('create-form-group-button-mounted');
    }
  }
};
</script>

<style type="text/css" scoped>
.box-header h3 {
  font-family: Source Sans Pro !important;
}
.right {
  float: right;
  margin-right: 3px;
}
.main-box {
    padding: 0px !important;
  }
</style>
