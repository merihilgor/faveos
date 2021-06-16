<template>

	<div class="btn-group">

		<a v-if="data.detach" id="detach-button" @click="()=>showModal = true" href="javascript:;">

      <span title="Detach" class="btn btn-primary btn-sm" id="detach_btn">
        <i class="fa fa-unlink"></i> 
      </span>
		</a>

		<transition  name="modal">
			<change-asset-detach v-if="showModal" :onClose="onClose" :showModal="showModal" :assetId="data.id" :changeId="data.change_id" :compName="data.compName">

      </change-asset-detach>
		</transition>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"change-associates-actions",

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
			}
		},

		methods:{
			onClose(){
		        this.showModal = false;
		        this.$store.dispatch('unsetValidationError');
		    },
		},

		components:{
			'change-asset-detach': require('./ChangeDetach'),
		}
	};

</script>

<style type="text/css">

</style>