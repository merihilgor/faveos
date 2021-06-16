<template>
	
	<div class="btn-group">
												
		<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
			
			<i class="fa fa-refresh"> </i> {{ lang('associate_change') }} <span class="caret"></span>
		
		</button>
			
		<ul class="dropdown-menu">

			<template v-if="actions.attach_change_initiated">
				
				<li> <a>{{lang('change_initiated_by_this_ticket')}}</a> </li>

				<li>
							
					<a class="cursor" @click="showModalMethod('attach_new')" id="attach_new">

						<span class="margin"> <i class="fa fa-circle-o"> </i> {{ lang('new_change') }} </span>
					</a>
				</li>
						
				<li>
							
					<a class="cursor" @click="showModalMethod('attach_exists')" id="attach_exists">

						<span class="margin"> <i class="fa fa-circle-o"> </i> {{ lang('existing_change') }} </span>
					</a>
				</li>
			</template>
			
			<li v-if="actions.attach_change_initiating">
						
				<a class="cursor" @click="showModalMethod('attach_initiating')" id="attach_initiating">

					{{ lang('change_initiating_this_ticket') }} 
				</a>	
			</li>			
		</ul>
			
		<transition name="modal">

		 	<change-attach-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :title="title" :data="data"
		 	:type="type"> 
		 	
		 	</change-attach-modal>
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

				title : '',

				type : ''
			}
		},

		methods:{
			
			showModalMethod(id){

				this.currentModal = id
				
				this.title = id === 'attach_new' ? 'create_a_change_to_this_ticket' :  'link_to_an_existing_change'
				
				this.type = id === 'attach_initiating' ? 'initiating' : 'initiated'

				this.showModal = true
			},

			onClose(){

				this.showModal = false;
				
				this.$store.dispatch('unsetValidationError');
			},
		},

		components: {

			'change-attach-modal' : require('./AttachChangeModal.vue')					
		},
		
	};
</script>

<style scoped>
	.inline{
		display: inline;
	}
	.cursor{
		cursor: pointer;
	}
	#change{
		margin-left: 0px !important;
	}
	.margin{
		margin-left: 15px;
	}
</style>
