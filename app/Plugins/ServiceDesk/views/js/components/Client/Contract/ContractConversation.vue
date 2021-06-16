<template>
	
	<div>

		<meta-component :dynamic_title="trans('approval_settings-page-title')" :dynamic_description="trans('approval_settings-page-description')" :layout="layout" >
				
		</meta-component>

		<alert/>
		
		<div class="row" v-if="statusText === '' && hasDataPopulated">

			<div id="content" class="site-content col-sm-12">
								
				<article class="hentry">
										
					<header class="entry-header">
											
						<h3 class="entry-title" :class="{align1: lang_locale == 'ar'}">
							
							<i class="fas fa-file-contract"> </i> 
							
							<span class="h3 fw-700" v-tooltip="contractDetails.name"> {{ contractDetails.name }}</span> 
						</h3>
					</header>

					<div class="entry-content clearfix" :class="{align1: lang_locale == 'ar'}">

						<div class="row">

		  					<div class="col-md-12">

			 					<div class="alert alert-info">

									<div class="row">

										<div class="col-md-4">

											<b>{{trans('start_date')}} : </b>

											<span  v-tooltip="formattedTime(contractDetails.contract_start_date)"> {{formattedTime(contractDetails.contract_start_date)}}

											</span>
										</div>

										<div class="col-md-4">

											<b>{{trans('expiry_date')}} : </b>

											<span  v-tooltip="formattedTime(contractDetails.contract_end_date)"> {{formattedTime(contractDetails.contract_end_date)}}

											</span>
										</div>

										<div class="col-md-4">

											<b>{{trans('contract_type')}} : </b>

											<span v-tooltip="contractDetails.contractType ? contractDetails.contractType.name : '---'"> 

											  {{contractDetails.contractType ? subString(contractDetails.contractType.name,20) : '---'}}
											</span>
										</div>
									</div>
								 </div>
							  </div>
							</div>

							<div class="row">
								
								<div class="col-md-6">

									<table class="table">
										
										<tbody>
										
											<tr>

												<td><b>{{ trans('vendor') }} :</b></td> 

												<td> <span v-tooltip="contractDetails.vendor ? contractDetails.vendor.name : '---'">
													{{ contractDetails.vendor ? subString(contractDetails.vendor.name) : '---'}} </span>
												</td>
											</tr>

											<tr>

												<td><b>{{ trans('contract_status') }} :</b></td> 
												
												<td> <span v-tooltip="contractDetails.status.name"> {{ subString(contractDetails.status.name)}}</span> </td>
											</tr>

											<tr>

												<td><b>{{ trans('license_type') }} :</b></td> 
												
												<td> <span v-tooltip="contractDetails.licence ? contractDetails.licence.name : '---'">
													{{ contractDetails.licence ? subString(contractDetails.licence.name) : '---'}} </span>
												</td>
											</tr>							
										</tbody>
									</table>
									<div class="clearfix"></div>
								</div>

								<div class="col-md-6">

									<table class="table">
										
										<tbody>
										
											<tr>

												<td><b>{{ trans('cost') }} :</b></td> 

												<td> <span v-tooltip="contractDetails.cost.toString()"> {{ contractDetails.cost}}</span></td>
											</tr>

											<tr>

												<td><b>{{ trans('contract_creator') }} :</b></td> 
												
												<td>
													<span v-tooltip="contractDetails.owner ? contractDetails.owner.name : '---'">
														{{ contractDetails.owner ? subString(contractDetails.owner.name) : '---'}} 
													</span>
												</td>
											</tr>

											<tr>

												<td><b>{{ trans('license_count') }} :</b></td> 
												
												<td> <span v-tooltip="contractDetails.licensce_count ? contractDetails.licensce_count.toString() : '---'">

													{{ contractDetails.licensce_count ? subString(contractDetails.licensce_count) : '---'}} </span>
												</td>
											</tr>							
										</tbody>
									</table>
									<div class="clearfix"></div>
								</div>
							</div>
					</div>
				</article>

				<div v-if="!loading && shallShowApprovalAction" id="approval-action-form" :class="{align1: lang_locale == 'ar'}">

						<text-field id="textarea" :label="trans('comment')" :value="comment" type="textarea" name="comment"
						:onChange="onChange" classname="col-sm-12" :hint="trans('contarct_approval_comment')"></text-field>
						
						<div class="col-sm-12">

							<button id="approve" @click = "onClick('approve')" class="btn btn-success" :disabled="isDisabled">
									
								<span class="fas fa-check"></span> {{trans('approve')}}
							</button>
									
							<button id="deny" @click = "onClick('reject')" class="btn btn-danger" :disabled="isDisabled">
									
								<span class="fas fa-times"></span> {{trans('reject')}}
							</button>
						</div>
					</div>
				</div>
			</div>

		<div v-if="loading" id="loader" class="row">
						
			<loader :animation-duration="4000" :size="60" :color="layout.portal.client_header_color"/>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { getSubStringValue } from 'helpers/extraLogics'

	import { mapGetters } from 'vuex'

	import { validateContractApprovalSettings } from "../../../validator/contractApprovalSettings.js"

	export default {

		name : 'contract-conversation',

		description : 'Contract Conversation Component',

		props : { 

			layout : { type :  Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data () {
			return {
				
				contractDetails: '',

				loading: true,

				hasDataPopulated : false,

				isDisabled: false,

				lang_locale : this.layout.language,

				details : {},

				statusText : '',

				comment : '',

				shallShowApprovalAction: true,

				actionType : ''
			}
		},

		beforeMount() {

			this.$Progress.start();

			this.getInitialValues();
		},

		computed :{

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods:{
			
			subString(value,length = 15){
	 
				return getSubStringValue(value,length)
			},

			getHashFromUrl(url){

				let obj = {};

				let urlArray = url.split("/");

				obj['hash'] = urlArray[urlArray.length - 2];

				obj['id'] = urlArray[urlArray.length - 1];

				return obj;
			},

			isValid(){
			
				const {errors, isValid} = validateContractApprovalSettings(this.$data)
				
				return isValid
			},

			onChange(value, name){
			
				this[name]= value;
			},

			onClick(value) {

				this.actionType = value;

				if(value == 'reject') {

					if(this.isValid()){
				
						this.onSubmit();
					}
				} else {

					this.onSubmit();
				}
			},

			onSubmit() {

				this.loading = true
				
					this.isDisabled = true
					
					this.$Progress.start();

					let data = {};

					data['actionType'] = this.actionType;

					if(this.actionType == 'reject') {

						data['purpose_of_rejection'] = this.comment;
					}

					if(this.actionType == 'approve') {

						data['purpose_of_approval'] = this.comment;
					}

					axios.post('/service-desk/api/contract-approval/'+this.details['hash']+'/'+this.details['id'],data).then(res=>{
				
						successHandler(res);
				
						this.loading = false;
				
						this.isDisabled = false;
				
						this.comment = '';
				
						this.shallShowApprovalAction = false;

						this.$Progress.finish();

						setTimeout(()=>{

							this.$router.push({ path:'/',name: 'Home'});
						},2000)
				
					}).catch(err => {
				
						errorHandler(err)
				
						this.loading = false
				
						this.isDisabled = false
				
						this.shallShowApprovalAction = true;

						this.$Progress.fail();
					})
			},

			getInitialValues(){
				
				const path = window.location.pathname;
				
				this.details = this.getHashFromUrl(path);

				axios.get('/service-desk/api/contract-details/'+this.details['hash']+'/'+this.details['id']).then(res => {
				
					this.contractDetails = res.data.data.contract;

					this.loading = false

					this.hasDataPopulated = true;

					this.$Progress.finish();

				}).catch(err => {

					this.statusText = err.response.data.message
				
					this.hasDataPopulated = true
				
					this.loading = false
					
					this.$Progress.fail();

					this.$router.push({ path:'/not-found',name: 'NotFound',params: { statusText: this.statusText }});
				})
			},
		},
		components:{
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader': require('components/MiniComponent/Loader'),

			'text-field': require('components/MiniComponent/FormField/TextField'),
		}
	};
</script>

<style scoped type="text/css">
	.fw-700 { font-weight: 700 !important; }
</style>