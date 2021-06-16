<template>

  <div id="associated-contracts">
    
    <div :class="[{'box box-primary' : !from}]">
    
      <div v-if="from != 'tab'" class="box-header with-border">
    
        <div class="row">
    
          <div class="col-md-4">
    
            <h2 class="box-title">{{lang('associated_contracts')}}</h2>
    
          </div>
    
        </div>  
      </div>

      <div v-if="loading" class="row">
                
        <loader :animation-duration="4000" color="#1d78ff" :size="60"/>
      
      </div>

      <data-table :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>

    </div>

</div>

</template>

<script>
  
  import axios from 'axios'

  import { mapGetters } from 'vuex'

  import Vue from 'vue'

  Vue.component('contract-name', require('./MiniComponents/ContractNameComponent.vue'));

  export default {

    name : 'associated_contracts',

    description : 'Associated contracts data table page',

    props : {

      data : { type : String | Object, default : ''},

      from : { type : String, default : ''}
    },

    data() {
      return {
        
        currentComponent:'associated_contracts',
        
        loading:false,

        apiUrl: null,

        columns: null,

        options: null,

      }
    },

    computed:{
        ...mapGetters(['getStoredTicketId','formattedTime','formattedDate'])
    },

    watch:{
        getStoredTicketId(newValue,oldValue){
          this.apiUrl = '/service-desk/api/contract/ticket/'+newValue
          
        }
      },

      beforeMount(){

        const details = JSON.parse(this.data);

        if(details && details.from === 'org'){

          this.apiUrl =  '/service-desk/contract/organization/'+details.org_id;
        
        } else if(details && details.from === 'user') {

           this.apiUrl =  '/service-desk/api/contract-list?user_ids='+details.user_id;
        }else {

          this.apiUrl = '/service-desk/api/contract/ticket/'+this.getStoredTicketId
        }

        const self = this;

        this.columns = ['name', 'cost','contract_start_date','contract_end_date']

        this.options = {

          texts : { 'filter' : '', 'limit': ''},

          headings: { name: 'Name', cost: 'Cost',contract_start_date: 'Contract Start Date',contract_end_date: 'Contract End Date'},

          templates: {
            name: 'contract-name',
             contract_start_date : function(h,row){

              return self.formattedTime(row.contract_start_date)
            },

          contract_end_date : function(h,row){

              return self.formattedTime(row.contract_end_date)
            },

          },

          sortable:  ['name', 'cost','contract_start_date','contract_end_date'],

    

          pagination:{chunk:5,nav: 'scroll'},

          requestAdapter(data) {

            return {
              'sort-field': data.orderBy ? data.orderBy : 'name',
              'sort-order': data.ascending ? 'desc' : 'asc',
              'search-query':data.query.trim(),
              'page':data.page,
              'limit':data.limit,
            }

          },

          responseAdapter({data}) {
            
            return {

            data: details.from == 'user' ? data.data.contracts :  data.data.data,
            count: data.data.total
            }
          },

        }
      },

    components : {
      
      'data-table' : require('components/Extra/DataTable'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
      
      "alert": require("components/MiniComponent/Alert"),

      "contract-name": require('./MiniComponents/ContractNameComponent'),
    
    }
  
  };
</script>

<style scoped>
  #asset_tab{
    cursor: pointer;
  }
</style>