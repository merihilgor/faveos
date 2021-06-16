<template>
    <div>
        <custom-loader v-if="loading" :duration="loadingSpeed" />
        <alert componentName="barcode_settings" />

        <div class="box box-primary">

            <div class="box-header with-border">

                <h2 class="box-title">
                    {{ lang('barcode_settings') }}
                </h2>

                

            </div> <!--box-header-->


            <div class="box-body">

                <div class="row">

                    <text-field 
                        :label="lang('label_width')" :onChange="onChange" 
                        :value="width" type="text" name="width" 
                        classname="col-xs-3" 
                        id="label_width" 
                        :required="true"
                    />

                    <text-field 
                        :label="lang('label_height')" :onChange="onChange" 
                        :value="height" type="text" 
                        name="height" :required="true"  
                        classname="col-xs-3" 
                        id="label_height" 
                        
                    />


                    <text-field 
                        :label="lang('space_between_labels')" :onChange="onChange" 
                        :value="space_between_labels" type="text" 
                        name="space_between_labels" :required="true"  
                        classname="col-xs-3" 
                        id="space_between_labels" 
                        
                    />


                    <text-field 
                        :label="lang('labels_per_row')" :onChange="onChange" 
                        :value="labels_per_row" type="text" 
                        name="labels_per_row" :required="true"  
                        classname="col-xs-3"
                        id="labels_per_row" 
                    />

                </div>    

                <div class="row">

                    <radio
                        name="display_logo_confirmed"
                        :value="display_logo_confirmed"
                        :label="lang('display_logo_confirm')"
                        :options="radioOptionsLogo"
                        :required="true"
                        classname="form-group col-sm-3"
                        :onChange="onChange"
                    />

                    <file 
                        name="logo_image"
                        :value="logo_image"
                        :label="lang('logo_image')"
                        :onChange="onChange"
                        id="logo_image"
                        classname="col-sm-6"
                        :key="componentRenderKey"
                        accept="image/x-png,image/jpeg"
                    />
                </div> <!--row-->
					
                <div class="row">

                   

                    
                </div>

                
            </div> <!--box-body-->

            <div class="box-footer">

                <button type="button" @click = "onSubmit" class="btn btn-primary" >
                    <i class="fa fa-floppy-o"></i>
                    {{lang('save')}}
                </button>

                <button class="btn btn-primary" @click="toggleModal" id="resetBtn" :disabled="isDisabled">
                    <i class="glyphicon glyphicon-repeat"></i>&nbsp;
                    {{ lang('reset') }}
                </button>

            </div> <!--box-footer-->

        </div> <!--box-->

        <transition name="modal">
            <!-- reset pop-up -->
                <modal v-if="showModal" :showModal="true" :onClose="()=> showModal = false" :containerStyle="containerStyle">

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
                    <button type="button" @click="deleteTemplate" class="btn btn-primary">
                      <i class="glyphicon glyphicon-repeat" aria-hidden="true"></i>
                      &nbsp;{{lang('reset')}}
                    </button>
                  </div>

                </modal>

        </transition>

    </div>
</template>

<script>
import axios from "axios";
import {errorHandler, successHandler} from 'helpers/responseHandler';
import { mapGetters } from 'vuex'
export default {

    components:{

      "text-field": require("components/MiniComponent/FormField/TextField"),
      "radio" :require("components/MiniComponent/FormField/RadioButton"),
      "file" :require("components/MiniComponent/FormField/fileUpload"),
      'alert' : require('components/MiniComponent/Alert'),
      "custom-loader": require("components/MiniComponent/Loader"),
      "modal": require('components/Common/Modal'),
      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
     
    },


    beforeMount() {

        this.getExistingCustomTemplateDetails();

    },

    data() {
        return {
            labelType: 1,
            display_logo_confirmed: 0,
            radioOptionsLogo:[{name:'Yes',value:1},{name:'No',value:0}],
            width : '',
            height: '',
            logo_image: null,
            labels_per_row: '',
            space_between_labels : '',
            showModal: false,
            loading:false,
            loadingSpeed: 4000,
            logo_text: '',
            barcode_mode:'system_default',
            template_exists: false,
            image_exists: false,
            isDisabled: true,
            templateId: '',
            componentRenderKey: Math.random(),
            isLoading: false,
            containerStyle: {
                width:" 500px",
            }
        }
    },

    watch: {
        
    },

    methods: {
        onChange(value, name) {
            
            this[name] = value;

            if(value === null){

                this[name] = '';
            }
        },

        deleteTemplate() {
        	//for reset
			this.isLoading = true
			this.isDisabled = true
			axios.delete('service-desk/barcode/template/delete/' + this.templateId).then(res=>{
				successHandler(res,"barcode_settings");
                Object.assign(this.$data, this.$options.data.apply(this))
                this.getExistingCustomTemplateDetails();
			}).catch(err => {
				errorHandler(err,"barcode_settings");
			}).finally(() => {
                this.showModal = false;
                this.isLoading = false;
                this.loading = false;
            })
        },

        toggleModal() {
            this.showModal = !(this.showModal);
        },

        onClose(){
            this.showModal = false;
        },

        onSubmit() {

            let formDataObj = new FormData();
            this.image_exists = (this.logo_image) ? true : false;
            if(this.logo_image instanceof File)
                formDataObj.append('logo_image',this.logo_image);
            formDataObj.append('image_exists',this.image_exists);    
            formDataObj.append('width',this.width);
            formDataObj.append('height',this.height);
            formDataObj.append('labels_per_row',this.labels_per_row);
            formDataObj.append('space_between_labels',this.space_between_labels);
            formDataObj.append('display_logo_confirmed',this.display_logo_confirmed);


            let configuration = { headers: { 'Content-Type': 'multipart/form-data' } }

            this.loading = true;

            let url =(this.template_exists) 
            ? 'service-desk/barcode/template/update/'+this.templateId
            : 'service-desk/barcode/template/create';

            axios.post(url,formDataObj,{ headers: { 'Content-Type': 'multipart/form-data' } })
            .then((res) => {
                successHandler(res,'barcode_settings');
                this.loading = false;
                this.$store.dispatch('unsetValidationError');
                Object.assign(this.$data, this.$options.data.apply(this))
                this.getExistingCustomTemplateDetails();
            })  
            .catch((err) => {
                errorHandler(err,'barcode_settings')
                this.loading = false;
            })          
        },


        updateStatesWithData(data){
				const self = this;
				const stateData = this.$data;
				
				Object.keys(data).map(key => {
					
					if (stateData.hasOwnProperty(key)) {
					
						self[key] = data[key];
					}
				});
			},

        getExistingCustomTemplateDetails() {
            axios.get('service-desk/barcode-template')
            .then((res) => {
                this.template_exists = true;
                let data = res.data.data;
                this.updateStatesWithData(data);
                this.isDisabled = false;
                this.templateId = data.id;

            })
            .catch((err) => {
                this.template_exists = false;
                this.isDisabled = true;
            })
        }
    }
}
</script>

<style>
#H5{
	margin-left:16px;
}
</style>