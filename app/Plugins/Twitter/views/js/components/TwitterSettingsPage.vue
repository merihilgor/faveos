<template>
    
    <div>

        <custom-loader v-if="loading" :duration="loadingSpeed" />
        <alert componentName="dataTableModal" />

        <div class="box box-primary">

            <div class="box-header with-border">

                <h2 class="box-title">
                    {{ lang('twitter_settings') }}
                </h2>

                

            </div> <!--box-header-->


            <div class="box-body">

                <div class="row">

                    <text-field 
                        :label="lang('consumer_api_key')" :onChange="onChange" 
                        :value="consumer_api_key" type="text" name="consumer_api_key" 
                        :required=required  classname="col-xs-6" 
                        :hint="lang('consumer_api_key')" id="consumer_api_key" 
                    />

                    <text-field 
                        :label="lang('consumer_api_secret')" :onChange="onChange" 
                        :value="consumer_api_secret" type="text" 
                        name="consumer_api_secret" :required=required  
                        classname="col-xs-6" :hint="lang('consumer_api_secret')" 
                        id="consumer_api_secret" 
                    />

                </div> <!--row-->

                <div class="row">

                    <text-field 
                        :label="lang('access_token')" :onChange="onChange" 
                        :value="access_token" type="text" 
                        name="access_token" :required=required  
                        classname="col-xs-6" :hint="lang('access_token')" 
                        id="access_token" 
                    />

                    <text-field 
                        :label="lang('access_token_secret')" :onChange="onChange" 
                        :value="access_token_secret" type="text" 
                        name="access_token_secret" :required=required  
                        classname="col-xs-6" :hint="lang('access_token_secret')" 
                        id="access_token_secret"
                    />

                </div> <!--row-->
					
                <div class="row">

                    <dynamic-select 
                        name="hashtag_text" classname="col-sm-6" 
                        :elements="elements_hashtag" :multiple="true"
                        :prePopulate="false" :label="lang('hashtags')" 
                        :value="hashtag_text" :onChange="onChange" 
                        :taggable="true" :searchable="true"
                        :disableNoOptionsMessage="true"
                        :hint="lang('hashtag_hint')"
                    />


                     <div class="form-group ">

                        <dynamic-select 
                            name="new_ticket_interval" classname="col-sm-6" 
                            :elements="elements_new_ticket" :multiple="false"
                            :prePopulate="false" :label="lang('time_check_twitter')" 
                            :value="new_ticket_interval" :onChange="onChange" 
                            :searchable="false" :clearable="false"
                        />

                    </div> <!--form-group-->

                </div>

                <div class="row">

                    <div class="form-group">

                        <dynamic-select 
                            name="cron_confirm" classname="col-sm-6" 
                            :elements="elements_cron" :multiple="false"
                            :prePopulate="false" :label="lang('cron_label_twitter')" 
                            :value="cron_confirm" :onChange="onChange" 
                            :searchable="false" :clearable="false"
                        />

                    </div> <!--form-group-->

                </div> <!--row-->

            </div> <!--box-body-->

            <div class="box-footer">

                <button type="button" @click = "onSubmit" class="btn btn-primary" >
                    <i class="fa fa-floppy-o"></i>
                    {{(!app_exists) ? lang('save') : lang('upd8')}}
                </button>

                <button class="btn btn-primary" @click="toggleModal" :disabled="!app_exists">
                    <i class="glyphicon glyphicon-repeat"></i>&nbsp;
                    {{ lang('reset') }}
                </button>

            </div> <!--box-footer-->

        </div> <!--box-->

        <transition name="modal">
            <!-- reset pop-up -->
                <modal v-if="showModal" :showModal="true" :onClose="()=> showModal = false" containerStyle="width: 500px">

                  <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
                  <div slot="title">
                    <h4>{{lang('reset')}}</h4>
                  </div>

                  <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
                  <div v-if="!isLoading" slot="fields">
                      <h5 id="H5">
                        {{lang('reset_confirm')}}
                      </h5>
                  </div>

                  <div v-if="isLoading" class="row" slot="fields" >
                    <loader :animation-duration="4000" color="#1d78ff" :size="size" :class="{spin: lang_locale == 'ar'}" />
                </div>

                  <div slot="controls">
                    <button type="button" @click="deleteTwitter" class="btn btn-primary">
                      <i class="glyphicon glyphicon-repeat" aria-hidden="true"></i>
                      &nbsp;{{lang('reset')}}
                    </button>
                  </div>

                </modal>

        </transition>


    </div>

