<template>
	
		<div class="btn-group">
												
			<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
			
				<i class="fa fa-bug"> </i> {{ lang('problem') }} <span class="caret"></span>
			
			</button>
			
			<ul class="dropdown-menu">

				<li v-if="actions.show_attach_new_problem">
						
					<a class="cursor" @click="showModalMethod('attach_new')" id="attach_new"><i class="fa fa-bug"></i>
						{{ lang('attach_new_problem') }} 
					</a>
					
				</li>

				<li v-if="actions.show_attach_existing_problem">
						
					<a class="cursor" @click="showModalMethod('attach_exists')" id="attach_exists"><i class="fa fa-bug"></i>
						{{ lang('attach_existing_problem') }} 
					</a>
					
				</li>			
			</ul>
			
			<transition name="modal">

		 		<problem-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :title="title" :data="data"> 
		 		
		 		</problem-modal>
			</transition> 
		</div>
</template>

<script>

	export default{

		props : {

			actions : { type : Object, default : ()=> {}},

			data : { type : Object|String, required : true}
		},
			 
		data(){
			return {

				showModal : false,

				title : ''
			}
		},
			 

		methods:{
			
			showModalMethod(id){

				this.currentModal = id
				
				this.title = id === 'attach_new' ? 'attach_new_problem' : 'attach_existing_problem'
				
				this.showModal = true
			},

			onClose(){
				
				this.showModal = false;
				
				this.$store.dispatch('unsetValidationError');
			},
		},

		components: {
			
			'problem-modal' : require('./AttachProblemModal.vue')					
		}
	};
</script>

<style scoped>
	.inline{
		display: inline;
	}
	.cursor{
		cursor: pointer;
	}
	#pblm{
		margin-left: -2px !important;
	}
</style>
