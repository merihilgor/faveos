<template>

  <div id="product-associates">

    <alert componentName="ProductAssociates"/>

    <div v-if="!loading" class="nav-tabs-custom">

      <ul class="nav nav-tabs">

        <li v-for="section in tabs" :class="{ active: category === section.id }">

          <a id="product_tab" data-toggle="tab" @click="associates(section.id)">

            {{lang(section.title)}}
          </a>
        </li>
      </ul>

      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <div v-if="!tabLoading">

            <product-associates-table :category="category" :componentTitle="compTitle" :productId="productId" 
              :apiUrl="apiUrl">

            </product-associates-table>
          </div>

          <div v-if="tabLoading" class="row">

			      <loader :animation-duration="4000" :size="60"/>
			    </div>
        </div>
      </div>
    </div>

    <div v-if="loading" class="row">

      <loader :animation-duration="4000" :size="60"/>
    </div>
  </div>
</template>

<script>

  export default {

    name : 'product-associates',

    description : 'Product associates page',

    props : {

      productId : { type : String | Number, default : '' },

      product : { type : Object, default : ()=>{} },
    },

    data(){

      return {

        tabs:[

          {id : 'assets', title : 'associated_assets'},
        
          {id : 'vendors', title : 'associated_vendors'},
        ],

        category : 'assets',

        loading : false,

        tabLoading : false,
      }
    },

    computed : {

    	apiUrl() {

    		return this.category === 'assets' ? '/service-desk/api/asset-list?product_ids='+ this.productId : 'service-desk/api/product/vendor/' + this.productId;
    	},

    	compTitle() {

    		return this.category === 'assets' ? 'ProductAssets' : 'ProductVendors'
    	}
    },
    
    methods : {

      associates(category){

      	this.tabLoading = true;

				this.category = category;

				setTimeout(()=>{

					this.tabLoading = false;
				},1)
			}
    },

    components : {

      'product-associates-table' : require('./MiniComponents/ProductAssociatesTable'),

      'alert' : require('components/MiniComponent/Alert'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    }
  };
</script>

<style scoped>
  
  #product_tab{
    cursor: pointer;
    margin-bottom: -1px;
  }
</style>