<template>

  <div>

    <data-table :url="apiEndPoint" :dataColumns="columns"  :option="options" 
      scroll_to="problem-associates" :componentTitle="componentTitle">

    </data-table>

    <transition name="modal">

      <ticket-detach-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :ticketId="ticketId"
        :problemId="problemId" :type="ticket_type"> 
        
      </ticket-detach-modal>

    </transition> 
  </div>
</template>

<script>

  import Vue from 'vue'

	export default {

    name : 'problem-tickets-table',

		description : 'Problem tickets table page',

		props : {

      problemId : { type : String | Number, default : ''},

      componentTitle : { type : String, default : 'ProblemTickets'},
    },

		data() {

			return {

        selectedTickets : this.tickets,

        columns: ['ticket_number', 'subject', 'assignedTo','action'],

        options : {},

        apiEndPoint : '/service-desk/api/problem-tickets/'+this.problemId,

        ticketId : '',

        ticket_type : '',

        showModal : false,
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

          ticket_number : 'Ticket Number',

          assignedTo : 'Assigned to',
        },

        columnsClasses : {

          ticket_number : 'associate-number',

          subject : 'associate-subject',

          assignedTo : 'associate-assigned',

          type : 'associate-type',

          action : 'associate-action',
        },

        texts : {

          filter : '',

          limit : ''
        },

        templates: {

          subject: function(createElement, row) {

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/thread/'+ row.id,
              }
            }, row.subject);
          },

          ticket_number: function(createElement, row) {

             return createElement('a', {

              attrs: {

                href: self.basePath() + '/thread/'+ row.id,
              }
            }, '#'+row.ticket_number);
          },

          assignedTo: function(createElement, row) {

            if(row.assignedTo){

              return createElement('a', {

                attrs: {

                  href: self.basePath() + '/user/'+ row.assignedTo.id,
                }
              }, row.assignedTo.full_name);
            } else { return '--'}
          },

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
                  self.onDetach(row.id, row.type);
                }
              }
            }, [icon]);
          }
        },

        sortable:  ['ticket_number'],

        sortable:  ['ticket_number'],

        pagination:{chunk:5,nav: 'scroll'},

        requestAdapter(data) {

          return {

            'sort-field': data.orderBy ? data.orderBy : 'id',

            'sort-order': data.ascending ? 'desc' : 'asc',

            'search_query':data.query.trim(),

            page:data.page,

            limit:data.limit,
          }
        },

        responseAdapter({data}) {

          return {

            data: data.data.tickets,

            count: data.data.total
          }
        },
      }
    },

    methods : {

      onDetach(id, type){

        this.ticketId = id;

        this.ticket_type = type;

        this.showModal = true;
      },

      onClose(){

        this.showModal = false;

        window.eventHub.$emit('updateProblemActions');
      }
    },

		components : {
			
      'data-table': require('components/Extra/DataTable'),

      'ticket-detach-modal' : require('./ProblemTicketDetach.vue')
		}
	};
</script>

<style type="text/css">
  
  .table-bordered { font-size: 14px !important; }
  
	.associate-number{
    width: 23%;
    word-break: break-all;
  }

  .associate-subject{
    width: 27%;
    word-break: break-all;
  }

  .associate-assigned{
    width: 27%;
    word-break: break-all;
  }

  .associate-type{
    width: 13%;
    word-break: break-all;
  }

  .associate-action{
    width: 10%;
    word-break: break-all;
  }
</style>