<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose">

		<div slot="title">
	
			<h4>{{lang('fork_ticket')}}</h4>
		</div>

		<div slot="fields" id="ticket_fork_body">
				
			<create-form ref="forkTicket"
				person="agent"
				category="ticket"
				usedby="fork-ticket"
				:categoryId="ticketId"
				:editDataApiEndpoint="editDataApiEndpoint"
				:submitApiEndpoint="submitApiEndpoint"
				:buttonStyle="btnCss"
				alertComponent="timeline">
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

		name:'fork-ticket-modal',

		description:'Fork Ticket modal Component',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			ticketId : { type : String | Number, default : '' },

			reloadDetails : { type : Function },

			componentTitle : { type : String, default : '' }
		},

	  	beforeMount(){
		 	// setting form mode to fork
		 	this.$store.dispatch('setFormMode', 'fork-ticket');
	  	},

	  	data() {

	  		return {

	  			btnCss : { visibility : 'hidden !important' }
	  		}
	  	},

  		created(){
	 		
	 		window.eventHub.$on('ticketFormSubmitted', () => {

				// if form is visible, then only refresh
				// REASON: all timeline action components are mounted by default
				// (then pop-up should be mounted/unmounted, intead of hidden to avoid such scenarios)
				// so if ticket form gets submitted in another component, this component is going to emit event too,
				// which will cause duplicate API calls for ticket refresh
				// emit events which causes ticket data to update
				this.reloadDetails();
  	
				setTimeout(() => {
					
					this.onClose();
										
					this.$store.dispatch("unsetAlert");
				}, 2000);
		 	});
	  	},

	  	computed : {
		 	
		 	submitApiEndpoint(){
				
				return "/fork/ticket/" + this.ticketId;
		 	},

		 	editDataApiEndpoint(){
				
				return "/api/agent/edit-mode-ticket-details/" + this.ticketId;
		 	}
			},

	  	 methods : {

	  		onSubmit() {

	  			this.$refs.forkTicket.validateForm()
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
	
	#ticket_fork_body { max-height: 300px;overflow-y: auto;overflow-x: hidden; }
</style>