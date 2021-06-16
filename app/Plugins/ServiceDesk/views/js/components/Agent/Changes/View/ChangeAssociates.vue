<template>

  <div id="changes-associates">

    <alert componentName="changesAssociates"/>

    <div v-if="!loading" class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <li v-for="section in tabs" :class="{ active: category === section.id }">

          <a id="changes_tab" data-toggle="tab" @click="associates(section.id)">

            {{lang(section.title)}}
          </a>
        </li>
      </ul>

      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <div>

            <change-activity v-if="category === 'activity'" :changeId="changeId"></change-activity>

            <change-release v-if="category === 'releases'" :release="change.attach_releases">
              
            </change-release>

            <change-cab v-if="category === 'cabs'" :changeId="changeId"></change-cab>

            <change-associates-table v-if="category === 'assets'" componentTitle="changeAssets"
              :apiUrl="'/service-desk/api/asset-list?change_ids='+ changeId" :changeId="changeId" 
              :category="category">

            </change-associates-table>

            <change-details-tab v-if="category === 'details'" :changeId="changeId"></change-details-tab>

             <change-tickets v-if="category === 'tickets'" :changeId="changeId" componentTitle="ChangeTickets">
               
             </change-tickets>
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

    name : 'change-associates',

    description : 'Changes associates page',

    props : {

      changeId : { type : String | Number, default : '' },

      change : { type : Object, default : ()=>{} },
    },

    data(){

      return {

        tabs:[
          {id : 'details', title : 'planning'},
          {id : 'assets', title : 'associated_assets'},
          {id : 'activity', title : 'activity_log'},
          {id : 'releases', title : 'associated_releases'},
          {id : 'cabs', title : 'associated_cabs'},
          {id : 'tickets', title : 'associated_tickets'},
        ],

        category : 'details',

        loading : false
      }
    },

    created(){

      window.eventHub.$on('ChangeAssociatesAction',this.associateMethod)
    },
    
    methods : {

      associateMethod(tab){

        this.loading = true;

        setTimeout(()=>{
          
          this.loading = false;

          this.associates(tab);
        },3000)
      },

      associates(category){

				this.category = category;
			}
    },

    components : {

      'change-associates-table' : require('./MiniComponents/ChangeAssociatesTable'),

      'alert' : require('components/MiniComponent/Alert'),

      'change-activity' : require('./MiniComponents/ChangeActivity'),

      'change-details-tab' : require('./MiniComponents/ChangeDetailsTab'),

      'change-release' : require('./MiniComponents/ChangeRelease'),

      'change-cab' : require('./MiniComponents/ChangeCabDetails'),

      'change-tickets' : require('./MiniComponents/ChangeTickets'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    }
  };
</script>

<style scoped>
  
  #changes_tab{
    cursor: pointer;
  }
</style>