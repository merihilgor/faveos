<template>

  <div v-if="tabs" id="contract-associates">

    <alert componentName="contractAssociates"/>

    <div v-if="!loading" class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <template v-for="section in tabs">
            
          <li v-if="section.show" :class="{ active: category === section.id }">

            <a id="contract_tab" data-toggle="tab" @click="associates(section.id)">

              {{trans(section.title)}}
            </a>
          </li>
        </template>
      </ul>

      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <div>

            <contract-history v-if="category == 'history'" :contractId="contract.id"></contract-history>

          	<contract-associated-assets v-if="category == 'assets'" componentTitle="contractAssets" :actions="actions"
              :apiUrl="'/service-desk/api/asset-list?contract_ids='+ contract.id" :contractId="contract.id" 
              :category="category">

            </contract-associated-assets>

            <contract-activity v-if="category == 'activity'" :contractId="contract.id"></contract-activity>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" class="row">

      <loader :animation-duration="4000" :size="60"/>
    </div>
  </div>
</template>

<script>

  export default {

    name : 'contract-associates',

    description : 'Contract associates page',

    props : {

      contract : { type : Object, default : ()=>{} },
    },

    data(){

      return {

        tabs:'',

        category : 'activity',

        loading : false,

        actions : ''
      }
    },

    created() {

      window.eventHub.$on('updateContractTableActions',this.getActions);
    },

    beforeMount() {

    	this.getTabs()
   },
    
    methods : {

      getActions(value) {

       this.actions = value;
      },

      getTabs() {

        this.tabs =  [
         
         {id : 'activity', title : 'activity_log',show:true},
          
         {id : 'history', title : 'contract_history',show:true},
         
         {id : 'assets', title : 'associated_assets',show:this.contract.attach_assets.length > 0}
        ]
      },

      associates(category){

  			this.category = category;
  		}
    },

    components : {

      'alert' : require('components/MiniComponent/Alert'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),

      'contract-activity':require('./Mini/ContractActivity'),

      'contract-history':require('./Mini/ContractHistory'),

      'contract-associated-assets':require('./Mini/ContractAssets'),
    }
  };
</script>

<style scoped>
  
  #contract_tab{
    cursor: pointer;
  }
</style>