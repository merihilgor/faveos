<template>
	
	<div>

		<alert componentName="vendor-view"/>

		<div class="box box-primary pad_0">
			
			<div class="row" v-if="!hasDataPopulated || loading">

				<div class="col-md-12 m_30">

					<loader :animation-duration="4000" :size="60"/>	
				</div>
			</div>

			<template v-if="hasDataPopulated && vendorData">

				<div class="box-header with-border">
				
					<div class="row ml_20">
						
						<div class="col-md-8">
						
							<h3 class="box-title" :title="vendorData.name">

								<i class="fa fa-barcode" aria-hidden="true"></i> {{subString(vendorData.name,50)}}
							</h3>
						</div>
						
						<div class="col-md-4">
						
							<div class="dropdown pull-right">
						
								<a class="btn btn-default btn-sm" :href="basePath()+'/service-desk/vendor/'+vendorData.id+'/edit'" 
									title="Edit" >
									
									<span class="fa fa-edit"></span> {{lang('edit')}}
								</a>

								<a class="btn btn-default btn-sm" @click="modalMethod('delete')" href="javascript:;" title="Delete" >

									<span class="fa fa-trash"></span> {{lang('delete')}}
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="box-body">
					
					<div class="row">

		        <div class="col-md-12">

		          <div class="callout callout-info">

		            <div class="row">

		              <div class="col-md-4" :title="formattedTime(vendorData.created_at)">

		                <b>{{lang('created_date')}} : </b> {{formattedTime(vendorData.created_at)}}
		              </div>

		              <div class="col-md-4" :title="formattedTime(vendorData.updated_at)">

		                <b>{{lang('updated_date')}} : </b> {{formattedTime(vendorData.updated_at)}}
		              </div>

		              <div class="col-md-4" :title="vendorData.status.name">

		                <b>{{lang('status')}} : </b> {{subString(vendorData.status.name)}}
		              </div>
		            </div>
		          </div>
		        </div>
		      </div>

		      <div class="row">

		        <div class="col-md-12">

		          <div class="col-md-6 info-row">

		            <div class="col-md-6"><label>{{ lang('email') }}</label></div>

		            <div class="col-md-6">

		              <a :title="vendorData.email" href="javascript:void(0)">{{subString(vendorData.email)}}</a>
		            </div>

		          </div>

		          <div class="col-md-6 info-row">

		            <div class="col-md-6"><label>{{ lang('primary_contact') }}</label></div>

		            <div class="col-md-6">

		              <a :title="vendorData.primary_contact" href="javascript:void(0)">{{subString(vendorData.primary_contact)}}</a>
		            </div>

		          </div>

		          <div class="col-md-6 info-row">

		            <div class="col-md-6"><label>{{ lang('address') }}</label></div>

		            <div class="col-md-6">

		              <a :title="vendorData.address"> {{subString(vendorData.address)}}</a>
		            </div>

		          </div>

		          <div class="col-md-6 info-row">

		            <div class="col-md-6"><label>{{lang('description')}} </label></div>

		            <div class="col-md-6">

		              <button type="button" class="btn btn-info btn-xs" @click="modalMethod('vendor_description')"> 

										<i class="fa fa-file-text"> </i> {{lang('show_description')}}

									</button>
		            </div>
		          </div>
		        </div>
		      </div>
				</div>
			</template>
		</div>

		<div v-if="hasDataPopulated && vendorData" class="nav-tabs-custom">

		  <ul class="nav nav-tabs">

				<li v-for="section in tabs" :class="{ active: category === section.id }">

					<a id="vendor_tab" data-toggle="tab" @click="associates(section.id)">

						<strong>{{lang(section.title)}}</strong>
					</a>
				</li>
			</ul>

			<div class="tab-content">

			  <div class="active tab-pane" id="activity">

			  	<div>

			  		<vendor-associates-table v-if="!tabloading" :category="category" :vendorId="vendorData.id">

						</vendor-associates-table>
            
            <template v-if="tabloading">

            	<div class="row">

            		<loader :animation-duration="4000" :size="60"/>

            	</div>
            </template>
          </div>
        </div>
      </div>
    </div>

		<transition name="modal">

			<vendor-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :data="vendorData" :title="title">
				
			</vendor-modal>
		</transition>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import  { getIdFromUrl } from 'helpers/extraLogics';

	import { mapGetters } from 'vuex';

	import { getSubStringValue } from 'helpers/extraLogics';

	export default {

		name : 'vendor-view',

		description : 'Vendor details page component',

		data(){

			return {

				hasDataPopulated : false,

				loading : false,

				vendorData : '',

				showModal :  false,

				title : '',

				tabs:[
          
          {id : 'contracts', title : 'associated_contracts'},

          {id : 'products', title : 'associated_products'}
        ],

        category : 'contracts',

        tabloading : false
			}
		},

		computed : {

			...mapGetters(['formattedDate','formattedTime'])
		},

		beforeMount(){

			const path = window.location.pathname

			this.getInitialValues(path);
		},

		methods : {

			associates(category){
				
				this.tabloading = true;

				this.category = category

				setTimeout(()=>{
	
					this.tabloading = false;
				},2000)
			},

			subString(value,length = 20){

				return getSubStringValue(value,length)
			},

			modalMethod(title){

				this.title = title;

				this.showModal = true;
			},

			getInitialValues(path){

				const id = getIdFromUrl(path)

				this.loading = true
				
				axios.get('/service-desk/api/vendor/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.vendorData = res.data.data.vendor;

				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'vendor-view');

					this.redirect('/service-desk/vendor')
				});
			},

			onClose(){

				this.showModal = false;
			},	
		},

		components : {

			'vendor-modal': require('./MiniComponents/VendorModal'),

			'vendor-associates-table': require('./MiniComponents/VendorAssociates'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		}
	};
</script>

<style scoped>
	
	.info-row{
    border-top: 1px solid #f4f4f4; padding: 10px;
  }
  .ml_20{
  	margin-left: -20px;
  }
	.pad_0{
		padding: 0px !important;
	}
	#border{
		border: 1px solid #e7e6e6 !important;
	}

	#vendor_tab{
    cursor: pointer;
   	margin-bottom: -1px;
  }

  .m_30{
  	margin-top: 30px;
  	margin-bottom: 30px;
  }
</style>