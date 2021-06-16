<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="divStyle">

		<div slot="title">
	
			<h4>{{lang('forward_ticket')}}</h4>
		</div>

		<div slot="fields" id="forward_fields">
			
			<dynamic-select :label="lang('users')" :multiple="true" name="users" :required="true"
			 	 classname="col-sm-12" apiEndpoint="/api/dependency/users?meta=true" 
				:value="users" :onChange="onChange" :taggable="true">

			</dynamic-select>

			<checkbox  id="send-attachments" name="sendAttachments" :value="sendAttachments" 
				:label="lang('send_attachments_along_with_ticket')" :onChange="onChange" classname="col-sm-12">
         
         </checkbox>
		</div>

		<div class="row" slot="fields" v-if="loading">

			<loader :duration="4000" :size="60"></loader>

		</div>
						
		<div slot="controls">
			
			<button type="button"  @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">
			
				<i class="fa fa-refresh"></i> {{lang('update')}}
			</button>
		</div>
	</modal>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name: 'forward-modal',

  	description: 'applies selected workflow on the given ticketId',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			ticketId : { type : String | Number, default : '' },

			reloadDetails : { type : Function }
		},

		data(){
			
			return {

				isDisabled:false,

				loading:false,

				users : [],

      		sendAttachments: false,

				divStyle : { width : '700px' }
			} 
		},

		beforeMount() {

			this.isDisabled = this.users.length > 0 ? false : true;
		},

		methods:{

			onChange(value,name) {

				this[name] = value;

				if(name === 'users')
				{
					this.isDisabled = value && value.length > 0 ? false : true;
				}
			},

			onSubmit(){
				
				let emails = this.users.map(user => {
		         // if user has a property called email, it will be passed, else id
		         if(user.hasOwnProperty('email')){
		            return user.email;
		          }
		          return user.name;
		      });
				
				this.loading = true;

				this.isDisabled = true;

				const data = {};

				data['ticket_id'] = this.ticketId;

				data['emails'] =  emails;

				data['send_attachments'] =  this.sendAttachments;

				axios.post('/api/forward-ticket',data).then(res=>{

					this.reloadDetails();

					successHandler(res,'timeline');

					this.loading = false;

					this.isDisabled = false;

					this.onClose();

				}).catch(err=>{

					errorHandler(err,'timeline');

					this.loading = false;

					this.isDisabled = false;
				})
			},
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),

			'checkbox' :require('components/MiniComponent/FormField/Checkbox'),
		},
	};
</script>

<style scoped>
	#send-attachments { display : inline-block !important; }
	/*#forward_fields {
		max-height: 300px;
		overflow-y: auto;
		overflow-x: hidden;
	}*/
</style>