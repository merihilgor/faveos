<template>
	
	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
			<div slot="title">

				<h4>{{lang('turn_off_authenticator')}}</h4>
			</div>
			
			<div slot="fields">
				
				<div slot="fields">
				
					<div class="row">

						<div class="col-sm-12">
							
							<p id="pad20">
								Turning off 2-Step Verification will remove the extra security on your account, and you’ll only use your password to sign in.
							</p>
						</div>
					</div> 
				</div>

				<div class="row" v-if="loading"  slot="fields">

					<custom-loader :duration="4000" :color="color"></custom-loader>
					
				</div>	
			</div>

			<template slot="controls">
					
				<button class="btn btn-danger pull-right float-right" @click="onRemove()"> <i class="fa fa-power-off"></i> {{lang('turn_off')}}</button>
			</template>	
		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'remove-authenticator-modal',

		description : 'Remove Authenticator Modal component',

		props:{
	
			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			alertName : { type : String, default : 'edit_agent_profile'},

			id : { type : String | Number, default : ''},

			color : { type : String, default : '#1d78ff'}
		},

		data(){

			return {

				isDisabled:true,

				containerStyle:{ width:'600px' },

				loading:false,
			}
		},

		methods:{

			onRemove(){

				this.loading = true;

				this.isDisabled = true;

				axios.get('/2fa/disable/'+this.id).then(res=>{

					window.eventHub.$emit('updateEditData');

					this.onClose();

					successHandler(res, this.alertName)

					this.loading = false;

					this.isDisabled = false;

				}).catch(err=>{

					this.onClose();

					errorHandler(err, this.alertName);

					this.isDisabled = false;

					this.loading = false;
				})
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),
		}
	};
</script>

<style type="text/css">

	#H5{
		margin-left:16px; 
	}

	#scan{
		text-decoration: underline !important;
	}

	#pass_btn{
		margin-top: 24px;
	}

	#prev_btn{
		margin-left: 30px;
	}

	#pad20{
		padding: 20px;
	}
</style>