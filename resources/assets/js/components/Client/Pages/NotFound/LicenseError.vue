<template>
	
	<div class="row">

		<div id="content" class="site-content col-sm-12">

			<div v-if="loading">

        <client-panel-loader :size="60"></client-panel-loader>
      </div>

      <div v-if="!hasDataPopulated">

        <loader :size="60"></loader>
      </div>

      <alert componentName="License"/>

			<article class="hentry" v-if="verify">

				<header class="entry-header text-center">

					<h3 class="entry-title">{{lang('licenseCode')}}</h3>
				</header>

				<div class="entry-content clearfix">

					<p class="text-center">You can find your license code in our billing portal 
						<a href="https://billing.faveohelpdesk.com" target="_blank">billing.faveohelpdesk.com</a>
					</p>

					<p class="text-center"><b>Please enter your License Code for Faveo Helpdesk Enterprise</b></p>

					<p class="container text-center">

						<input type="text" name="set1" id="set1" v-model="set1" maxlength="4" size="4" v-validate="'required'"
							@keydown="keyDown($event, 'set1');" @paste="onPaste($event,'set1');"
							class="form-control inline_block text-uppercase" :class="{'field-danger': errors.has('set1') }">&nbsp;-

						<input type="text" name="set2" id="set2" v-model="set2" maxlength="4" size="4" v-validate="'required'"
							@keydown="keyDown($event, 'set2');"  @paste="onPaste($event,'set2');" 
							class="form-control inline_block text-uppercase" :class="{'field-danger': errors.has('set2') }">&nbsp;-

						<input type="text" name="set3" id="set3" v-model="set3" maxlength="4" size="4" v-validate="'required'"
							@keydown="keyDown($event, 'set3');"  @paste="onPaste($event,'set3');" 
							class="form-control inline_block text-uppercase" :class="{'field-danger': errors.has('set3') }">&nbsp;-

						<input type="text" name="set4" id="set4" v-model="set4" maxlength="4" size="4" v-validate="'required'"
							@keydown="keyDown($event, 'set4');"  @paste="onPaste($event,'set4');" 
							class="form-control inline_block text-uppercase" :class="{'field-danger': errors.has('set4') }">
					</p>

					<p class="text-center"> 

						<button class="btn btn-primary" @click="onSubmit()">
							<i class="fa fa-save"> </i> {{lang('submit')}}
						</button>
					</p>
				</div>
			</article>
		</div>
	</div>
</template>

<script>
	
	import { errorHandler, successHandler } from 'helpers/responseHandler'

	import axios from  'axios'

	export default {
		
		data(){

			return {

				set1 : '', 

				set2 : '',

				set3 : '', 

				set4 : '',

				loading : false,

				hasDataPopulated : false,

				verify : false
			}
		},

		beforeMount(){

			this.getValues();
		},

		methods : {

			getValues(){

				this.$Progress.start();

				axios.get('/api/licenseError').then(res=>{

					this.hasDataPopulated = true;

					this.verify = true;

					this.$Progress.finish();

				}).catch(error=>{

					this.$Progress.fail();

					this.hasDataPopulated = true;

					errorHandler(error,'License');

					var msg = error.response.data.message;

					if(msg.includes('not') || msg.includes('expired')){

						this.verify = true	
					} else {

						this.redirect('/')
					}
				})
			},

			keyDown(e, id) {

				var target = e.target;
				
				var code = e.which || e.keyCode;

				if( code  != 8 && code  != 46 ){

					if(target.value.length === 4){

						if(e.target.nextElementSibling){
							
							e.target.nextElementSibling.focus()

						}
					}
				}

				if( code  == 8 || code  == 46 ){
					
					if(!e.target.value){
							
						if(e.target.previousElementSibling){
							
							e.target.previousElementSibling.focus();
						}
					}
				}
			},

			onPaste(ev,el) {

				ev.preventDefault();
				
				var text = (ev.originalEvent || ev).clipboardData.getData('text/plain');

				var clipboardDataLenght = text.length;
				
				var start = 0;
				
				var end = 3;

				var target = ev.target;
				
				while(clipboardDataLenght > 0) {
					
					this[target.id] = text.substring(start, end+1);

					target.value = text.substring(start, end+1);
					
					if(clipboardDataLenght < 4) {
						
						if(target.nextElementSibling){

							target.nextElementSibling.focus();

						}
						
						clipboardDataLenght = 0;
					
					} else if(clipboardDataLenght == 4) {
						
						clipboardDataLenght = 0;
						
						if(target.nextElementSibling){

							target.nextElementSibling.focus();

						}
					} else {
						
						clipboardDataLenght = clipboardDataLenght - 4;
						
						start = start + 4;
						
						end = end + 4;
						
						target = target.nextElementSibling;

						if(target.nextElementSibling){
							
							target.nextElementSibling.focus();

						}
					}
				}
			},

			isValid(){
				
				const {errors, isValid} = validateLicenseSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			onSubmit(){

				this.$validator.validateAll().then(result=>{
					
					if(result) {
						
						this.$Progress.start();

						this.loading = true;

						const data = {};

						data['first'] = this.set1;
						
						data['second'] = this.set2;
						
						data['third'] = this.set3;

						data['forth'] = this.set4;

						axios.post('/licenseVerification',data).then(res=>{

							this.loading = false;

							successHandler(res,'License');

							this.redirect('/');
							
							this.$Progress.finish();
						
						}).catch(error=>{

							this.loading = false;

							errorHandler(error,'License');

							this.$Progress.fail();

						})
					}
				})
			}
		},

		components : {

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style scoped>
	.inline_block{
		width: auto !important;
		display: inline-block !important;
	}
	#set1,#set2,#set3,#set4{
		max-width: 10% !important;
    width: 10% !important;
    flex: 0 0 10% !important;
	}
	#dash{
		margin-top: 5px;
    color: #838586;
	}
	.field-danger{
		border : 1px solid red;
	}
</style>