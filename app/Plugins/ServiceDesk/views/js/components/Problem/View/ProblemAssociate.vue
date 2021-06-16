<template>

  <div v-if="tabs" id="changes-associates">

    <alert componentName="problemAssociates"/>

    <div v-if="!loading" class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <template v-for="section in tabs">
            
          <li v-if="section.show" :class="{ active: category === section.id }">

            <a id="changes_tab" data-toggle="tab" @click="associates(section.id)">

              {{lang(section.title)}}
            </a>
          </li>
        </template>
      </ul>

      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <div>

            <problem-planning v-if="category == 'details'" :problemId="problemId"></problem-planning>

          	<problem-associated-assets v-if="category == 'assets'" componentTitle="problemAssets"
              :apiUrl="'/service-desk/api/asset-list?problem_ids='+ problemId" :problemId="problemId" 
              :category="category">

            </problem-associated-assets>

            <problem-activity v-if="category == 'activity'" :problemId="problemId"></problem-activity>

            <problem-change-details v-if="category == 'changes'" :change="problem.change">
              
            </problem-change-details>

             <problem-associated-tickets v-if="category == 'tickets'" :problemId="problemId" componentTitle="ProblemTickets">
               
             </problem-associated-tickets>
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

    name : 'problem-associates',

    description : 'Problem associates page',

    props : {

      problemId : { type : String | Number, default : '' },

      problem : { type : Object, default : ()=>{} },
    },

    data(){

      return {

        tabs:'',

        category : 'activity',

        loading : false
      }
    },

    created(){

      window.eventHub.$on('problemActionsList',this.getActionsList);
    },
    
    methods : {

      getActionsList(actions) {

        this.tabs =  [
          {id : 'activity', title : 'activity_log',show:true},
           {id : 'details', title : 'planning',show:actions.check_planning},
          {id : 'assets', title : 'associated_assets',show:actions.associated_asset},
          {id : 'changes', title : 'associated_changes',show:actions.associated_change},
          {id : 'tickets', title : 'associated_tickets',show:actions.associated_ticket},
        ]
      },

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

      'problem-associated-assets' : require('./MiniComponents/Associates/ProblemAssociatedAssets'),

      'problem-planning' : require('./MiniComponents/Associates/ProblemPlanning'),

      'alert' : require('components/MiniComponent/Alert'),

      'problem-activity' : require('./MiniComponents/Associates/ProblemActivity'),

      'problem-change-details' : require('./MiniComponents/Associates/ProblemChangeDetails'),

      'problem-associated-tickets' : require('./MiniComponents/Associates/ProblemAssociatedTickets'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    }
  };
</script>

<style scoped>
  
  #changes_tab{
    cursor: pointer;
  }
</style>