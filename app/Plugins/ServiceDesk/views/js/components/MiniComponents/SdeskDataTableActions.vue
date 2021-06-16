<template>

	<div class="btn-group">

		<a v-if="data.detach" id="detach-button" @click="()=>showModal = true" href="javascript:;">
			<span title="Detach" class="btn btn-primary" id="detach_btn" :disabled="disabled"><i class="fa fa-unlink"></i> </span>
		</a>

		<transition  name="modal">
			<detach-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :assetId="data.id"
				:alert="alertName">
				
			</detach-modal>
		</transition>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from 'helpers/extraLogics'

	export default {
		
		name:"sdesk-data-table-action",

		description: "Contains detach button which can be used as a component as whole. It is built basically for displaying deatch button in a datable.",		
		
		props: {
			
			/**
			 * component will display buttons based on the indexes present in the `data` object
			 * For eg : if you want this component to display delete button, `data` should have 
			 * an index `delete` which should be the API endpoint which can be used to delete 
			 * the data.
			 * @type {Object}
			 */
			data : { type : Object, required : true },

		
		},

		data(){
			return{
				showModal : false,
				disabled : ''
			}
		},

		created(){

			this.alertName = this.data.alertName ? this.data.alertName : '';

			this.disabled = boolean(this.data.is_default)
		},

		watch : {
			data(newValue,oldValue){
				this.disabled = boolean(newValue.is_default) 
				return newValue
			}
		},
		
		methods:{
			onClose(){
		        this.showModal = false;
		        this.$store.dispatch('unsetValidationError');
		    },
		},

		components:{
			'detach-modal': require('./DetachModal'),
		}
	};

</script>

<style type="text/css">
	#edit_btn,#edit_modal_btn {
		border: 1px solid #f2ebeb !important;
	}
</style>