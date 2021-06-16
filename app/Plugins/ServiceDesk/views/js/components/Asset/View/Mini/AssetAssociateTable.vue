<template>

  <div id="asset_associates">

    <data-table :url="apiEndPoint" :dataColumns="columns"  :option="options" 
      scroll_to="asset-associates" :componentTitle="componentTitle">

    </data-table>

    <transition name="modal">

      <asset-associate-detach v-if="showModal" :onClose="onClose" :showModal="showModal" :associateId="associateId"
        :assetId="assetId" :category="category" :compName="componentTitle"> 
        
      </asset-associate-detach>

    </transition> 
  </div>
</template>

<script>

  import Vue from 'vue'

  import { mapGetters } from 'vuex'

  import { lang } from 'helpers/extraLogics'

	export default {

    name : 'asset-associate-table',

		description : 'Asset associates table page',

		props : {

      assetId : { type : String | Number, default : ''},

      category : { type : String, default : ''},

      componentTitle : { type : String, default : ''},

      columns : { type : Array, default : ()=>[]},

      sortable : { type : Array, default : ()=>[]},
      
      filterable : { type : Array, default : ()=>[]},
    },

		data() {

			return {

        options : {},

        showModal : false,

        associateId : ''
			}
		},

    computed : {

      apiEndPoint() {
      let assetIdsKeyName = this.category == 'ticket' ? 'asset-ids[]=' : 'asset_ids[]=';

        return '/service-desk/api/'+this.category+'-list?'+assetIdsKeyName+this.assetId
      },

      ...mapGetters(['formattedTime'])
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

          title: 'Subject',

           status_type_id: 'Status', 

           assignedTo : 'Assigned To' 
        },

        texts : {

          filter : '',

          limit : ''
        },

        templates: {

          subject: function(createElement, row) {

            let relation = self.category == 'problem' ? 'problem' : self.category + 's';

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/service-desk/'+relation+'/'+ row.id +'/show',
              }
            }, row.subject);
          },

          name: function(createElement, row) {

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/service-desk/contracts/'+ row.id +'/show',
              }
            }, row.name);
          },

           requester: function(createElement, row) {
            
            if(row.requester){

              return createElement('a', {
                
                attrs:{
                    
                    href : self.basePath() + '/user/'+row.requester.id,
                    
                    target : '_blank'
                  },
              }, row.requester.full_name ? row.requester.full_name : row.requester.email);
            } else { 

              return '---'
            }
          },

          department: function(createElement, row) {
            
            if(row.department){

              return createElement('a', {
                
                attrs:{
                    
                    href : self.basePath() + '/department/'+row.department.id,
                    
                    target : '_blank'
                  },
              }, row.department.name);
            } else { 

              return '---'
            }
          },

          status(h,row){
            
            return row.status ? row.status.name : '---';
          },

          priority(h,row){

            if(row.priority) {

              return row.priority.name ? row.priority.name : row.priority.priority;  
            } else {

              return '---'
            }
          },

          planned_start_date(h,row) {
            
            return row.planned_start_date && row.planned_start_date != '0000-00-00 00:00:00' ? self.formattedTime(row.planned_start_date) : '---'
          },

          planned_end_date(h,row) {
            
            return row.planned_end_date && row.planned_end_date != '0000-00-00 00:00:00' ? self.formattedTime(row.planned_end_date) : '---'
          },

          status_type_id(h, row) {
            
            return row.status ? row.status.name : '--';
          },


          assignedTo: function(createElement, row) {
            
            if(row.assignedTo){

              return createElement('a', {
                
                attrs:{
                    
                    href : self.basePath() + '/user/'+row.assignedTo.id,
                    
                    target : '_blank'
                  },
              }, row.assignedTo.full_name ? row.assignedTo.full_name : row.assignedTo.email);
            } else { 

              return '--'
            }
          },

          release_type(h,row) {

            return row.release_type ? row.release_type.name : '---' 
          },

          created_at(h,row) {
            
            return self.formattedTime(row.created_at)
          },

          expiry(createElement,row) {
            
                if(row.expiry && row.expiry.title){

                   return createElement('span', {

                    attrs: {
              
                        title: row.expiry.timestamp ? self.formattedTime(row.expiry.timestamp) : '',
                    }
                  },row.expiry.title);
                } else { return '---'}
          },

          contract_status(h,row) {

            return row.contract_status ? lang(row.contract_status.name) : '---' 
          },

          contract_renewal_status(h,row){

            return row.contract_renewal_status ? lang(row.contract_renewal_status.name) : '---' 
          },

          vendor(createElement,row) {

            if(row.vendor) {

              return createElement('a', {

                attrs: {

                  href: self.basePath() + '/service-desk/vendor/'+ row.id +'/show',
                }
              }, row.vendor.name);
            } else {

              return '---'
            } 
          },

          contract_type(h,row) {

            return row.contract_type ? row.contract_type.name : '---' 
          },

          title: function(createElement, row) {

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/thread/'+ row.id,
              }
            }, row.title);
          },

          ticket_number: function(createElement, row) {

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/thread/'+ row.id,
              }
            }, '#'+row.ticket_number);
          },

          assigned(h,row){  return row.assigned ? row.assigned.full_name : '---'; },


          action(createElement,row){

            let icon = createElement('i', {
              attrs:{
                'class' : 'fa fa-unlink'
              }
            });
            
            return createElement('a', {

              attrs:{ class : 'btn btn-primary', title : 'Detach' },
              
              on: {
                click: function() {
                  self.onDetach(row.id);
                }
              }
            }, [icon]);
          },

          identifier: function(createElement, row) {

             let relation = self.category == 'problem' ? 'problem' : self.category + 's';

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/service-desk/'+relation+'/'+ row.id +'/show',
              }
            }, row.identifier);
          },
        },

        sortable: self.sortable,
        
        filterable:  self.filterable,

        pagination:{chunk:5,nav: 'scroll'},

        requestAdapter(data) {

          return {

            'sort-field': data.orderBy ? data.orderBy : 'id',

            'sort-order': data.ascending ? 'desc' : 'asc',

            'search-query':data.query,

            page:data.page,

            limit:data.limit,
          }
        },

        responseAdapter({data}) {

          return {

            data: data.data[self.category+'s'],

            count: data.data.total
          }
        },
      }
    },

    methods : {

      onDetach(id){

        this.associateId = id;

        this.showModal = true;
      },

      onClose(){

        this.showModal = false;
      }
    },

		components : {
			
      'data-table': require('components/Extra/DataTable'),

      'asset-associate-detach' : require('./AssetAssociateDetach.vue')
		}
	};
</script>

<style type="text/css">

	#asset_associates .VueTables .table-responsive {
    overflow-x: auto;
  }

  #asset_associates .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
  }

  #asset_associates .VueTables .table-responsive > table > th, #asset_associates .VueTables .table-responsive > table > td{
    max-width: 250px;
    word-break: break-all;
  }
</style>