<template>

  <data-table :url="apiEndPoint" :dataColumns="columns"  :option="options" 
    scroll_to="changes-associates" :componentTitle="componentTitle">

  </data-table>

</template>

<script>

  import { mapGetters } from 'vuex'

  import Vue from 'vue'

  Vue.component('change-associates-actions', require('./ChangeAssociatesActions.vue'));

	export default {

    name : 'changes-associates-table',

		description : 'Changes associates table page',

		props : {

      category : { type:String , default : 'inbox'},

      apiUrl : { type : String, default : ''},

      changeId : { type : String | Number, default : ''},

      componentTitle : { type : String, default : ''},
    },

		data() {

			return {

      selectedTickets : this.tickets,

      columns: ['name', 'managed_by', 'used_by', 'contract','action'],

      options : {},

      apiEndPoint : this.apiUrl,
			}
		},

    beforeMount(){

      const self = this;

      this.options = {

        sortIcon: {

          base : 'glyphicon',

          up: 'glyphicon-chevron-down',

          down: 'glyphicon-chevron-up'
        },

        headings: {

          name:'Name',

          managed_by: 'Managed by',

          used_by : 'Used by',

          action: 'Action',
        },

        columnsClasses : {

          name : 'associate-name',

          managed_by : 'associate-manage',

          used_by : 'associate-used',

          contract : 'associate-contract',

          action : 'associate-action',
        },

        texts : {

          filter : '',

          limit : ''
        },

        templates: {

          name: function(createElement, row) {

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/service-desk/'+ self.category + '/' + row.id + '/show',
              }
            }, row.name);
          },

          managed_by(h,row){  return row.managed_by ? row.managed_by.full_name : '--'; },

          used_by(h,row){  return row.used_by ? row.used_by.full_name : '--'; },

          contract: function(createElement, row) {
            
            if(row.contract){

              return createElement('a', {
                attrs:{

                  href : self.basePath()+'/service-desk/contracts/'+row.contract.id+'/show',
                  target : '_blank'
                },
              }, row.contract.name);
            } else {
              return '--'
            }
          },

          action: 'change-associates-actions'
        },

        sortable:  ['name', 'managed_by', 'used_by'],

        sortable:  ['name', 'managed_by', 'used_by'],

        pagination:{chunk:5,nav: 'scroll'},

        requestAdapter(data) {

          return {

            'sort-field': data.orderBy ? data.orderBy : 'name',

            'sort-order': data.ascending ? 'desc' : 'asc',

            'search-query':data.query.trim(),

            page:data.page,

            limit:data.limit,
          }
        },

        responseAdapter({data}) {

          return {

            data: data.data.assets.map(data => {

            data.detach = true;

            data.change_id = self.changeId;

            data.compName = self.componentTitle;

            return data;
          }),
            count: data.data.total
          }
        },
      }
    },

		components : {
			'data-table': require('components/Extra/DataTable')
		}
	};
</script>

<style type="text/css">

	.associate-name{
    width: 23%;
    word-break: break-all;
  }

  .associate-manage{
    width: 22%;
    word-break: break-all;
  }

  .associate-use{
    width: 22%;
    word-break: break-all;
  }

  .associate-contract{
    width: 23%;
    word-break: break-all;
  }

  .associate-action{
    width: 10%;
    word-break: break-all;
  }
</style>