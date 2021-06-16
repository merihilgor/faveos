<template>

  <div class="box box-primary pad_0">

    <div class="box-header with-border">

      <h3 class="box-title text-capitalize ml_5" :title="product.name"> {{ subString(product.name,50) }}</h3>

      <product-actions :product="product"></product-actions>
    </div>

    <div class="box-body">

      <div class="row">

        <div class="col-md-12">

          <div class="callout callout-info">

            <div class="row">

              <div class="col-md-4" :title="formattedTime(product.created_at)">

                <b>{{lang('created_date')}} : </b> {{formattedTime(product.created_at)}}
              </div>

              <div class="col-md-4" :title="product.status ? 'Enable' : 'Disable'">

                <b>{{lang('status')}} : </b> {{product.status ? lang('enable') : lang('disable')}}
              </div>

              <div class="col-md-4" :title="product.manufacturer">

                <b>{{lang('manufacturer')}} : </b> {{subString(product.manufacturer)}}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-md-12">

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('product_status') }}</label></div>

            <div class="col-md-6">

              <a :title="product.product_status.name" href="javascript:void(0)">{{subString(product.product_status.name)}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('department_access') }}</label></div>

            <div class="col-md-6">

              <a :title="product.department.name" href="javascript:void(0)"> {{subString(product.department.name)}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('product_mode_procurement') }}</label></div>

            <div class="col-md-6">

              <a :title="product.procurement.name" href="javascript:void(0)">{{subString(product.procurement.name)}}</a>
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

            <div class="col-md-6"><label>{{ lang('asset_type') }}</label></div>

            <div class="col-md-6">

              <a v-if="product.asset_type" :title="product.asset_type.name" href="javascript:void(0)">{{subString(product.asset_type.name)}}</a>
              <p v-else><strong>--</strong></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <transition name="modal">

      <product-description  v-if="showDescription" :onClose="onClose" :showModal="showDescription" :description="product.description">

      </product-description>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios';

	import { mapGetters } from 'vuex'

  export default {

    name : 'product-details',

    description : 'Product details page',

    props : {

      product : { type : Object, default : ()=>{} },
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
		  }
    },
    
    components : {
    
      'product-actions' : require('./MiniComponents/ProductActions'),
    
      'product-description' : require('./MiniComponents/ProductDescription')
    }
  };
</script>

<style scoped>
  .info-row{
    border-top: 1px solid #f4f4f4; padding: 10px;
  }
  .pad_0{
    padding: 0px !important;
  }
  .ml_5{
    margin-left: -5px;
  }
</style>