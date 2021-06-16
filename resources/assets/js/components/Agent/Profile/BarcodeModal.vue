<template>
	
	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
			<div slot="title">

				<h4>{{lang('setup_authenticator')}}</h4>
			</div>
			
			<div slot="fields">
				
				<div v-if="!loading" slot="fields">
				
					<div class="row" :class="{bar_row : from === 'client'}">

						<template>
							
							<div class="text-center" v-if="passwordVerified">

								<template v-if="showBarcode && !showPasscode">

									<ul class="col-sm-offset-3 offset-sm-2 text-left">
										
										<li>Get the Authenticator App from the Play Store.</li>
										
										<li>In the App select <b>Set up account.</b></li>
										
										<li>Choose <b>Scan a barcode.</b></li>
									</ul>

									<div class="col-sm-offset-4">

										<img class="img-responsive" :src="factorData.image" alt="barcode">
									</div>

									<a href="javascript:;" id="scan" @click="keyCode()" :style="linkStyle">{{lang('cant_scan')}}</a>
								
								</template>

								<template v-if="showKeycode && !showBarcode && !showPasscode">
									
									<div class="row">

										<div class="col-sm-8 col-sm-offset-2 offset-sm-2 text-left">

											<ul>
											
												<li>Tap <b>Menu</b>, then <b>Set up account.</b></li>
												
												<li>Tap <b>Enter provided key.</b></li>
												
												<li>Enter your email address and this key : </li>

											</ul>
										</div>

										<div class="col-sm-8 col-sm-offset-2 offset-sm-2" id="secret_key">

											<input type="text" :value="factorData.secret" class="form-control" readonly>
										</div> 

										<div class="col-sm-8 col-sm-offset-2 offset-sm-2 text-left">

											<ul>

												<li>Make sure <b>Time based</b> is turned on, and tap <b>Add</b> to finish.</li>
											</ul>
										</div>
										
									</div>

									<a href="javascript:;" id="scan" @click="keyCodeReverse()" :style="linkStyle">{{lang('scan_barcode')}}</a>
								
								</template>

								<template v-if="showPasscode && !showBarcode">

									<div class="col-sm-12" v-if="!codeVerified" :class="{flex_row : from === 'client'}">

										<text-field :label="lang('enter_the_code_you_see_in_the_app')" :value="pass_code" type="text" name="pass_code" 
											placehold="Enter Passcode..." :onChange="onChange" classname="col-sm-9 text-left">
													
										</text-field>

										<div class="col-sm-3">

											<button type="button" class="btn btn-primary pull-right float-right" id="pass_btn" :disabled="pass_code ? false : true"
												@click="validatePassCode()" :style="btnStyle" :class="{flex_btn : from === 'client'}"> 

												<i class="fa fa-check"> </i> {{lang('verify')}} 

											</button>
										</div>
									</div>

									<div class="col-sm-12" v-if="codeVerified">
										
										<div class="col-sm-12">
											<p class="text-left">
												You're all set. From now on, you'll use Authenticator to sign in to your Faveo Account.
											</p>
										</div>
									</div>
								</template>
							</div>

							<div v-if="!passwordVerified">
								
								<div class="col-sm-12">

									<text-field :label="lang('to_continue_first_verify')" :value="password" type="password" name="password" 
										placehold="Enter Password..." :onChange="onChange" classname="col-sm-12">
												
									</text-field>
								</div>
							</div>

						</template>
					</div> 
				</div>
				
				<div v-if="loading" class="row" slot="fields" >
				
					<loader :animation-duration="4000" :size="60" :color="color"/>
				</div>	

				<div class="row" v-if="verifyLoader"  slot="fields">

					<custom-loader :duration="4000" :color="color"></custom-loader>
					
				</div>		
			</div>

			<template slot="controls">

				<template v-if="!passwordVerified && !codeVerified">
					
					<button type="button" class="btn btn-primary pull-right float-right" :disabled="password ? false : true"
						@click="validatePass()" :style="btnStyle"> 

						<i class="fa fa-check"> </i> {{lang('validate')}}
					</button>
				</template>

				<template v-if="passwordVerified">
					
					<button v-if="showBarcode || showKeycode && !showPasscode" class="btn btn-primary pull-right float-right" @click="passCode()"
						:style="btnStyle"> 

						{{lang('next')}} &nbsp;&nbsp;<i class="fa fa-arrow-right"> </i>
					</button>

					<button v-if="showPasscode && !codeVerified" class="btn btn-primary pull-right float-right" @click="barCode()" id="prev_btn"
						:style="btnStyle"> 

						<i class="fa fa-arrow-left"> </i>&nbsp;&nbsp;{{lang('previous')}} 
												
					</button>
				</template>
				
				<button v-if="codeVerified" class="btn btn-primary" @click="onDone()" :style="btnStyle" :class="{'float-right' : from === 'client'}">
					<i class="fa fa-check"> </i> {{lang('done')}}</button>
			</template>
		</modal>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios'

	export default {
		
		name : 'barcode-modal',

		description : 'BarCode Modal component',

		props:{
	
			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			color : { type : String, default : '#1d78ff'},

			btnStyle : { type : Object, default : ()=> {}},

			linkStyle : { type : Object, default : ()=> {}},

			from : { type : String, default : ''}
		},

		data(){

			return {

				isDisabled:true,

				containerStyle:{ width:'600px' },

				loading:false,

				showBarcode : true,

				btnName : 'next',

				passwordVerified : false,

				password : '',

				labelStyle : { visibility : 'hidden' },

				remember : true,

				verifyLoader : false,

				showPasscode : false,

				showKeycode : false,

				pass_code : '',

				factorData : '',

				codeVerified : false
			}
		},

		beforeMount() {

			this.getBarCode();
		},

		methods:{

			getBarCode(){

				this.loading = true;

				axios.get('/2fa/enable').then(res=>{

					this.factorData = res.data.data;

					this.loading = false;

				}).catch(err=>{

					this.loading = false;
				})
			},

			onChange(value, name) {

				this[name] = value;
			},

			validatePass(){

				this.verifyLoader=true;

				const data = {}

				data['password'] = this.password;

				axios.post('/verify/password', data).then(res=>{

					this.passwordVerified = true;

					this.verifyLoader = false;
				
				}).catch(err=>{

					this.passwordVerified = false;

					this.verifyLoader = false;

					this.$store.dispatch('setValidationError', {'password' : 'Incorrect password.'})

				})
			},

			passCode(){

				this.showBarcode = false;

				this.showPasscode = true;
			},

			barCode() {

				this.showPasscode = false;

				this.showBarcode = true;
			},

			keyCode() {

				this.showBarcode = false;

				this.showPasscode = false;

				this.showKeycode = true;
			},

			keyCodeReverse(){

				this.showBarcode = true;

				this.showPasscode = false;

				this.showKeycode = false;
			},

			validatePassCode(){

				this.verifyLoader=true;

				const data = {}

				data['totp'] = this.pass_code;

				axios.post('/2fa/setupValidate', data).then(res=>{

					window.eventHub.$emit('updateEditData');

					this.codeVerified = true; 

					this.verifyLoader = false;
				
				}).catch(err=>{

					this.verifyLoader = false;

					this.$store.dispatch('setValidationError', {'pass_code' : 'Wrong code. Try again'})

				})
			},

			onDone(){

				this.onClose();
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'text-field': require('components/MiniComponent/FormField/TextField'),

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

	.bar_row{
		display: flow-root !important;
	}

	#secret_key{
		margin-bottom: 15px;
	}

	.flex_row{
		display: flex;
	}

	.flex_btn{
		margin-top: 30px !important;
	}
</style>