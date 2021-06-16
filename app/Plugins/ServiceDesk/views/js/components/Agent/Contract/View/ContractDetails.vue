<template>

  <div class="box box-primary">

    <div class="box-header with-border">

      <h3 class="box-title" v-tooltip="contract.name"> {{ subString(contract.name,50) }}</h3>

      <contract-actions :contract="contract" :updateContractDetails="updateData">
        
      </contract-actions>
    </div>

    <div class="box-body">

      <div class="row">

        <div class="col-md-12">

          <div class="callout callout-info">

            <div class="row">

              	<div class="col-md-4">

                	<b>{{trans('start_date')}} : </b>

                	<span  v-tooltip="formattedTime(contract.contract_start_date)"> {{formattedTime(contract.contract_start_date)}}

                	</span>
              	</div>

              	<div class="col-md-4">

                	<b>{{trans('expiry_date')}} : </b>

                	<span  v-tooltip="formattedTime(contract.contract_end_date)"> {{formattedTime(contract.contract_end_date)}}

                	</span>
              	</div>

              	<div class="col-md-4">

                	<b>{{trans('contract_type')}} : </b>

                	<span v-tooltip="contract.contractType ? contract.contractType.name : '---'"> 

                    {{contract.contractType ? subString(contract.contractType.name,20) : '---'}}
                  </span>
              	</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-md-12">

        	<div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('vendor') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="contract.vendor ? contract.vendor.name : '---'"> 

                {{ contract.vendor ? subString(contract.vendor.name) : '---'}}</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('contract_status') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="contract.status.name"> {{subString(contract.status.name)}}</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('license_type') }}</label></div>

            <div class="col-md-6">

              	<a v-tooltip="contract.licence ? contract.licence.name : '---'">
              		{{contract.licence ? subString(contract.licence.name) : '---'}}
              	</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('cost') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="contract.cost"> {{subString(contract.cost)}}</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('identifier') }}</label></div>

            <div class="col-md-6">

              	<a v-tooltip="contract.identifier ? contract.identifier : '---'">
              		{{contract.identifier ? subString(contract.identifier) : '---'}}
              	</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('contract_creator') }}</label></div>

            <div class="col-md-6">

              	<a v-tooltip="contract.owner ? contract.owner.name : '---'">
              		{{contract.owner ? subString(contract.owner.name) : '---'}}
              	</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('approver') }}</label></div>

            <div class="col-md-6">

              	<a v-tooltip="contract.approver ? contract.approver.name : '---'">
              		{{contract.approver ? subString(contract.approver.name) : '---'}}
              	</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('license_count') }}</label></div>

            <div class="col-md-6">

              	<a v-tooltip="contract.licensce_count ? contract.licensce_count.toString() : '---'">
              		{{contract.licensce_count ? subString(contract.licensce_count) : '---'}}
              	</a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('user') }}</label></div>

            <div class="col-md-6">

                <a v-tooltip="contract.user.length > 0 ? contract.user[0].name : '---'">
                  {{contract.user.length > 0 ? subString(contract.user[0].name) : '---'}}
                </a>
            </div>
         </div>

         <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('organization') }}</label></div>

            <div class="col-md-6">

                <a v-tooltip="contract.organization.length > 0 ? contract.organization[0].name : '---'">
                  {{contract.organization.length > 0 ? subString(contract.organization[0].name) : '---'}}
                </a>
            </div>
         </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('notify_before') }}</label></div>

            <div class="col-md-6">

                <a v-tooltip="contract.notify_before ? contract.notify_before.toString() : '---'">
                  {{contract.notify_before ? subString(contract.notify_before) : '---'}}
                </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('notify_to') }}</label></div>

            <div class="col-md-6">

              <template v-if="contract.notify_to.length > 0">
                  
                <a v-for="(agent,index) in contract.notify_to" v-tooltip="agent.name ? agent.name : agent.email">
                
                  {{agent.name ? agent.name : agent.email}}<span v-if="index != Object.keys(contract.notify_to).length - 1">, </span>
                </a>
              </template>

              <template v-else>  --- </template>
            </div>
          </div>

           <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('description') }}</label></div>

            <div class="col-md-6">

              <a href="javascript:;" class="btn btn-info btn-xs" @click="showDescription = true">

                <i class="fa fa-file-text">&nbsp;&nbsp;</i>{{trans('show_description')}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ trans('attachment') }}</label></div>

            <div class="col-md-6">

              <span v-if="contract.attachment.length > 0">

              	<span v-tooltip="contract.attachment[0].value">

                  <img :src="contract.attachment[0].icon_url" class="attach_img"> {{subString(contract.attachment[0].value,15)}} {{'('+contract.attachment[0].size+')'}}
              		
                </span>
                
                <a id="download" v-tooltip="trans('click_to_download')" @click="downloadFile(contract.attachment[0].id)"> 
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

      <contract-description  v-if="showDescription" :onClose="onClose" :showModal="showDescription" 
      	:description="contract.description">

      </contract-description>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios';

	import { mapGetters } from 'vuex'

  export default {

    name : 'contract-details',

    description : 'Contract details page',

    props : {

      contract : { type : Object, default : ()=>{} },

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
    
      subString(value,length = 15){
    
        return getSubStringValue(value,length)
      },
    
      onClose(){
		
        this.showDescription = false;
		
        this.$store.dispatch('unsetValidationError');
		  },

      downloadFile(id){

        window.open(this.basePath() + '/service-desk/download/'+id+'/sd_contracts:'+this.contract.id+'/attachment')

      }
    },
    
    components : {
    
      'contract-actions' : require('./Mini/ContractActions'),
    
      'contract-description' : require('./Mini/ContractDescription')
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