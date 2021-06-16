<template>

  <div>
  
    <alert componentName="dataTableModal"/>

    <div class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <li v-for="section in tabs" :class="{ active: category === section.id }" :key="section.id">

          <a id="settings_tab" data-toggle="tab" @click="associates(section.id)">

            {{lang(section.title)}}
          </a>
        </li>
      </ul>

      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <component v-bind:is="currentComponent" :category="category"></component>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  export default {

    name : 'task-settings',

    description : 'Task Settings page',

    data(){

      return {

        tabs:[
          
          {id : 'project', title : 'project'},
          
          {id : 'tasklist', title : 'tasklist'},
        ],

        category : 'project',

        loading: false,
      }
    },

    computed :{
      
      currentComponent(){
        
        let option = this.category;

        return option === 'project' ? 'task-project' : 'task-list'  
      }
    },

    methods : {

      associates(category){

        this.category = category;
      }
    },

    components: {

      'task-project' : require('./MiniComponents/TaskProject'),

      'task-list' : require('./MiniComponents/TaskList'),
      
      'alert' : require('components/MiniComponent/Alert')
    },
  };
</script>

<style>
  #settings_tab{
    cursor: pointer;
    margin-bottom: -1px;
  }
</style>