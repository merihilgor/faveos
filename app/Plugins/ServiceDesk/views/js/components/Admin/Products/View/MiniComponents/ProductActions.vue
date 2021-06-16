<template>

  <div class="pull-right">

    <div class="btn-group">

      <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">

        <i class="fa fa-link"> </i> {{lang('vendor')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu">

        <template>
          
          <li><a href="javascript:;" @click="showVendorCreate = true"> {{lang('new_vendor')}}</a></li>

          <li><a href="javascript:;" @click="showVendorExists = true"> {{lang('existing_vendor')}}</a></li>
        </template>
      </ul>
    </div>

    <button class="btn btn-sm btn-default" @click="showAttach = true">
      
      <i class="fa fa-server"> </i> {{lang('attach_asset')}}
    </button>

    <div class="btn-group">

      <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">

        <i class="fa fa-cog"> </i> {{lang('more')}} <span class="caret"></span>
      </button>

      <ul class="dropdown-menu" id="more_actions">

        <li>

          <a :href="basePath()+'/service-desk/products/'+product.id+'/edit'">
            {{lang('edit')}}
          </a>
        </li>

        <li>

          <a href="javascript:;" @click="showDeleteModal = true">{{lang('delete')}}</a>
        </li>
      </ul>
    </div>

    <transition name="modal">

		 <delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
        alertComponentName="product-view" :deleteUrl="'/service-desk/api/product-delete/' + product.id" 
        redirectUrl="/service-desk/products">

     </delete-modal>
		</transition>

    <transition name="modal">

      <vendor-create-modal v-if="showVendorCreate" :onClose="onClose" :showModal="showVendorCreate"
        :productId="product.id">

      </vendor-create-modal>
    </transition>

    <transition name="modal">

      <vendor-add-modal v-if="showVendorExists" :onClose="onClose" :showModal="showVendorExists" 
        :productId="product.id">

      </vendor-add-modal>
    </transition>

    <transition name="modal">

      <product-assets  v-if="showAttach" :onClose="onClose" :showModal="showAttach" :productId="product.id">

      </product-assets>
    </transition>
  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import axios from 'axios'

  export default {

    name : 'product-actions',

    description : 'Product actions component',

    props : {

      product : { type : Object, default : ()=> {}}
    },

    data() {

      return {

        showDeleteModal : false,

        showVendorCreate : false,

        showVendorExists : false,

        showAttach : false
      }
    },

    methods : {

      subString(value,length = 15){

        return getSubStringValue(value,length)
      },

      onClose(){

        this.showDeleteModal = false;

        this.showVendorCreate = false;
		    
        this.showVendorExists = false;
        
        this.showAttach = false;

		    this.$store.dispatch('unsetValidationError');
		  },
    },

    components : {

      'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),

      'vendor-add-modal': require('./VendorAddModal'),
  
      'vendor-create-modal': require('./VendorCreateModal'),

      'product-assets' : require('./ProductAssets'),
    }
  };
</script>

<style scoped>

  #more_actions {
    right: 0 !important;
    left: unset !important;
  }
</style>