<template>

  <div v-if="tabs" id="release-associates">

    <alert componentName="releaseAssociates"/>

    <div v-if="!loading" class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <template v-for="section in tabs">
            
          <li v-if="section.show" :class="{ active: category === section.id }">

            <a id="release_tab" data-toggle="tab" @click="associates(section.id)">

              {{trans(section.title)}}
            </a>
          </li>
        </template>
      </ul>

      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <div>

            <release-planning v-if="category == 'planning'" :releaseId="release.id"></release-planning>

            <release-associated-assets v-if="category == 'assets'" componentTitle="releaseAssets" :actions="actions"
              :apiUrl="'/service-desk/api/asset-list?release_ids='+ release.id" :releaseId="release.id" :category="category">

            </release-associated-assets>

            <release-associated-changes v-if="category == 'changes'" :releaseId="release.id" :actions="actions"
            	:apiUrl="'/service-desk/api/change-list?release_ids='+ release.id">

            </release-associated-changes>

            <release-activity v-if="category == 'activity'" :releaseId="release.id" ></release-activity>
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

    name : 'release-associates',

    description : 'Release associates page',

    props : {

      release : { type : Object, default : ()=>{} },
    },

    data(){

      return {

        tabs:'',

        category : 'activity',

        loading : false,

        planning : true,

        actions : ''
      }
    },

    beforeMount() {

      this.getTabs(this.planning)
   },

   created() {

    window.eventHub.$on('updateAssociatesTab',this.getTabs);

   	window.eventHub.$on('updateAssociatesTableActions',this.getActions);
   },
    
    methods : {

      getActions(value) {

       this.actions = value;
      },

      getTabs(value) {

        	this.tabs =  [
         
	         {id : 'activity', title : 'activity_log',show:true},

	         {id : 'planning', title : 'planning',show:value},
	          
	         {id : 'assets', title : 'associated_assets',show:this.release.attach_assets.length > 0},
	         
	         {id : 'changes', title : 'associated_changes',show:this.release.attach_changes.length > 0}
	      ]
      },

      associates(category){

        this.category = category;
      }
    },

    components : {

      'alert' : require('components/MiniComponent/Alert'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),

      'release-activity':require('./Mini/ReleaseActivity'),

      'release-planning':require('./Mini/ReleasePlanning'),

      'release-associated-assets':require('./Mini/ReleaseAssociatedAssets'),

      'release-associated-changes':require('./Mini/ReleaseAssociatedChanges'),
    }
  };
</script>

<style scoped>
  
  #release_tab{
    cursor: pointer;
  }
</style>