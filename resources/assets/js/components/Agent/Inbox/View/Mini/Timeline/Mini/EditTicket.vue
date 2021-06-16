<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose">

		<div slot="title">
	
			<h4>{{lang('edit_ticket')}}</h4>
		</div>

		<div slot="fields" id="ticket_modal_body">
				
			 <create-form ref="editTicket"
				person="agent"
				category="ticket"
				usedby="edit-ticket"
				:categoryId="ticketId"
				:editDataApiEndpoint="editDataApiEndpoint"
				:submitApiEndpoint="submitApiEndpoint"
				:buttonStyle="btnCss"
				alertComponent="timeline"
				  >
			</create-form>
			
		</div>

		<div slot="controls">

			<button type="button" @click="onSubmit()" class="btn btn-primary">

				<i class="fa fa-refresh"></i> {{lang('update')}}
			</button>
		</div>
	</modal>
</template>

<script>

import {errorHandler, successHandler} from 'helpers/responseHandler'

import axios from 'axios'

	export default {

		name:'edit-ticket-modal',

		description:'Edit Ticket modal Component',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			ticketId : { type : String | Number, default : '' },

			reloadDetails : { type : Function },

			componentTitle : { type : String, default : '' }
		},

		data() {

			return { 

				btnCss : { visibility : 'hidden !important' }
			}
		},

		beforeMount(){
	 	
	 		this.$store.dispatch('setFormMode', 'edit-ticket');
  		},

  		created(){
	 		
	 		window.eventHub.$on('ticketFormSubmitted', () => {

				// if form is visible, then only refresh
				// REASON: all timeline action components are mounted by default
				// (then pop-up should be mounted/unmounted, intead of hidden to avoid such scenarios)
				// so if ticket form gets submitted in another component, this component is going to emit event too,
				// which will cause duplicate API calls for ticket refresh
				
				this.reloadDetails();
	  	
				setTimeout(() => {
						
					this.onClose();
											
					this.$store.dispatch("unsetAlert");
				}, 2000);
		 });

	 	window.eventHub.$on('ticketFormError', () => {

			this.onClose();
		});
	  },

	 	 computed : {
			 
			 submitApiEndpoint(){
				
				return "/ticket/update/api/" + this.ticketId;
		 	},

		 	editDataApiEndpoint(){
				
				return "/api/agent/edit-mode-ticket-details/" + this.ticketId;
		 	}
	  },

	  methods : {

	  		onSubmit() {

	  			this.$refs.editTicket.validateForm()
	  		}
	  },

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			"custom-loader": require("components/MiniComponent/Loader"),

			"create-form" : require('components/Common/Form/CreateForm.vue'),
		}
	};
</script>

<style scoped>
	
	#ticket_modal_body { max-height: 300px;overflow-y: auto;overflow-x: hidden; }
</style>