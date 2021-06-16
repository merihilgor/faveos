<template>

  <div class="box box-primary">

    <div class="box-header with-border">

      <h3 class="box-title" v-tooltip="problem.subject"> {{ subString(problem.subject,50) }}</h3>

      <problem-actions :problem="problem" :updateChangeDetails="updateData"></problem-actions>
    </div>

    <div class="box-body">

      <div class="row">

        <div class="col-md-12">

          <div class="callout callout-info">

            <div class="row">

              <div class="col-md-4">

                <b>{{lang('created_date')}} : </b>

                <span  v-tooltip="formattedTime(problem.created_at)"> {{formattedTime(problem.created_at)}}</span>
              </div>

              <div class="col-md-4">

                <b>{{lang('status')}} : </b>

                <span v-tooltip="problem.status_type_id.name"> {{subString(problem.status_type_id.name)}}</span>
              </div>

              <div class="col-md-4">

                <b>{{lang('requester')}} : </b>

                <span  v-tooltip="problem.requester_id.name ? problem.requester_id.name : problem.requester_id.email">
                	{{problem.requester_id.name ? subString(problem.requester_id.name) : subString(problem.requester_id.email)}}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-md-12">

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('department') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="problem.department_id.name" href="javascript:void(0)">{{subString(problem.department_id.name)}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('priority') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="problem.priority_id.name" href="javascript:void(0)"> {{subString(problem.priority_id.name)}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('impact') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="problem.impact_id.name" href="javascript:void(0)"> {{subString(problem.impact_id.name)}}</a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('location') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="problem.location_id ? problem.location_id.name : '---'" href="javascript:void(0)"> 
                {{problem.location_id ? subString(problem.location_id.name) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('assigned') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="problem.assigned_id ? problem.assigned_id.name : '---'" href="javascript:void(0)">
                {{problem.assigned_id ? subString(problem.assigned_id.name) : '---'}}
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

              <span v-if="problem.attachment">

              	<span v-tooltip="problem.attachment.value">
              		<i :class="icon"></i> {{subString(problem.attachment.value,15)}} {{'('+formatBytes(problem.attachment.size)+')'}}
              	</span>
                
                <a id="download" v-tooltip="lang('click_to_download')" @click="downloadFile(problem.attachment.id)"> 
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

      <problem-description  v-if="showDescription" :onClose="onClose" :showModal="showDescription" 
      	:description="problem.description">

      </problem-description>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios';

	import { mapGetters } from 'vuex'

  export default {

    name : 'problem-details',

    description : 'Problem details page',

    props : {

      problem : { type : Object, default : ()=>{} },

      updateData  : { type : Function }
    },

    data(){

      return {

        showDescription : false,
      }
    },

    computed :{
		  
      icon(){

        var attach = this.problem.attachment;

        if(attach){
          
          switch (attach.type) {

            case 'pdf':
              return 'fa fa-file-pdf-o';
              break;

            case 'xls':
              return 'fa fa-file-excel-o'
              break;

            case 'txt':
            case 'text':
              return 'fa fa-file-text-o'
              break;

            case 'png':
            case 'jpg':
            case 'jpeg':
            case 'gif':
              return 'fa fa-file-picture-o'  
              break;

            default:
              return 'fa fa-file'
              break;

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

        window.open(this.basePath() + '/service-desk/download/'+id+'/sd_problem:'+this.problem.id+'/attachment')

      }
    },
    
    components : {
    
      'problem-actions' : require('./MiniComponents/ProblemAction'),
    
      'problem-description' : require('./MiniComponents/ProblemDescriptionPage')
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