</template>

<script>

import axios from 'axios';
import {errorHandler, successHandler} from 'helpers/responseHandler';
import { TwitterMixin } from './Mixins/TwitterMixin.js';

export default {
    
    mixins: [TwitterMixin],

    components:{
      "text-field": require("components/MiniComponent/FormField/TextField"),
      'alert' : require('components/MiniComponent/Alert'),
      "custom-loader": require("components/MiniComponent/Loader"),
      'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
      "data-table" : require('components/Extra/DataTable'),
      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
      "modal": require('components/Common/Modal'),
    },

    beforeMount() {

        this.hitApi();
        
    },

    methods: {

        hitApi() {
            this.getAppDetails();
            this.getCronDetails();
        },

        onChange(value, name){
            this[name]= value
        },

        deleteTwitter() {
        	//for reset
			this.isLoading = true
			this.isDisabled = true
			axios.delete(this.deleteUrl+'/'+this.appID).then(res=>{
				successHandler(res,"dataTableModal");
                this.isLoading = false;
                this.showModal = false
                this.loading = true;
                this.hitApi();
			}).catch(err => {
				errorHandler(err,"dataTableModal");
                this.showModal = false;
                this.isLoading = false;
			})
        },

        onSubmit() {
            let formDataObj = {
                consumer_api_key : this.consumer_api_key,
                consumer_api_secret : this.consumer_api_secret,
                access_token : this.access_token,
                access_token_secret : this.access_token_secret,
                hashtag_text : this.hashtag_text,
                cron_confirm  : this.cron_confirm.value,
				reply_interval : this.new_ticket_interval.value,

            }

            this.loading = true;

            let url,method;

            if(this.app_exists) {
                url = 'twitter/update/'+this.appID;
                method = 'PUT';
            } else {
                url = 'twitter/create';
                method = 'POST';
            }

            axios.request({
                method,
                url,
                data: formDataObj
            })
            .then(res=>{
                successHandler(res,'dataTableModal');
                this.hitApi()

            }).catch(err => {
                this.loading = false;
                errorHandler(err,'dataTableModal');
            });
        },

        prepareHashtags(hashtagsArray){
            let hashtagsForSelect = [];
            hashtagsArray.forEach((item,index) => {
                hashtagsForSelect.push({
                    id: item.hashtag,
                    name: item.hashtag
                });
            })
            return hashtagsForSelect;
        },

        getAppDetails() {
            this.loading = true;
            axios.get('twitter/app')
            .then((res) => {
                let data = res.data.data.apps[0];
                if(data) {
                    this.app_exists = true;
                    this.appID = data.id;
                    this.consumer_api_key = data.consumer_api_key;
                    this.consumer_api_secret = data.consumer_api_secret;
                    this.new_ticket_interval = this.filterValues(
                        this.elements_new_ticket,data.reply_interval
                    );
                    this.access_token = data.access_token;
                    this.access_token_secret = data.access_token_secret;
                    this.hashtag_text = this.prepareHashtags(data.hashtags);
                    this.loading = false;
                } else {
                    this.app_exists = false;
                    Object.assign(this.$data, this.$options.data.apply(this))
                    this.loading = false;
                }
            })
            .catch(err => {
                this.loading = false;
                errorHandler(err,'dataTableModal');
            })
            
            
        },

        filterValues(array,value) {
            return array.filter(x => {
                if(x.value == value)
                    return x;
            })[0];
        },

        getCronDetails() {
            axios.get('twitter/check/condition')
            .then((res) => {
                this.cron_confirm = this.filterValues(
                    this.elements_cron,res.data.data.active
                )
            })
        },

        toggleModal() {
            this.showModal = !(this.showModal);
        },

        onClose(){
            this.showModal = false;
        },
    }

}
</script>


<style>
.search {
    display: none !important;
}
#H5{
	margin-left:16px;
	/*margin-bottom:18px !important;*/
}
</style>