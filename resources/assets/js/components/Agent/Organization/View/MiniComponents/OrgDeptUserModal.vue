<template>

	<div id="org-dept-user-list"> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">
			
				<h4>{{lang('list_of_users')}}</h4>
			</div>

			<div slot="fields" id="static_select">

				<div class="height-limit">
					
					<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="org-dept-user-list"
					componentTitle="OrgDeptUserList">
			
					</data-table>	
				</div>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import { mapGetters } from "vuex"

	import axios from "axios"

	export default {
		
		name : 'org-dept-user-modal',

		description : 'Organization department user modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

			orgId : { type : Number | String, default  : ''},

			deptId : { type : Number | String, default  : ''}
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle:{ width:'950px' },

				loading:false,

				size: 60,

				apiUrl : '/org-dept-user-list/'+this.orgId+'/'+this.deptId,

				columns: ['name', 'email'],
				
				options: {},
			}
		},

		beforeMount(){

			const self = this;
	
			this.options = {

				texts : { 'filter' : '', 'limit': ''},

				headings: { name: 'Name', email: 'Email' },

				templates: {

					name(createElement,row){
						
						return createElement('a', {
								
							attrs: {
									
								href: self.basePath()+'/user/' + row.id,
							}
						}, row.full_name ? row.full_name : row.user_name);
					}
				},

				sortable:  ['name', 'email'],

				filterable:  ['name', 'email'],

				pagination:{chunk:5,nav: 'scroll'},

				requestAdapter(data) {

					return {
						
						'sort-by': data.orderBy ? data.orderBy : 'id',
						
						'order': data.ascending ? 'desc' : 'asc',
						
						'search-query':data.query.trim(),
						
						page:data.page,
						
						limit:data.limit,
					}
				},

				responseAdapter({data}) {
					
					return {
						
						data: data.message.data.map(data => {

						data.detach = true;

						return data;
					}),
					
						count: data.message.total
					}
				},
			}
		},
		
		components:{

			'modal':require('components/Common/Modal.vue'),
				
			'alert' : require('components/MiniComponent/Alert'),
				
			'custom-loader' : require('components/MiniComponent/Loader'),
				
			'data-table' : require('components/Extra/DataTable'),
		}
	};
</script>

<style type="text/css">
.has-feedback .form-control {
	padding-right: 0px !important;
}
#H5{
	margin-left:16px; 
}
.margin {
	margin-right: 16px !important;margin-left: 0px !important;
}

#alert_msg {
	margin: 0px 5px 30px 5px;
}
#static_select{
			margin-left: 15px;
		margin-right: 15px;
}
#alert_top{
	margin-top:20px
}
.height-limit{
	overflow-y: auto;
		max-height: 300px;
		overflow-x: hidden;
}
</style>