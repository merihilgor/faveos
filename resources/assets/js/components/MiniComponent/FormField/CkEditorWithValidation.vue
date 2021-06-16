<template>
 
  <div class="form-group" id="reply_content_class">
          
    <div class="col-md-12">
            
      <label :style="[show_error ? {'color': '#dc3545 '} : {'color': 'black'}]" for="Reply Content">
        
        {{ lang('description') }}
      
      </label>
            
      <span class="text-red"> *</span>
          
      <ck-editor :value="description" :noDropdown="noDropdown"></ck-editor>
            
      <span v-if="show_error" class="help-block">This field is required</span>
        
    </div>
  
  </div>

</template>

<script type="text/javascript">
  
  export default {

    name : 'ck-editor-with-validation',

    description : 'Ckeditor with validation component',

    props : {
      
      description : { type : String, default : ''},

      classname : { type : String, default : 'form-group'},
      
      show_error : { type : Boolean, default : false},

      noDropdown : { type : Boolean , default : false}
    
    },

    components : {    
    
      'ck-editor': require('components/MiniComponent/FormField/CkEditor.vue'),
    
    },

    data(){
      return {
        
      }
    },

    computed:{

    },

    watch : {
      noDropdown(newValue,oldValue){
        return newValue
      }
    },

    methods : {
     
    }
  };
</script>

<style type="text/css" scoped>
 
</style>