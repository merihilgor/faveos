<template>

  <div id="associated-asset-list">
    
    <alert componentName="dataTableModal"/>

    <div :class="[{'box box-primary pad_0' : !from}]">
    
      <div v-if="from != 'tab'" class="box-header with-border">
    
        <div class="row">
    
          <div class="col-md-4">
    
            <h2 class="box-title">{{lang('associated_assets')}}</h2>
    
          </div>
    
        </div>
      </div>

      <div v-if="loading" class="row">
                
        <loader :animation-duration="4000" color="#1d78ff" :size="60"/>
        
      </div>

      <associated-assets v-if="!loading" :data="data" :alertName="alert"></associated-assets>
    
    </div>

</div>

</template>

<script>
  
  import axios from 'axios'

  export default {

    name : 'associated_assets',

    description : 'Associated assets data table page',

    props : { 
       data: {type: String|Object},
       from : { type : String, default : '' },
       alert : { type : String, default : '' },
    },

    data() {
      return {
        
        currentComponent:'associated_assets',
        
        loading:false,

      }
    },

    components : {
      "associated-assets": require('./Asset/AssetList'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
      
      "alert": require("components/MiniComponent/Alert"),
    
    }
  
  };
</script>

<style scoped>
  #asset_tab{
    cursor: pointer;
  }
  .pad_0{
    padding: 0 !important;
  }
</style>