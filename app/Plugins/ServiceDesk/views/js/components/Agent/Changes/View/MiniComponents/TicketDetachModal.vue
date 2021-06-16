<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title" id="alert_top">

			<alert componentName="detach-ticket"/>
				
		</div>

		<div slot="title">

			<h4>{{lang('detach')}}</h4>
			
		</div>
			
		<div v-if="!loading" slot="fields">
			
			<h5 id="H5">{{lang('are_you_sure')}}</h5>
		</div> 
			
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>
						
		<div slot="controls">
			
			<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

				<i class="fa fa-unlink"></i> {{lang('detach')}}
			</button>
		</div>
	</modal>
</template>

<script>
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'detach-ticket-modal',

		description : 'Detach Ticket Modal component',

		props:{
			
			showModal : { type : Boolean, default : false },

			changeId : { type : String | Number , default : ''},

			ticketId : { type : String | Number , default : ''},

			type : { type : String , default : ''},

			onClose : { type : Function }
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle : { width : '500px' },

				loading : false
			}
		},

		methods:{

			onSubmit(){
				
				this.loading = true
				
				this.isDisabled = true

				axios.delete('/service-desk/api/detach-ticket/change',{

					params : {

						'ticket_id' : this.ticketId,

						'change_id' : this.changeId,

						'type' : this.type
				
					}
				}).then(res=>{
					
					successHandler(res,'changes-view');
					
					window.eventHub.$emit('updateActivity');
					
	        window.eventHub.$emit('ChangeTicketsrefreshData');
	        
	        this.onClose()
					
					this.loading = false;
					
					this.isDisabled = true;
			
				}).catch(err => {
				
					this.loading = false;
					
					this.isDisabled = false;

					errorHandler(err,'detach-ticket')
				})
			}
		},

		components:{
		
			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css">

	#H5{
		margin-left:16px; 
	}
	
	#alert_top{
		margin-top:20px
	}

	#alert_msg {
		margin: 0px 5px 30px 5px;
	}

	.btn-danger {
    background-color: #c9302c !important;
    border-color: #ac2925 !important;
	}
</style>