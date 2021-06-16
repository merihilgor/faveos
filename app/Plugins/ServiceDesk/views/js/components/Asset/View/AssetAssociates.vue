<template>

  <div  v-if="tabs" id="asset-associates">

    <alert componentName="assetAssociates"/>

    <div v-if="!loading" class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <template v-for="section in tabs">
            
          <li v-if="section.show" :class="{ active: category === section.id }">

            <a id="asset_tab" data-toggle="tab" @click="associates(section.id)">

              {{trans(section.title)}}
            </a>
          </li>
        </template>
      </ul>

      <div class="tab-content">

        	<div class="active tab-pane" id="activity">

         	<div>

         		<asset-activity v-if="category == 'activity'" :assetId="assetId"></asset-activity>

         		<asset-associate-table v-else :category="category" :columns="columns" :assetId="assetId" :sortable="sortable"
         			:filterable="filterable" :componentTitle="'Asset'+category">

         		</asset-associate-table>
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
  
  import axios from 'axios'

  export default {

    name : 'asset-associates',

    description : 'Asset associates page',

    props : {

      assetId : { type : String | Number, default : '' }
    },

    data(){

      return {

        tabs:'',

        category : 'activity',

        loading : false
      }
    },

    beforeMount(){

    	this.getActionsList();
    },

    created() {

    	window.eventHub.$on('updateAssetAssociates',this.updateAssociates);

    	window.eventHub.$on('updateAssetActivityLog',this.refreshTabs);
    },

    computed : {

    	columns() {

    		switch(this.category) {

    			case 'problem':
    				return ['identifier', 'subject', 'requester','assignedTo','priority', 'department', 'status_type_id','action'];

    			case 'change':
    				return ['identifier','subject','requester','status','priority','department','created_at','action'];

    			case 'release':
    				return ['identifier','subject','release_type','priority', 'status','planned_start_date','planned_end_date','action'];

    			case 'contract':
    				return ['identifier','name','cost','expiry','contract_status','contract_renewal_status','vendor','contract_type','action'];

    			case 'ticket':
    				return ['ticket_number', 'title', 'assigned','action'];
    		}
    	},

    	filterable() {

    		switch(this.category) {

    			case 'problem':
    				return ['subject', 'identifier'];

    			case 'change':
    				return ['identifier','subject','requester','status','priority','department'];

    			case 'release':
    				return ['identifier','subject','release_type','priority', 'status','planned_start_date','planned_end_date'];

    			case 'contract':
    				return ['identifier','name','cost','expiry','contract_status','contract_renewal_status','vendor','contract_type'];

    			case 'ticket':
    				return ['ticket_number'];
    		}
    	},

    	sortable() {

    		switch(this.category) {

    			case 'problem':
    				return ['subject', 'identifier'];

    			case 'change':
    				return ['identifier','subject','created_at'];

    			case 'release':
    				return ['subject', 'identifier', 'planned_start_date', 'planned_end_date'];

    			case 'contract':
    				return ['identifier','name', 'contract_number', 'expiry','cost'];

    			case 'ticket':
    				return ['ticket_number'];
    		}
    	}
    },
    
    methods : {

    	refreshTabs(value){

    		this.getActionsList();

    		this.associates(value)
    	},

      getActionsList() {

	      axios.get('/service-desk/api/asset-action/'+this.assetId).then(res=>{

      		let actions = res.data.data.asset_actions;

      		this.tabs =  [
	          	
	          	{id : 'activity', title : 'activity_log',show:true},
	           	
	           	{id : 'problem', title : 'associated_problems',show:actions.associated_problems_tab_viewable},
	          	
	          	{id : 'change', title : 'associated_changes',show:actions.associated_changes_tab_viewable},
	          	
	          	{id : 'release', title : 'associated_releases',show:actions.associated_releases_tab_viewable},
	          	
	          	{id : 'contract', title : 'associated_contracts',show:actions.associated_contracts_tab_viewable},
	          	
	          	{id : 'ticket', title : 'associated_tickets',show:actions.associated_tickets_tab_viewable},
	        ];

      	}).catch(err=>{

      		this.tabs = [];
      	});
      },

      updateAssociates(value){

      	this.getActionsList();
      	
      	this.associates(value.slice(0, -1));
      },

      associates(category){

      	this.loading = true;

        setTimeout(()=>{ this.loading = false; },1)

			this.category = category;
		}
    },

    components : {

      'alert' : require('components/MiniComponent/Alert'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),

      'asset-activity':require('./Mini/AssetActivity'),

      'asset-associate-table':require('./Mini/AssetAssociateTable'),
    }
  };
</script>

<style scoped>
  
  #asset_tab{
    cursor: pointer;
  }
</style>