<template>

	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">
			
				<h4>{{lang('change_password')}}</h4>
			
			</div>

			<div slot="fields" class="row" v-if="!loading">

				<div :class="class_name" id="register">
						
					<label id="pass">{{lang('new_password')}}</label><span class="text-red"> *</span>

					<button class="btn btn-sm btn-default pull-right" id="random" @click="getRandomPass()">

						<i class="fa fa-key">&nbsp;&nbsp;</i>{{lang('password_generator')}}
					</button>
					
					<div class="col-xs-12">	

						<input type="password" class="form-control" name="change_password" id="changepassword1" v-model="password"
						@keyup="keyUp(password)">
								
						<a id="basic-addon" @click="mouseoverPass()">
										
							<span id="eye" class="fa fa-eye"></span>
						</a>
					</div>

					<div class="col-xs-12">
						
						<span class="help-block" id="demo">{{lang(statusText)}}</span>
					</div>
				</div>
			</div>
			<div v-if="loading" class="row" slot="fields" >
				
				<loader :animation-duration="4000" :size="60"/>
			</div>
						
			<div slot="controls">

				<button type="button" id="submit_btn" @click = "onSubmit" class="btn btn-primary" :disabled="isDisabled">
					
					<i class="fa fa-check"></i> {{lang('proceed')}}
				</button>
			</div>
		</modal>
	</div>

</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { passwordLengthValidation } from 'helpers/extraLogics'

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {
		
		name : 'password-modal',

		description : 'Password Modal component',

		props:{

			showModal : { type : Boolean, default : false},

			userId : { type : String | Number, default : '' },

			onClose : { type : Function},
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle:{ width:'800px' },

				loading:false,
				
				password  : '',

				class_name : 'col-xs-12 form-group',

				statusText : ''
			}
		},

		methods:{
			
			mouseoverPass(){

				var obj = document.getElementById('changepassword1');

				var obj1 = document.getElementById('eye');
				
				obj.type =  obj.type === "text" ? "password" : "text";
				
				obj1.className = obj1.className === "fa fa-eye" ? "fa fa-eye-slash" : "fa fa-eye" ;
			},

			keyUp(password){

				this.statusText = this.checkStrLength(password);
			},

			checkStrLength(password) {

				let message = passwordLengthValidation(password);

				if(message === 'too_short' || message === 'weak'){
						
					this.class_name = 'col-xs-12 form-group has-error'; 					
				
				} else if(message === 'good'){

					this.class_name = 'col-xs-12 form-group has-warning'; 
				} else {

					this.class_name = 'col-xs-12 form-group has-success';
				}

				return message;
			},

			getRandomPass(){

				this.class_name = 'col-xs-12 form-group'

				this.statusText = '';

				axios.get('/get/random/password').then(res=>{

					this.password = res.data.data;

				}).catch(error=>{ 

					this.password = ''
				})
			},

			onSubmit(){
				
				if (this.password.length < 6) {
				
					this.class_name = 'col-xs-12 form-group has-error'

					return this.statusText = 'your_password_must_be_6_character';

				} else {

					this.loading = true;

					this.isDisabled = true;

					const data = {};

					data['change_password'] = this.password;
					
					axios.post('/changepassword/'+this.userId,data).then(res=>{

						successHandler(res,'user-view')
							
						this.afterSuccess();

					}).catch(error=>{

						errorHandler(error,'user-view')
									
						this.afterResponse();
					})
				}
			},

			afterSuccess(){

				this.afterResponse();

				window.eventHub.$emit('refreshUserData');
			},

			afterResponse(){

				this.loading = false;

				this.isDisabled = false;
				
				this.onClose();
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
		
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css">
	
	#eye{
		float: right;margin-left: -25px;margin-top: -35px;
		margin-right: 6px;
		position: relative;
		z-index: 2;
		color: black;
	}
	#pass{
		margin-left: 15px;
	}
	#random{
		margin-bottom: 8px;
		margin-right: 15px;
	}
</style>