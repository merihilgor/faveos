
<template>
  <div id="main-div">
    <alert componentName="dataTableModal"/>

    <faveo-box :title="lang('list_of_approval_workflow')">

      <div class="pull-right" slot="headerMenu" >
        <a class="btn btn-primary right" :href="base+'/approval-workflow/create'"><span class="glyphicon glyphicon-plus"> </span> {{lang('create_approval_workflow')}}</a>
      </div>

      <div id="my_agents">
        <data-table v-if="apiUrl !== ''" :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
      </div>

    </faveo-box>
  </div>
</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import moment from 'moment';

	import momentTimezone from 'moment-timezone';

	import FaveoBox from 'components/MiniComponent/FaveoBox';

    import {mapGetters} from "vuex";

	export default {

		name : 'approval-workflow-index',

		description : 'Approval workflow table component',

		data: () => ({
			/**
			* base url of the application
			* @type {String}
			*/
			base:window.axios.defaults.baseURL,

			columns:[],

			options:{},

			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'/api/admin/approval-workflow/',

		}),

		beforeMount(){

            /**
			* columns required for datatable
			* @type {Array}
			*/
			this.columns= ['name', 'created_at', 'updated_at', 'action']

			this.options= {
				texts: {
		          filter: '',
		          limit: ''
		        },
				headings: { name: 'Name', CreatedAt: 'created_at', UpdatedAt: 'created_at', action:'Action'},
				templates: {
					action: 'data-table-actions',
					created_at: (h, row) => {
                      return this.formattedTime(row.created_at)
			        },
			        updated_at: (h, row) => {
                      return this.formattedTime(row.updated_at)
			        },
				},
				sortable:  ['name', 'created_at', 'updated_at'],
				filterable: ['name', 'created_at', 'updated_at'],
				pagination:{chunk:5,nav: 'fixed',edge:true},
				requestAdapter(data) {
			        return {
			          sort: data.orderBy ? data.orderBy : 'name',
			          order: data.ascending ? 'asc' : 'desc',
			          search:data.query.trim(),
			          page:data.page,
			          limit:data.limit,
			        }
			    },
			 	responseAdapter({data}) {
					return {
						data: data.data.data.map(data => {
						data.edit_url = window.axios.defaults.baseURL + '/approval-workflow/' + data.id + '/edit/';
						data.delete_url = window.axios.defaults.baseURL + '/api/admin/approval-workflow/' + data.id + '/workflow/';
						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

        computed:{
          ...mapGetters(['formattedTime'])
        },

        components:{
			'data-table' : require('components/Extra/DataTable'),
			"alert": require("components/MiniComponent/Alert"),
			'faveo-box': FaveoBox,
		}

	};
</script>

<style type="text/css" scoped>
	.box-header h3{
		font-family: Source Sans Pro !important
	}
	.box.box-primary {
		padding: 0px !important;
	}
	.right{
		float: right;
	}
</style>
