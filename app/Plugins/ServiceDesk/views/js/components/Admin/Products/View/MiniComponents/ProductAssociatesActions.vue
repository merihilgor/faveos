<template>

	<div class="btn-group">

		<a v-if="data.detach" id="detach-button" @click="()=>showModal = true" href="javascript:;">

      <span title="Detach" class="btn btn-primary btn-sm" id="detach_btn">
        <i class="fa fa-unlink"></i> 
      </span>
		</a>

		<transition name="modal">

			<product-detach v-if="showModal" :onClose="onClose" :showModal="showModal" :detachId="data.id" 
				:productId="data.product_id" :compName="data.compName">

      </product-detach>
		</transition>
	</div>
</template>

<script>

	import axios from 'axios';

	export default {

		name:"product-associates-actions",

		description: "Contains detach button which can be used as a component as whole. It is built basically for displaying deatch button in a datable.",

		props: {

			data : { type : Object, required : true },
		},

		data(){

			return{
			
				showModal : false,
			}
		},

		methods:{
			
			onClose(){
		    
		    this.showModal = false;
		    
		    this.$store.dispatch('unsetValidationError');
		  },
		},

		components:{
			
			'product-detach': require('./ProductDetach'),
		}
	};
</script>

<style type="text/css">

</style>