<template>

  <div class="box box-primary">

    <div class="box-header with-border">

      <h3 class="box-title" v-tooltip="release.subject"> {{ subString(release.subject,50) }}</h3>

      <release-actions :release="release" :updateReleaseDetails="updateData"></release-actions>
    </div>

    <div class="box-body">

      <div class="row">

        <div class="col-md-12">

          <div class="callout callout-info">

            <div class="row">

              <div class="col-md-4">

                <b>{{lang('created_date')}} : </b>

                <span  v-tooltip="formattedTime(release.created_at)"> {{formattedTime(release.created_at)}}</span>
              </div>

              <div class="col-md-4">

                <b>{{lang('status')}} : </b>

                <span v-tooltip="release.status.name"> {{subString(release.status.name)}}</span>
              </div>

              <div class="col-md-4">

                <b>{{lang('release_type')}} : </b>

                <span  v-tooltip="release.release_type.name"> {{ subString(release.release_type.name) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-md-12">

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('planned_start_date') }}</label></div>

            <div class="col-md-6">

              <a v-if="release.planned_start_date && release.planned_start_date != '--' && release.planned_start_date != '0000-00-00 00:00:00'" v-tooltip="formattedTime(release.planned_start_date)" href="javascript:;">

              		{{formattedTime(release.planned_start_date)}}
              </a>

              <a v-else>---</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('planned_end_date') }}</label></div>

            <div class="col-md-6">

              <a v-if="release.planned_end_date && release.planned_end_date != '--' && release.planned_end_date != '0000-00-00 00:00:00' " v-tooltip="formattedTime(release.planned_end_date)" href="javascript:;">

              		{{formattedTime(release.planned_end_date)}}
              </a>

               <a v-else>---</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('priority') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="release.priority.name" href="javascript:void(0)"> {{subString(release.priority.name)}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('identifier') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="checkValues(release.identifier)" href="javascript:;"> 
              		
              		{{ release.identifier | checkValue(15)}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('location') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="checkValues(release.location)" href="javascript:void(0)">  {{release.location | checkValue(15)}}</a>
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

              <span v-if="release.attachment.length > 0">

              	<span v-tooltip="release.attachment[0].value">
              		<img :src="release.attachment[0].icon_url" class="attach_img"/>  
                    {{subString(release.attachment[0].value,15)}} {{'('+release.attachment[0].size+')'}}
              	</span>
                
                <a id="download" v-tooltip="lang('click_to_download')" @click="downloadFile(release.attachment[0].id)"> 
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

      <release-description  v-if="showDescription" :onClose="onClose" :showModal="showDescription" 
      	:description="release.description">

      </release-description>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios';

	import { mapGetters } from 'vuex'

  export default {

    name : 'release-details',

    description : 'Release details page',

    props : {

      release : { type : Object, default : ()=>{} },

      updateData  : { type : Function }
    },

    data(){

      return {

        showDescription : false,
      }
    },

    computed :{

    	...mapGetters(['formattedTime','formattedDate'])
		},
    
    methods : {

      checkValues(value) {
      
        return this.$options.filters.checkValue(value);
      },

      subString(value,length = 15){
    
        return getSubStringValue(value,length)
      },
    
      onClose(){
		
        this.showDescription = false;
		
        this.$store.dispatch('unsetValidationError');
		  },

      downloadFile(id){

        window.open(this.basePath() + '/service-desk/download/'+id+'/sd_releases:'+this.release.id+'/attachment')

      }
    },
    
    components : {
    
      'release-actions' : require('./Mini/ReleaseActions'),
    
      'release-description' : require('./Mini/ReleaseDescription')
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
  .attach_img { width: 15px; height: 15px; }
</style>