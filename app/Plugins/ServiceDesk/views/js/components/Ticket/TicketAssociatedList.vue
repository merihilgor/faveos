<template>

	<div id="ticket-associated-list">

    <div v-if="!loading" class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <li v-for="section in tabs" :class="{ active: category === section.id }">

          <a id="associates_tab" data-toggle="tab" @click="associates(section.id)">

            {{lang(section.title)}}
          </a>
        </li>

        <li class="pull-right">

          <a id="associates_tab" class="text-muted" @click="refreshAssociates" v-tooltip="lang('click_to_refresh')" >

            <i class="fa fa-refresh"></i>
          </a>
        </li>
      </ul>

      <div class="tab-content">

        <div class="active tab-pane" id="activity">

        	<component v-bind:is="currentComponent" :data="componentData" from="tab" :alert="alertName"></component>
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

		name : 'ticket-associated-list',

		description : 'Tickets Assocites List',

		props : {

			data : { type : String | Object }
		},

		data() {

			return {

				tabs:[
        
          {id : 'assets', title : 'associated_assets'},
        
          {id : 'changes', title : 'associated_changes'},
        
          {id : 'contracts', title : 'associated_contracts'},
        ],

        category : 'assets',

        alertName : '',

        loading : false,
			}
		},

    beforeMount() {

      if(JSON.parse(this.data).from === 'org' || JSON.parse(this.data).from === 'user') {

        this.tabs = [
        
          {id : 'assets', title : 'associated_assets'},
        
          {id : 'contracts', title : 'associated_contracts'},
        ];

        this.alertName = ''

      } else {
        
        this.alertName = 'timeline';
      }
    },

		created(){

      window.eventHub.$on('TicketChanged',this.associateMethod)
    },

    computed : {

    	currentComponent(){
        
        let option = this.category;

        return option === 'assets' ? 'associated-asset-list' : option === 'changes' ? 'associated-changes' : 'associated-contracts'  
      },

      componentData() {

        // let details = JSON.parse(this.data);

        // if(details.from === 'user' && category === 'contracts') {

        //   return '{}'
        
        // } else {

          return this.data;
      //   }
      }
    },

		methods : {

			associateMethod(){

				this.category = 'assets';

        this.loading = true;

        setTimeout(()=>{
          
          this.loading = false;

          this.associates(this.category);
        },3000)
      },

      refreshAssociates() {

        this.category = 'assets';
        
        window.eventHub.$emit('AssetListrefreshData');
      },

			associates(category){

				this.category = category;
			}
		},

		components : {

			'associated-asset-list' : require('../../components/AssociatedAssetList.vue'),

			'associated-contracts' : require('../../components/Ticket/AssociatedContractList.vue'),

			'associated-changes' : require('../../components/Ticket/Changes/AssociatedChanges.vue'),

			'loader' : require('components/Client/Pages/ReusableComponents/Loader')
		}
	};
</script>

<style scoped>
	#associates_tab{
		cursor: pointer;
	}
  .text-muted { color: #777 !important; }
</style>