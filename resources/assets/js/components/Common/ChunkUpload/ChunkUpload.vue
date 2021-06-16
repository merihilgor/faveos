<template>
  
  <div class="media-lib">

    <button type="button" class="btn btn-default media-btn" data-toggle="modal" data-target="#myModal" 
      @click="getGallery()">

      <i class="fa fa-caret-square-o-right"></i>&nbsp;{{lang('add-media')}}&nbsp;

    </button>

    <slot name="templateBtn"></slot>

    <div id="myModal" class="modal fade" role="dialog">

      <div class="modal-dialog" >

        <div class="modal-content">

          <div class="modal-header">

            <alert componentName="MediaLibrary" />
            
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            
            <h4 class="modal-title">{{lang('insert-media')}}</h4>
          
          </div>

          <div class="modal-body">
            
            <div class="row">
              
              <div class="col-sm-12 media-tab">
                      
                <ul class="nav nav-tabs" id="mytabs">
                  
                  <li>

                    <a data-toggle="tab" href="#upload" @click="noAddOption()">{{lang('upload-files')}}</a>
                  </li>
                        
                  <li class="active">

                    <a data-toggle="tab" href="#gallery">{{lang('media-library')}}</a>
                  </li>
                </ul>

                <div class="tab-content active">
                  
                  <!-- Upload Tab -->
                  <upload-tab :apiUrl="apiUrl" :target_url="target_url"></upload-tab>
                        
                  <!-- Gallery Tab -->
                  <div v-if="simpleLoader" class="col-sm-12 col-xs-12" id="loader">

                    <loader :animation-duration="4000" :size="40"/>
                  </div>
                          
                  <gallery-tab v-if="showGallery" :allgallery="gallery" :showloader="loader"  :commonObj="mediaObj" v-on:disable="checkDisable" v-on:getSize="setImageSize" :page="page" 
                    :apiUrl="apiUrl">
                      
                  </gallery-tab>
                      
                </div>
                
              </div>
                  
            </div>
            
          </div>

          <div class="modal-footer">
        
            <button v-if="page != 'canned' && page!= 'reply' && page != 'ticket'  && page != 'internal_note'" type="button" 
              class="btn btn-primary btn-md" :disabled="inlineBtnDisable" data-dismiss="modal" @click="addImgeToEditor()">{{lang('inline')}}</button>
                  
            <button v-if="page != 'kb'" type="button" class="btn btn-primary btn-md" :disabled="disableBtn"  
              @click="addAttachment()" data-dismiss="modal">{{lang('attach')}}</button>
              
            <button type="button" class="btn btn-danger btn-md" :disabled="disableBtn" @click="showModal = true">

              {{lang('delete')}}
            </button>
          </div>
        </div>
          
        <delete-popup v-if="showModal" :showModal="showModal" :path="inlineImage.pathname" :onClose="onClose"></delete-popup>
      </div>
    </div>
  </div>
</template>

<script>
  
  import axios from 'axios'

  export default{

    props : {

      inlineFiles : { type : Object | Array },

      attachmentFiles : { type : Object | Array },

      apiUrl : { type : String, default : '/media/files'},

      target_url : { type : String, default : '/chunk/upload'},

      page : { type : String | Number },

      onInlinePdf : { type : Function }

    },
          
    data(){
      
      return{
        
        gallery:[],
                 
        loader:true,
                 
        mediaObj:{},
                 
        inlineBtnDisable:true,
                 
        disableBtn:true,
        
        inlineImage:{},        

        allMedia:[],
        
        simpleLoader:true,
                 
        showGallery:false,

        showModal : false
      }
    
    },
    
    components: {
            
      'upload-tab':require('./UploadTab.vue'),
            
      'gallery-tab':require('./GalleryTab.vue'),
            
      'delete-popup':require('./DeletePopup.vue'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),

      "alert": require("components/MiniComponent/Alert"),
    },
        
    methods:{
      
      getGallery(){
        
        this.simpleLoader=true;
        
        this.gallery=[];
        
        axios.get(this.apiUrl).then(res=>{
          
          this.simpleLoader=false;
          
          this.gallery=res.data.data;
                       
          this.mediaObj=res.data;
          
          this.loader=false;
          
          this.showGallery=true;
        
        }).catch(error=>{
                       
          this.simpleLoader=false;
          
          this.loader=false;
          
          this.showGallery=true;
        
        })
      
      },
      
      //add imgae to editor
      addImgeToEditor(){
        
        if(this.inlineImage.extension === 'pdf'){
         
          var link = this.inlineImage.base_64.replace(/\s/g,'%20');

          var value = "<a  target='_blank' href=" + link + ">"+this.inlineImage.filename +"</a>";

          this.onInlinePdf(value);
        }
      },
            
      //add attachment
      addAttachment(){
        
        this.allMedia.push(this.inlineImage);
                   
        this.$emit('getAttach',this.inlineImage);
      
      },
            
      //set inline image size
      
      setImageSize(payload){
        
        this.inlineImage=payload;
      
      },
      
      // remove add options in bottom
      noAddOption(){
        
        this.inlineBtnDisable=true;
        
        this.disableBtn=true;
      
      },
      
      //disable button
      checkDisable(x,y){
        
        if(x=="disable"){
          
          this.noAddOption();
        
        }else{
                  
          this.disableBtn=false;
                  
          if (y == "pdf") {
            
            this.inlineBtnDisable=false;
          
          }else {
            
            this.inlineBtnDisable=true;     
          
          }

        }
                 
      },

      onClose(){

        this.showGallery=false;
      
        this.showModal = false;

        this.getGallery();
      }
    }
  };
</script>

<style scoped>
  .modal-dialog{
    width: 1000px !important;
    overflow: hidden !important;
  }
  .media-lib{
      margin-bottom: 9px;
  }
  #loader{
    margin-top: 20px;
    margin-bottom: 20px;
  }
  #link{
    cursor: pointer;
  }
</style>
  