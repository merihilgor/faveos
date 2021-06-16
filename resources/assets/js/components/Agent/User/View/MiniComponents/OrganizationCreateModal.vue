<template>

	<!-- CREATE ORG -->
	<div id="create-org" class="pull-right">

		<a data-toggle="modal" @click="showModal" id="cursor"><i class="fa fa-edit"> </i>
			{{ lang('create') }} |&nbsp;</a>

		<!-- MODAL START -->
		
		<div v-show="showForm" class="modal fade" id="Org" aria-hidden="false" data-backdrop="true" data-keyboard="false" >
			<!-- MODAL DIALOG -->
			<div class="modal-dialog modal-md">
				<!-- MODAL CONTENT -->
				<div class="modal-content">

					<div class="box-container">

						<div class="box-header with-border">
							
							<button type="button" class="close" data-dismiss="modal" @click="onClose" aria-label="Close"><span aria-hidd="" en="true">×</span></button>
							
							<h3 class="box-title">{{lang('create_organization')}}</h3>
						</div>

						<div class="box-body margin-10">
							 
							<div class="row">
								
								<create-form v-if="showForm"
									person='agent'
									category='organisation'
									usedby='agent-panel'
									:editDataApiEndpoint="editDataApiEndpoint"
									:submitApiEndpoint="submitApiEndpoint"
									:buttonStyle="btnStyle"
								></create-form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

	export default {

		props : {

			userId : { type : String | Number, default : ''}
		},

		data(){
			return {
				showForm : false,
				editDataApiEndpoint : '',
				btnStyle : {}
			}
		},

		beforeMount(){

		 	this.submitApiEndpoint = "/organisation/create/api?user_id="+this.userId;
		},

		created(){
			
			console.log(this.recaptchaVersion,'recaptchaVersion');

			if(this.recaptchaVersion == 'v3') { this.btnStyle = { marginBottom : '40px' }};
			// refresh data as soon as form is submitted
			window.eventHub.$on('organisationFormSubmitted', () => {
			
				window.eventHub.$emit('refreshUserData');

				setTimeout(()=>{
          
          $("#Org").modal("hide");
          
          this.showForm = false;
        }, 2000);
			});
		},

		methods: {
			
			showModal() {
				
				$("#Org").modal("show");
				
				this.showForm = true;
			},

			onClose(){
				
				this.showForm = false;
			}
		},

		components: {
			
			"create-form" : require('components/Common/Form/CreateForm.vue'),
		}
	};
</script>

<style>
	#create-org .modal-content {
	  color: #525252;
	  text-transform: initial;
	}
	#create-org .modal-body {
	  font-size: 12px;
	}

	#create-org{
	  display: inline-block;
	}

	#create-org .modal-dialog {
	  width: 950px !important;
	}

	#create-org .box-container {
	  margin: 0px !important;
	}

	#create-org #custom-form {
	  min-height: 400px;
	}
	#cursor{
		cursor: pointer;
	}
</style>
