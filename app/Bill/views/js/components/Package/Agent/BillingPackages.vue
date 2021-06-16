<template>

	<div id="billing-packages">

		<div class="box box-primary">
		
			<div class="box-header with-border">
				
				<div class="row">
				
					<div class="col-md-4">
				
						<h3  id="packages_title" class="box-title text-capitalize">{{ lang('packages') }}</h3>
					</div>
					
					<div class="col-md-8">
						
						<div v-if="category === 'invoice'" class="pull-right">
							
							<button @click="addInvoice()" class="btn btn-primary">

								<i class="fa fa-plus"> </i> {{ lang('create_invoice') }}
							</button>
							
							<transition name="modal">
                
                <order-modal v-if="showModal" :showModal="showModal" :onClose="onClose" :userId='user_id' 
                	:title="lang('create_invoice')">
                		
                </order-modal>
            	</transition>
						</div>
					</div>
				</div>
			</div>
		
			<div class="box-body">
				
				<div class="nav-tabs-custom">
				<!-- nav header -->
					<ul class="nav nav-tabs">
						
						<li v-for="section in tabs" v-bind:class="{ active: category === section.category }">
							
							<a id="package_tab"  data-toggle="tab" @click="packages(section.category)">{{lang(section.title)}} 
								
								<span id="badge" :class="section.b_class">{{section.count}}</span>
							</a>
						</li>
					</ul>

					<div class="tab-content" id="package_content">
					
						<div class="active tab-pane" id="activity">
							
							<component v-bind:is="currentTableComponent" :id="user_id" :category="category" :apiEndpoint="apiUrl">
					        
					    </component>
					
						</div>			
					</div>
				</div>	
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { lang } from 'helpers/extraLogics';

	import {getIdFromUrl} from 'helpers/extraLogics';

	export default {

		name : 'billing-packages',

		description : 'Billing packages datatable',

		props : {

			id : { type : String | Number, default :''},
		},

		data(){

			return {

				category:'orders',

				tabs:[],
				
				user_id : '',
				
				loading:true,

				apiUrl : '',
				
				showModal : false
			}
		},

		watch : {

			category(newValue,oldValue){
				return newValue
			},
		},

		computed : {
			currentTableComponent(){
		    	return this.category === 'orders' ? 'orders-table' : 'invoices-table';
      		},
		},	

		beforeMount(){

			const path = window.location.pathname;

			this.user_id = getIdFromUrl(path);

			this.getCount();

			this.getTableData(this.category);
		},

		methods :{

			getTableData(category){

				this.apiUrl = category === 'orders' ? '/bill/package/get-user-packages?user_id='+this.user_id : '/bill/package/get-user-invoice?users[0]='+this.user_id
			},

			packages(category){
				this.category = category
				this.getCount();
				this.getTableData(category);
			},

			getCount(){

				axios.get('/bill/package/get-all-count/'+this.user_id).then(res=>{
					this.tabs = [
						{category:'orders',title:'orders',b_class:'badge bg-orange',count:res.data.data.userpackage},
						{category:'invoice',title:'invoice',b_class:'badge bg-red',count:res.data.data.invoice},
					]
				}).catch(err=>{ this.tabs = [] })
			},
			
			onClose(){
				this.showModal = false;
				this.packages('invoice');
				this.$store.dispatch('unsetAlert');
			},

			addInvoice()
			{
				this.showModal = true;
			}
		},

		components : {

			'billing-packages-table' : require('./BillingPackagesTable'),
			
			'orders-table' : require('./Tables/Orders'),

			'invoices-table' : require('./Tables/Invoices'),

			'order-modal': require('./Tables/MiniComponents/OrdersModal'),
		},
	};
</script>

<style scoped>
	.box.box-primary{
		padding : 0px !important;
	}
	#package_tab{
		cursor: pointer;
	}
	#badge{
		border-radius: 3px !important;
	}
	#package_content{
		padding-bottom: 30px !important;
	}
</style>