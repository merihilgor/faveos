<template>
	
	<div>

		<meta-component :dynamic_title="lang('approval_settings-page-title')" :dynamic_description="lang('approval_settings-page-description')" :layout="layout" >
				
		</meta-component>

		<alert/>
		
		<div class="row" v-if="statusText === '' && hasDataPopulated">

			<div id="content" class="site-content col-sm-12">
								
				<article class="hentry">
										
					<header class="entry-header">
											
						<h3 class="entry-title" :title="changeDetails.subject" :class="{align1: lang_locale == 'ar'}">
							
							<i class="fas fa-exchange-alt"> </i> 
							
							{{ changeDetails.subject }} 
						</h3>
					</header>

					<div class="entry-content clearfix" :class="{align1: lang_locale == 'ar'}">

						<div>
								
							<label><b>{{lang('description')}}</b></label>
								
							<div v-html="changeDetails.description"></div>
						</div>
					</div>
				</article>

				<div v-if="!loading && shallShowApprovalAction" id="approval-action-form" :class="{align1: lang_locale == 'ar'}">

					<text-field id="textarea" :label="lang('comment')" :value="comment" type="textarea" name="comment"
						:onChange="onChange" classname="col-xs-12" :required="true"></text-field>

					<div>
								
						<button id="approve" @click = "onSubmit('approve')" class="btn btn-success" :disabled="isDisabled">
								
							<span class="fa fa-check"></span> {{lang('approve')}}
						</button>
								
						<button id="deny" @click = "onSubmit('deny')" class="btn btn-danger" :disabled="isDisabled">
								
							<span class="fa fa-times"></span> {{lang('deny')}}
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

	import { validateCabActionsSettings } from "../../../validator/cabActionsRules.js"

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		name : 'change-conversation',

		description : 'Change Conversation Component',

		props : { 

			layout : { type :  Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data () {
			return {
				
				changeDetails: '',

				loading: true,

				hasDataPopulated : false,

				isDisabled: false,

				comment:'',

				lang_locale : this.layout.language,

				hash: '',

				statusText : '',

				shallShowApprovalAction: true,
			}
		},

		beforeMount() {

			this.$Progress.start();

			this.getInitialValues();
		},


		methods:{
			
			getHashFromUrl(url){

		    let urlArray = url.split("/");

		    return urlArray[urlArray.length - 1];

			},

			getInitialValues(){
				
				const path = window.location.pathname;
				
				this.hash = this.getHashFromUrl(path);

				axios.get('/service-desk/api/change-conversation/'+this.hash).then(res => {
				
					this.changeDetails = res.data.data.change;

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

    	isValid(){
			
				const {errors, isValid} = validateCabActionsSettings(this.$data)
				
				if(!isValid){
					
					return false
				}
				return true
			},
			
			onChange(value, name){
			
				this[name]= value;
			},

			onSubmit(url){
				
				if(this.isValid()){
				
					this.loading = true
				
					this.isDisabled = true
					
					this.$Progress.start();

					axios.post('/service-desk/api/approval-action/'+this.hash,{ action_type:url,comment:this.comment}).then(res=>{
				
						successHandler(res);
				
						this.loading = false;
				
						this.isDisabled = false;
				
						this.comment = '';
				
						this.shallShowApprovalAction = false;

						this.$Progress.finish();
				
					}).catch(err => {
				
						errorHandler(err)
				
						this.loading = false
				
						this.isDisabled = false
				
						this.shallShowApprovalAction = true;

						this.$Progress.fail();
					})
				}
			},
		},
		components:{
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'text-field': require('components/MiniComponent/FormField/TextField'),
		}
	};
</script>

<style scoped type="text/css">
	#comment{
		margin-left:0px !important;
		border: 1px solid #d2d6de45 !important;
	}
	#textarea{
		margin-top: 10px !important;
	}
	#change_title{
		margin-top: 0px !important;
	}
</style>