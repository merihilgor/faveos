<template>

	<div class="btn-group block">

		<a class="btn btn-primary btn-sm" id="edit-modal-button" @click="showEditModal = true" href="javascript:;"
			v-tooltip="lang('edit')">

			<span id="edit_modal_btn">

				<i class="fa fa-edit"></i>
			</span>
		</a>

		<a class="btn btn-primary btn-sm" id="edit-modal-button" @click="showUserList = true" href="javascript:;"
			v-tooltip="lang('user_list_modal')">

			<span id="edit_modal_btn">

				<i class="fa fa-list"></i>
			</span>
		</a>

		<a id="delete-button" class="btn btn-danger btn-sm" @click="showModal = true" href="javascript:;"
			v-tooltip="lang('delete')">

			<span id="delete_btn">

				<i class="fa fa-trash"></i>
			</span>
		</a>

		<transition name="modal">

		 <delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="data.delete_url" 
		 componentTitle="OrgDepartments" alertComponentName="OrgDepartmentsdataTableModal">
		 	
		 </delete-modal>
		</transition>

		<transition name="modal">

		 	<org-dept-user v-if="showUserList" :onClose="onClose" :showModal="showUserList" :deptId="data.id"
		 	:orgId="data.orgId">

		 	</org-dept-user>
		</transition>

		<transition name="modal">

		 	<edit-org-dept v-if="showEditModal" :onClose="onClose" :showModal="showEditModal" :deptId="data.id"
		 	:orgId="data.orgId">
		 		
		 	</edit-org-dept>
		</transition>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"data-table-actions",

		description: "Contains edit, delete and view buttons as group which can be used as a component as whole. It is built basically for displaying edit, delete and view button in a datable.",

		props: {

			data : { type : Object, required : true },
		},

		data(){

			return{

				showModal : false,

				showEditModal : false,

				showUserList : false,

				disabled : ''
			}
		},

		created(){

			this.disabled = boolean(this.data.is_default)
		},

		methods:{

			onClose(){

		    this.showModal = false;

		    this.showEditModal = false;

		    this.showUserList = false;

		    this.$store.dispatch('unsetValidationError');
		  },
		},

		components:{

			'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),

			'edit-org-dept' : require('./OrgDeptModal.vue'),

			'org-dept-user' : require('./OrgDeptUserModal.vue'),
		}
	};

</script>

<style type="text/css">

	.block{
		display: block !important;
	}

	#edit-button , #view-button, #edit-modal-button, #download-button, #delete-button{
		margin: 2px !important;
	}
</style>