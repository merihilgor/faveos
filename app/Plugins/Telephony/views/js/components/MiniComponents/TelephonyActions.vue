<template>

	<div class="btn-group block">

		<a class="btn btn-primary btn-sm" id="edit-button" @click="showEditModal = true" href="javascript:;">

			<span title="Edit" id="edit_btn">

				<i class="fa fa-edit"></i>
			</span>
		</a>

		<a class="btn btn-primary btn-sm" id="settings-modal-button" @click="showSettingsModal = true" 
			href="javascript:;">

			<span :title="lang('get_webhook_url')" id="settings_modal_btn">

				<i class="fa fa-link"></i>
			</span>
		</a>

		<transition name="modal">

		 	<telephony-settings-modal v-if="showSettingsModal" :onClose="onClose" :showModal="showSettingsModal" 
		 		:data="data">

		 	</telephony-settings-modal>
		</transition>

		<transition name="modal">

		 	<telephony-edit-modal v-if="showEditModal" :onClose="onClose" :showModal="showEditModal" 
		 		:data="data">

		 	</telephony-edit-modal>
		</transition>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import {lang} from 'helpers/extraLogics';
	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"data-table-actions",

		description: "Contains edit, delete and view buttons as group which can be used as a component as whole. It is built basically for displaying edit, delete and view button in a datable.",

		props: {

			data : { type : Object, required : true },
		},

		data(){

			return{

				showSettingsModal : false,

				showEditModal : false
			}
		},

		methods:{

			onClose(){

		    this.showSettingsModal = false;

		    this.showEditModal = false;

		    this.$store.dispatch('unsetValidationError');
		  },
		},

		components:{

			'telephony-settings-modal' : require('./TelephonySettingsModal'),

			'telephony-edit-modal' : require('./TelephonyEditModal')
		}
	};

</script>

<style type="text/css">

	.block{
		display: block !important;
	}

	#edit-button , #settings-modal-button{
		margin: 2px !important;
	}
</style>
