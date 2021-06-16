<template>

	<div class="btn-group block">

		<a v-if="data.edit_modal" class="btn btn-primary btn-sm" @click="showEditModal = true" href="javascript:;">

			<i class="fa fa-edit" title="Edit"></i>
		</a>

		<a v-if="data.delete_url" class="btn btn-danger btn-sm" @click="showModal = true" href="javascript:;">

			<i class="fa fa-trash" title="Delete"></i>
		</a>

		<transition name="modal">

		 <delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="data.delete_url" 
		 	:alertComponentName="alert" :componentTitle="data.componentName">
		 		
		 	</delete-modal>
		</transition>

		<transition name="modal">

			<template v-if="data.componentName === 'TaskList'">
				
				<task-list-modal v-if="showEditModal" title="tasklist_edit" :onClose="onClose" :showModal="showEditModal" :data="data"
			 	  componentTitle="TaskList">
			 	
			 	</task-list-modal>
			</template>

			<template v-if="data.componentName === 'TaskProjects'">
				
				<task-modal v-if="showEditModal" title="edit_project" :onClose="onClose" :showModal="showEditModal" :data="data"
			 		:containerStyle="style" componentTitle="TaskProjects">
			 	
			 	</task-modal>
			</template>
		</transition> 
	</div>

</template>

<script>

	import axios from 'axios';

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

				alert : '',

				style : { width : '500px'},

				styleObj : { width : '700px'}
			}
		},

		created() {

			this.alert = this.data.alertComponentName ? this.data.alertComponentName : 'dataTableModal'; 
		},

		methods:{

			onClose(){
		  
		    this.showModal = false;
		  
		    this.showEditModal = false;
		  
		    this.$store.dispatch('unsetValidationError');
		  },
		},

		components:{
		
			'delete-modal': require('./TaskDeleteModal'),
		
			'task-modal': require('./TaskModal'),

			'task-list-modal': require('./TaskListModal')
		}
	};
</script>

<style>
	.block a{
		margin: 2px !important;
	}
</style>

