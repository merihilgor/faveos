<template>

 <span>

  <a class="user_name pop" :href="showUser(from)" data-container="body" data-toggle="popover" data-placement="right" 
    :data-content="hoverContent" target="_blank">
    
    <span class="emphasize"> {{ subString(fullName) }}
      
      <span>{{ subString(getUserName) }}</span>
    </span>
  </a>

  <span v-if="isUnassigned" style="color: red">{{lang('unassigned')}}</span>
</span>

</template>

<script>
  
  import { boolean,getSubStringValue } from 'helpers/extraLogics';

  export default{
    
    props : {

      from : { type : Object, default : ()=> {} },

      tableHeader : { type : String, default : '' },

    },

    computed:{
      
      fullName() {
        
        return boolean(this.from) ? `${this.from.first_name} ${this.from.last_name}` : '';
      },
      
      getUserName() {
        
        return (boolean(this.from) && (this.from.first_name=='' || this.from.first_name==null) && this.from.user_name) ? this.from.user_name : '';
      },
      
      isUnassigned() {
        
        return (this.fullName === '' && this.getUserName === ''); 
      },
      
      hoverContent(){

        if(boolean(this.from)){

          return `
            <div class='box-widget widget-user-2 cus-container'>

              <div class='widget-user-header bg-blue custom-head' style='color : ${this.tableHeader}'>

                <div class='widget-user-image'>

                  <img class='img-circle user-img' alt='User Avatar' src=${this.from.profile_pic}>
                </div>

                <h3 class='widget-user-username custom-name'>

                  <a href=${this.basePath()+'/user/'+this.from.id}>
                  ${this.from.first_name !== '' ?  this.from.first_name+" "+this.from.last_name : this.from.user_name}
                  </a>
                </h3>
              </div>

              <div class='box-footer no-padding'>

                <ul class='nav nav-stacked'>

                  <li id='b_bottom'>
                    <a id='cus-link' title=${this.from.email}>

                      <i class='fa fa-envelope'></i>&nbsp;
                        ${this.from.email  ? this.subString(this.from.email) : "---"}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          `
        } else { return '' }
      }
    },
   
    methods:{

      subString(value,length = 25){

        return getSubStringValue(value,length)
      },
      
      //show user page
      showUser(x){
        
        return boolean(x) ? this.basePath()+'/user/'+x.id : '';
      }
    }
  };
</script>

<style>

  .popover-content { padding: 5px 5px !important; }
  
  .user-img{ margin: auto; border: 1px solid #d2d6de; border-radius: 50px; width: 30px !important; 
    height: 30px !important;
  }
  
  .custom-head { padding: 7px !important; min-height: 54px !important;}

  .custom-name { margin-left: 35px !important; font-size: 15px !important;}

  .custom-name a{ color : white !important; }

  #cus-link { padding-left : 5px !important;}
  
  #b_bottom { border-bottom: 0px !important }
</style>

<style scoped>
  
  .padding-div{ padding-left:12px;padding-right:0px; }

  .break{ font-size:13px;word-wrap:break-word;}

  .popover{ min-width: 220px; border-radius: 0px;}

  .popover-title{ padding: 0px;}
</style>
