<template>

	<div class="btn-group block">

		<a v-if="data.edit_url" class="btn btn-primary btn-sm" id="edit-button" :href="data.edit_url" v-tooltip="trans('edit')"
			:target="data.tableName ? '_blank' : '' ">

			<span>

				<i class="fa fa-edit"></i>
			</span>
		</a>

		<a v-if="data.settings_url" class="btn btn-primary btn-sm" id="edit-button" :href="data.settings_url" target="_blank" rel="noopener noreferrer" v-tooltip="trans('settings')">

			<span id="edit_btn">

				<i class="fa fa-cogs"></i>
			</span>
		</a>

		<a v-if="data.edit_modal" class="btn btn-primary btn-sm" id="edit-modal-button"
			@click="showEditModalMethod" href="javascript:;" v-tooltip="disabled ? trans('default_field_is_not_editable') : trans('edit')" :disabled="disabled">

			<span id="edit_modal_btn">

				<i class="fa fa-edit"></i>
			</span>
		</a>

		<a v-if="data.download_url" class="btn btn-primary btn-sm" id="download-button" :href="data.download_url"
			v-tooltip="trans('download')">

			<span id="download_btn">

				<i class="fa fa-download"></i>
			</span>
		</a>

		<a v-if="data.view_url" id="view-button" class="btn btn-primary btn-sm" :href="data.view_url"
			:target="data.tableName ? '_blank' : '' " v-tooltip="trans('view')">
	
			<span id="view_btn">

				<i class="fa fa-eye"></i>
			</span>
		</a>

		<a v-if="data.delete_url" id="delete-button" class="btn btn-danger btn-sm" @click="showModalMethod"
			href="javascript:;" :disabled="disabled" v-tooltip="disabled ? trans('default_field_is_not_deletable') : trans('delete')">

			<span id="delete_btn" :disabled="disabled">

				<i class="fa fa-trash"></i>
			</span>
		</a>

		<transition name="modal">

		 <delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="data.delete_url" :alertComponentName="alert" ></delete-modal>
		</transition>

		<transition name="modal">

		 	<data-table-modal v-if="showEditModal" title="edit" :onClose="onClose" :showModal="showEditModal"
		 		:data="data" :apiUrl="data.edit_modal">
		 	</data-table-modal>
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
				location: this.data.delete_url,
				alert : '',
			}
		},

		computed : {

			disabled() {

				return boolean(this.data.is_default)
			}
		},

		created() {
			
			this.updateAlert()
		},

		methods:{

			updateAlert() {

				this.alert = this.data.alertComponentName ? this.data.alertComponentName : 'dataTableModal'; 
			},

			showModalMethod(){
				if(this.data.is_default){
					this.showModal = false
				} else {
					this.showModal = true
				}
			},
			showEditModalMethod(){
				if(this.data.is_default){
					this.showEditModal = false
				} else {
					this.showEditModal = true
				}
			},
			onClose(){
		    this.showModal = false;
		    this.showEditModal = false;
		    this.$store.dispatch('unsetValidationError');
		  },
		},
		components:{
			'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),
			'data-table-modal': require('components/MiniComponent/DataTableComponents/DataTableModal')
		}
	};
</script>

<style type="text/css">
	
	.block{
		display: block !important;
	}
	/*#edit_btn,#edit_modal_btn {
		border: 1px solid #f2ebeb !important;
	}
	#edit-button,#edit-modal-button {
		color: black !important;
	}*/
	/*#edit_btn,.edit_icon { color: white !important; border: 0px solid !important; }*/
	
	#edit-button , #view-button, #edit-modal-button, #download-button, #delete-button{
		margin: 2px !important;
	}
</style>

