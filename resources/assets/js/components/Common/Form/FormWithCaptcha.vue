<template>
	
	<div>
		
		<slot name="fields"></slot>

		<recaptcha-field :key="'captcha'+counter" v-captcha="page" :verifyCaptcha="getCaptcha" name="captcha">
			
		</recaptcha-field>

		<div class="row">

			<div class="col-sm-12">
			
				<button :id="btn_id" :class="btnClass" @click="onSubmit()" :style="btnStyle"> 

					<i :class="iconClass"> </i> {{trans(btnName)}}
				</button>
			</div>
		</div>

		<slot name="actions"></slot>
	</div>
</template>

<script>
	
	export default {

		name : 'form-with-captcha',

		description : 'Form with Recaptcha Component',

		props : {

			btn_id : { type : String, default : ''},
			 
			btnClass : { type : String, default : 'btn btn-primary' },

			btnName : { type : String, default : 'save' },

			btnStyle : { type : Object, default : ()=>{} },

			iconClass : { type : String, default : 'fa fa-save' },

			formSubmit : { type : Function },

			page : { type : String, default : '' },

			componentName : { type : String, default : '' }
		},

		data() {

			return {

				captchaKey : '',

				counter : 0
			}
		},

		created() {

			window.eventHub.$on('login-failure', this.updateCaptcha);

			window.eventHub.$on('forComment',this.updateCaptcha);
		},

		methods : {

			updateCaptcha() {

				this.counter += 1;
			},

			getCaptcha(key) {

				this.captchaKey = key;
			},

			onSubmit() {

				if(this.page && this.recaptchaApplyfor.includes(this.page)){

					if(this.captchaKey){

						this.formSubmit('g-recaptcha-response',this.captchaKey)
					
					} else {

						this.$store.dispatch('setAlert',{
							type:'danger',message:'Invalid ReCaptcha', component_name : this.componentName
						})
					}
				} else {

					this.formSubmit()
				}
			}
		},

		components : {

			'recaptcha-field' : require('components/FormFields/RecaptchaField.vue')
		}
	};
</script>