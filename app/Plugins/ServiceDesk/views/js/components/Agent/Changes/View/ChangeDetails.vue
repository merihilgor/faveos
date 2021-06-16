<template>

  <div class="box box-primary">

    <div class="box-header with-border">

      <h3 class="box-title" :title="change.subject"> {{ subString(change.subject,50) }}</h3>

      <change-actions :change="change"></change-actions>
    </div>

    <div class="box-body">

      <div class="row">

        <div class="col-md-12">

          <div class="callout callout-info">

            <div class="row">

              <div class="col-md-4" :title="formattedTime(change.created_at)">

                <b>{{lang('created_date')}} : </b> {{formattedTime(change.created_at)}}
              </div>

              <div class="col-md-4" :title="change.status.name">

                <b>{{lang('status')}} : </b> {{subString(change.status.name)}}
              </div>

              <div class="col-md-4" :title="change.requester.name ? change.requester.name : change.requester.email">

                <b>{{lang('requester')}} : </b> {{change.requester.name ? subString(change.requester.name) : subString(change.requester.email)}}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-md-12">

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('change_type') }}</label></div>

            <div class="col-md-6">

              <a :title="change.change_type.name" href="javascript:void(0)">{{change.change_type.name}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('impact') }}</label></div>

            <div class="col-md-6">

              <a :title="change.impact_type ? change.impact_type.name : '---'" href="javascript:void(0)"> 
                {{change.impact_type ? subString(change.impact_type.name) : '---'}}
              </a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('location') }}</label></div>

            <div class="col-md-6">

              <a :title="change.location ? change.location.name : '---'" href="javascript:void(0)">
                {{change.location ? subString(change.location.name) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('department') }}</label></div>

            <div class="col-md-6">

              <a :title="change.department ? change.department.name : '---'" href="javascript:void(0)"> 
                {{change.department ? subString(change.department.name) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('team') }}</label></div>

            <div class="col-md-6">

              <a :title="change.team ? change.team.name : '---'" href="javascript:void(0)">
                {{change.team ? subString(change.team.name) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('priority') }}</label></div>

            <div class="col-md-6">

              <a :title="change.priority ? change.priority.name : '---'" href="javascript:void(0)">
                {{change.priority ? subString(change.priority.name) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('description') }}</label></div>

            <div class="col-md-6">

              <a href="javascript:;" class="btn btn-info btn-xs" @click="showDescription = true">

                <i class="fa fa-file-text">&nbsp;&nbsp;</i>{{lang('show_description')}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('attachment') }}</label></div>

            <div class="col-md-6">

              <span v-if="change.attachment" :title="change.attachment.value">

                <i :class="icon"></i> {{subString(change.attachment.value,15)}} {{'('+formatBytes(change.attachment.size)+')'}}
                
                <a id="download" title="Click to download" @click="downloadFile(change.attachment.id)"> 
                  <i class="fa fa-download"></i>
                </a>
              </span>
              
              <span v-else>---</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <transition name="modal">

      <change-description  v-if="showDescription" :onClose="onClose" :showModal="showDescription" :description="change.description">

      </change-description>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios';

	import { mapGetters } from 'vuex'

  export default {

    name : 'change-details',

    description : 'Changes details page',

    props : {

      change : { type : Object, default : ()=>{} },
    },

    data(){

      return {

        showDescription : false,
      }
    },

    computed :{
		  
      icon(){

        var attach = this.change.attachment;

        if(attach){
          
          if(attach.type === 'pdf'){

            return 'fa fa-file-pdf-o'
          
          } else if(attach.type === 'xls'){

            return 'fa fa-file-excel-o'
          
          } else if(attach.type === 'txt' || attach.type === 'text'){

            return 'fa fa-file-text-o'
          
          } else if(attach.type === 'png' || attach.type === 'jpg' || attach.type === 'jpeg' || attach.type === 'gif'){

            return 'fa fa-file-picture-o'  
          
          } else {

            return 'fa fa-file'
          }     
        }
      },

    	...mapGetters(['formattedTime','formattedDate'])
		},
    
    methods : {
    
      subString(value,length = 15){
    
        return getSubStringValue(value,length)
      },
    
      formatBytes(bytes,decimals) {
		
    		if(bytes == 0) return '0 Bytes';
		
    		var k = 1024,
		
    		dm = decimals || 2,
		
    		sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
		
    		i = Math.floor(Math.log(bytes) / Math.log(k));
		
    		return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
			},
    
      onClose(){
		
        this.showDescription = false;
		
        this.$store.dispatch('unsetValidationError');
		  },

      downloadFile(id){

        window.open(this.basePath() + '/service-desk/download/'+id+'/sd_changes:'+this.change.id+'/attachment')

      }
    },
    
    components : {
    
      'change-actions' : require('./MiniComponents/ChangeActions'),
    
      'change-description' : require('./MiniComponents/ChangeDescription')
    }
  };
</script>

<style scoped>
  .info-row{
    border-top: 1px solid #f4f4f4; padding: 10px;
  }
  #download{
    cursor: pointer;
  }
</style>