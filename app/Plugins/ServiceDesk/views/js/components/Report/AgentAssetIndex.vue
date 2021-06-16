<template>
	
	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="asset-table">

			<div class="box-body agent-asset-table">

				<div v-if="from != 'view'" class="dropdown" id="table-columns">

					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">

						<i class="fa fa-bars"> </i> {{lang('columns')}}
							
						<span class="caret"></span>
					</button>
						
					<ul class="dropdown-menu" v-model="columns" id="cols" style="right:0;left: unset;">
							
						<template v-for="(col,i) in columns">
								
							<li v-if="col.key != 'id' && col.key != 'description'">
								<a>
									<label class="label_align">

										<input class="checkbox_align" type="checkbox" @click="columnToggle(i)" v-model="col.display">
										{{ lang(col.value) }}
									</label>
								</a>
							</li>
						</template>
					</ul>
				</div>

				<data-table id="asset-table" :url="ApiUrl" :dataColumns="columnsValues"  :option="options" 
					scroll_to ="asset-list">
					
				</data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import { mapGetters } from 'vuex'

	import { lang } from 'helpers/extraLogics';

	import  { getIdFromUrl } from 'helpers/extraLogics';

	export default {

		name : 'agent-asset-list',

		description : 'Agent asset lists table component',

		props: { 

			from : { type : String, default  :''},

			ApiUrl : { type : String, default : '/service-desk/api/asset-list?config=true&'} 
		},


		data(){

			return {
				columns: [],

				options: {},

				apiUrl:this.ApiUrl,
			}
		},

		watch : {

			ApiUrl(oldValue,newValue) {
				return newValue
			}
		},

		computed:{

			columnsValues(){
				
				this.$emit('columns-list', this.columns.filter(a => a.display).map(a => a.key));

				return this.columns.filter(a => a.display).map(a => a.key);
			},

			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount() {

			this.getColumns();

			const self = this;

			this.options = {

				headings: { name: 'Name', used_by : 'Used by', managed_by : 'Managed by', action:'Action'},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				templates: {


					assigned_on(h, row) {

						return self.formattedTime(row.assigned_on);
					},

					created_at(h, row) {

						return self.formattedTime(row.created_at);
					},

					updated_at(h, row) {

						return self.formattedTime(row.updated_at);
					},


					managed_by: function(createElement, row) {
						
						if(row.managed_by){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/user/'+row.managed_by.id,
										
										target : '_blank'
									},
							}, row.managed_by.full_name ? row.managed_by.full_name : row.managed_by.email);
						} else { 

							return '--'
						}
					},

					used_by: function(createElement, row) {
						
						if(row.used_by){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/user/'+row.used_by.id,
										
										target : '_blank'
									},
							}, row.used_by.full_name ? row.used_by.full_name : row.used_by.email);
						} else { 

							return '--'
						}
					},

					attachment(createElement, row) {
						
						if(row.attachment){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/uploads/service-desk/attachments/'+row.attachment.value,
										
										target : '_blank'
									},
							}, lang('click_to_see_attachment'));
						} else { 

							return '--'
						}
					},

					name(createElement, row) {
						
						return createElement('a', {
								
							attrs:{
										
								href : self.basePath() + '/service-desk/assets/'+row.id+'/show',
										
									target : '_blank'
								},
							}, row.name);
					},

					asset_type(h, row){

						return row.asset_type ? row.asset_type.name : '--'
					},

					department(createElement, row){

						if(row.department){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/department/'+row.department.id,
										
										target : '_blank'
									},
							}, row.department.name);
						} else { 

							return '--'
						}
					},

					impact_type(h, row){

						return row.impact_type ? row.impact_type.name : '--'
					},

					location(h, row){

						return row.location ? row.location.title : '--'
					},

					organization(createElement, row){

						if(row.organization){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/organizations/'+row.organization.id,
										
										target : '_blank'
									},
							}, row.organization.name);
						} else { 

							return '--'
						}
					},

					product(createElement, row){

						if(row.product){

							return createElement('a', {
								
								attrs:{
										
										href : self.basePath() + '/service-desk/products/'+row.product.id+'/show',
										
										target : '_blank'
									},
							}, row.product.name);
						} else { 

							return '--'
						}
					},
				},

				sortable:  ['asset_id', 'name', 'identifier', 'assigned_on','created_at','updated_at'],
				
				filterable:  ['name', 'identifier', 'assigned_on','created_at','updated_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
				
					return {
						
						'sort-field' : data.orderBy ? data.orderBy : 'id',
						
						'sort-order' : data.ascending ? 'desc' : 'asc',
						
						'search-query' : data.query.trim(),
						
						page : data.page,
						
						limit : data.limit,
					}
				},

				responseAdapter({data}) {

					self.$emit('asset-report-data', data.data.assets);

					return {
						data: data.data.assets,

						count: data.data.total
					}
				},
			}
		},

		methods:{
			
			columnToggle(i){
			
				this.columns[i].display = !this.columns[i].display;
			},

			getColumns(){

				const path = window.location.pathname

				const filterId = getIdFromUrl(path)

				const apiEndpoint = filterId ? '/service-desk/api/asset-list?column=true&filter_id='+filterId : 
																			'/service-desk/api/asset-list?column=true'
				axios.get(apiEndpoint).then(res=>{

					this.columns = res.data.data.asset_columns;

				}).catch(error=>{

					this.columns = [];
				})
			}
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),
			
			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style type="text/css">
	
	.label_align {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: 500 !important; padding-top: 6px;
	}
	.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}

	.agent-asset-table #asset-table .VueTables .table-responsive {
		overflow-x: auto;
	}

	.agent-asset-table #asset-table .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
	.asset-table {
		padding: 0px !important;
	}
	.add-asset{
		float: right;
	}
	.round-btn {
		margin-right: 5px;
	}
	#cols {
		overflow-y: auto !important;
		max-height: 200px !important;
	}
	#table-columns{

		float: right;
		margin-bottom: 5px;
	}
</style>
