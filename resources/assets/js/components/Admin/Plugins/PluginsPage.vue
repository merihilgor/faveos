<template>
    <div>
        <alert componentName="dataTableModal"/>

        <div class="box box-primary" id="plugins-list">

            <div class="box-header with-border">
                
                <h2 class="box-title">
                    {{ lang('plugins-list') }}
                </h2>

                        <button type="button" class="btn btn-primary pull-right" @click="toggleModal">
                            <i class="glyphicon glyphicon-plus"></i>
                            &nbsp;
                            {{ lang('add_plugin') }}
                        </button>        

            </div> <!--header-->

            <div class="box-body">

                <data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="plugins-list"></data-table>    
                
            </div> <!--box-body-->

        </div> <!--box-->


        <transition name="modal">
            <!-- reset pop-up -->
                <modal v-if="showModal" :showModal="true" :onClose="onClose" containerStyle="width: 500px">

                  <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
                  <div slot="title">
                    <h4>{{lang('upload')}}</h4>
                  </div>

                  <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
                  <div v-if="!isLoading" slot="fields">
                      <div class="row container-fluid">
                         <div class="form-group col-sm-12">
                             <label for="">{{ lang('plugin') }}</label>

                             <input type="file" :name="pluginZip" @change="fileSelected" accept=".zip">
                         </div>
                      </div>
                  </div>

                  <div v-if="isLoading" class="row" slot="fields" >
                    <loader :animation-duration="4000" color="#1d78ff" :size="size" :class="{spin: lang_locale == 'ar'}" />
                </div>

                  <div slot="controls">
                    <button type="button" @click="uploadZip" class="btn btn-primary">
                      <i class="fa fa-upload" aria-hidden="true"></i>
                      &nbsp;{{lang('upload')}}
                    </button>
                  </div>

                </modal>

        </transition>

    </div>
</template>


<script>
import Vue from 'vue';
import axios from 'axios';
import {errorHandler,successHandler} from "helpers/responseHandler";
Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));
Vue.component('switch-holder', require('./SwitchHolder'));
export default {

    components : {

        "data-table" : require('components/Extra/DataTable'),
        "alert": require("components/MiniComponent/Alert"),
        "modal": require('components/Common/Modal'),
    },

    methods: {

        toggleModal() {
            this.showModal = !(this.showModal);
        },
        onClose(){
            this.showModal = false;
        },

        fileSelected(event) {
            if(event.target.files[0].size < 10000000)
                this.pluginZip = event.target.files[0];
             else{
                 this.showModal = false;
                 this.setAlert('Maximum File upload size is 10MB.'); 
             }
                
        },

        setAlert(msg) {
          errorHandler({
              response: {
                  status: 400,
                  data: {
                      message: msg
                  }
              }
          },'dataTableModal');
        },

        onChange(value,name){
			this[name] = value;
        },
        
        uploadZip() {
            let fd = new FormData();
            fd.append('plugin', this.pluginZip);
            let configurations = { headers: { 'Content-Type': 'multipart/form-data' } }
            axios.post('post-plugin',fd,configurations)
            .then((res) => {
                this.showModal = false;
                successHandler(res,'dataTableModal');
                setTimeout(()=>window.eventHub.$emit('refreshData'),10);
            })
            .catch((err) => {
                if(err.response.status == 412) {
                    this.showModal = false;
                    this.setAlert(err.response.data.message.plugin)
                }
                this.showModal = false;
                errorHandler(err,'dataTableModal');
            })
        }

    },

    data() {
        return {
            showModal:false,
            pluginZip: '',
            isLoading:false,
            apiUrl:'/getplugin',
            columns: ['activate','name', 'description', 'website', 'author','version','action'],
            options: {
                headings: {activate: 'Status', name: 'Name', description : 'Description', website : 'Website', version:'Version',action:'Action',author:'Author'},

                columnsClasses : {

					name: 'name', 

					description: 'description', 

					website: 'website',

                    author: 'author',
                    
                    version: 'version',

                    action : 'action',

                    activate: 'activate'

                },
                
                perPage:25,

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
                },
                
                texts: { filter: '', limit: '' },

                sortable:  ['name','version'],
				
				filterable:  ['name'],
				
                pagination:{chunk:5,nav: 'fixed',edge:true},

                templates : {
                    action : 'table-actions',
                    activate: 'switch-holder'
                },
                
                requestAdapter(data) {
                    return {
                        
                        'sort_field' : data.orderBy ? data.orderBy : 'status',
                        
                        'sort_order' : data.ascending ? 'desc' : 'asc',
                        
                        'search_query' : data.query.trim(),
                        
                        page : data.page,

                        limit : data.limit,
                        
                    }
                },

                responseAdapter({data}) {
					return {
                        
						data: data.data.map(data => {
                        if(data.status == 1 && data.settings)
                            data.settings_url = this.basePath() + '/' +data.settings ;
                        data.delete_url = this.basePath() + '/plugin/delete/' +data.name;
						return data;
                    }),
						count: data.total
					}
				},
            },
        }
    }

}
</script>

<style>

.name {
    width: 13% !important;
    overflow-wrap: break-word;
    word-wrap: break-word;
    hyphens: auto;
}

.description{
    width: 40% !important;
    overflow-wrap: break-word;
    word-wrap: break-word;
    hyphens: auto;
}

.website {
     width: 17% !important;
    overflow-wrap: break-word;
    word-wrap: break-word;
    hyphens: auto;
}

.version {
     width: 10% !important;
    overflow-wrap: break-word;
    word-wrap: break-word;
    hyphens: auto;
}

.action {
     width: 10% !important;
    overflow-wrap: break-word;
    word-wrap: break-word;
    hyphens: auto;
}

.author {
    width: 10% !important;
    overflow-wrap: break-word;
    word-wrap: break-word;
    hyphens: auto;
}

#H5{
	margin-left:16px;
	/*margin-bottom:18px !important;*/
}

</style>