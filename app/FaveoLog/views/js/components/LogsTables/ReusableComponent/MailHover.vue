<template>

    <div id="main" v-on:mouseenter="popOver(data.id)" v-on:mouseleave="popOver(null)">

        <a v-for="element in formattedData[objectKey]" id="action_mail" href="javascript:;" @click="getData(element, formattedData.id)" v-popover:right>{{element}}&nbsp;&nbsp;

            <popover id="action_mail_popover" v-if="popId == data.id" event="click" name="right" style="top: !42px;left:!927px;width:289px">

                <div v-if="showAlert === true && loading === false"><p class="text-muted">{{err_message}}</p></div>

                <div v-if="loading === true">

                    <loader :animation-duration="4000" color="#1d78ff" :size="30"/>
                </div>

                <div v-if="hasDataPopulated === true && loading === false && showAlert === false" class="box-widget widget-user-2 cus-container">

                    <div class="widget-user-header bg-blue custom-head">

                        <div class="widget-user-image">

                            <faveo-image-element id="user_img" :source-url="user_data.profile_pic" :classes="['img-circle','img-responsive']" alternative-text="User Image" :img-width="40" :img-height="40"/>

                            <a :href="user_data.profile_link" target="_blank"><i class="fa fa-eye pull-right cus-icon"></i></a>
                        </div>

                        <h3 class="widget-user-username custom-name" :title="user_data.name">{{subString(user_data.name)}}</h3>
                    </div>

                    <div class="box-footer no-padding">

                        <ul class="nav nav-stacked">

                            <li id="b_bottom">

                                <a href="#" id="cus-link">{{lang('email')}}

                                    <span class="pull-right">{{subString(user_data.email)}}</span>

                                </a>
                            </li>
                            <li>

                                <a href="#" id="cus-link">{{lang('Role')}}

                                    <span class="pull-right">{{subString(user_data.role)}}</span>

                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </popover>
        </a>
    </div>
</template>

<script>

    import axios from 'axios';

    import { getSubStringValue } from 'helpers/extraLogics';

    import Vue      from 'vue'

    import Popover from 'vue-js-popover'

    Vue.use(Popover)

    import { mapGetters } from 'vuex'

    export default {

        name:"mail-hover",

        description: "Mail hover component",

        props: {

            data : { type : Object, required : true },

            /**
             * Key in the data prop which has to be made hoverable
             */
            objectKey: {type: String, required: true}
        },

        data(){

            return {

                popId : '',

                user_data : {},

                base : '',

                loading : true,

                hasDataPopulated : false,

                showAlert : false,

                err_message : ''
            }
        },

        computed:{
            ...mapGetters(['getUserData']),

            formattedData(){
                if(!Array.isArray(this.data[this.objectKey])) {
                    // cloning the prop to avoid prop mutation
                    let tempDataHolder = Object.assign({}, this.data);
                    tempDataHolder[this.objectKey] = [this.data[this.objectKey]];
                    return tempDataHolder;
                }

                return this.data;
            }
        },

        watch : {
            popId(newValue,oldValue){
                return newValue
            },

            getUserData(newValue,oldValue){
                this.base = newValue.system_url
                return newValue
            }
        },

        beforeMount(){
            if(this.getUserData.system_url){
                this.base = this.getUserData.system_url
            }
        },

        methods:{

            popOver(x) {
                this.popId = x;
            },

            getData(mail,id){

                this.user_data = ''

                this.showAlert = false

                this.loading = true,

                    this.popId = id

                axios.get('/api/get-user-by-email?email='+mail).then(res=>{

                    this.showAlert = false

                    this.user_data = res.data.data.user;

                    this.loading = false

                    this.hasDataPopulated = true

                }).catch(err=>{

                    this.loading = false

                    this.showAlert = true

                    this.err_message = err.response.data.message
                })
            },

            subString(name){
                return getSubStringValue(name,30)
            }
        },

        components:{

            'loader':require('components/Client/Pages/ReusableComponents/Loader'),
            'faveo-image-element': require('components/Common/FaveoImageElement')
        }
    };
</script>

<style type="text/css">
    .user-img{
        margin: auto;
        border: 1px solid #d2d6de;
        border-radius: 50px;
        width: 40px !important;
        height: 40px !important;
    }
    .custom-head {
        padding: 7px !important;
        min-height: 12vh !important;
    }
    .custom-name {
        margin-top: 11px !important;
        margin-left: 45px !important;
        font-size: 15px !important;
    }
    .cus-container {
        margin-bottom: 20px;
    }
    .cus-icon { color : white !important; }

    #cus-link { padding : 15px 15px 0px 0px !important;}
    #b_bottom { border-bottom: 0px !important }
</style>