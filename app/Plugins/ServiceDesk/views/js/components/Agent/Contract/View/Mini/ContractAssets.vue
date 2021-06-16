<template>

  <div>

    <data-table :url="apiEndPoint" :dataColumns="columns"  :option="options" 
      scroll_to="changes-associates" :componentTitle="componentTitle">

    </data-table>

    <transition  name="modal">

        <contract-asset-detach v-if="showModal" :onClose="onClose" :showModal="showModal" :assetId="assetId" 
          :contractId="contractId" :compName="componentTitle">

        </contract-asset-detach>
    </transition>
  </div>
</template>

<script>

  import { mapGetters } from 'vuex'

  import Vue from 'vue'

	export default {

    name : 'contract-assets',

		description : 'Contract assets component',

		props : {

      actions : { type : String | Object, default : '' },

      category : { type:String , default : 'inbox'},

      apiUrl : { type : String, default : ''},

      contractId : { type : String | Number, default : ''},

      componentTitle : { type : String, default : ''},
    },

		data() {

			return {

        selectedTickets : this.tickets,

        columns: this.actions.contract_asset_detach ?  ['name', 'managed_by', 'used_by','action'] : ['name', 'managed_by', 'used_by'] ,

        options : {},

        apiEndPoint : this.apiUrl,

        showModal : false,

        assetId : '',
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

          action: function(createElement, row) {
          
            let span = createElement('i', {  attrs:{ 'class' : 'fa fa-unlink' } });
          
            return createElement('a', {
             
              attrs: { href: 'javascript:;', class : 'btn btn-primary' },
             
              on: {
                
                click: function() {
                
                  self.showModal = true;

                  self.assetId = row.id
                }
              }
            }, [span]);
          },
        },

        sortable:  ['name', 'managed_by', 'used_by'],

        sortable:  ['name', 'managed_by', 'used_by'],

        pagination:{chunk:5,nav: 'scroll'},

        requestAdapter(data) {

          return {

            'sort-field': data.orderBy ? data.orderBy : 'name',

            'sort-order': data.ascending ? 'desc' : 'asc',

            'search-query':data.query,

            page:data.page,

            limit:data.limit,
          }
        },

        responseAdapter({data}) {

          return {

            data: data.data.assets.map(data => {

            data.detach = true;

            data.contract_id = self.contractId;

            data.compName = self.componentTitle;

            return data;
          }),
            count: data.data.total
          }
        },
      }
    },

    methods : { 
    
      onClose(){
           
          this.showModal = false;
        },
    },

		components : {

      'contract-asset-detach': require('./Child/ContractAssetDetach'),

			'data-table': require('components/Extra/DataTable')
		}
	};
</script>

<style type="text/css">

	.associate-name{
    width: 30%;
    word-break: break-all;
  }

  .associate-manage{
    width: 30%;
    word-break: break-all;
  }

  .associate-use{
    width: 30%;
    word-break: break-all;
  }

  .associate-action{
    width: 10%;
    word-break: break-all;
  }
</style>