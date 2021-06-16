<template>

	<div>

		<data-table :url="apiEndPoint" :dataColumns="columns"  :option="options" 
			scroll_to="release-associates" :componentTitle="componentTitle">

		</data-table>

		<transition  name="modal">

			<release-asset-detach v-if="showModal" :onClose="onClose" :showModal="showModal" :fieldId="fieldId" 
				:releaseId="releaseId" :compName="componentTitle" category="asset">

      	</release-asset-detach>
		</transition>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex'

	import Vue from 'vue'

	export default {

		name : 'release-associated-assets',

		description : 'Release associated assets table page',

		props : {

			actions : { type : String | Object, default : '' },

			category : { type:String , default : ''},

			apiUrl : { type : String, default : ''},

			releaseId : { type : String | Number, default : ''},

			componentTitle : { type : String, default : ''},
		},

		data() {

			return {

				columns: this.actions.release_asset_detach ?  ['name', 'managed_by', 'used_by', 'contract','action'] : ['name', 'managed_by', 'used_by', 'contract'] ,

				options : {},

				apiEndPoint : this.apiUrl,

				showModal : false,

				fieldId : '',
			}
		},

		beforeMount(){

			const self = this;

			this.options = {

				sortIcon: {

					base : 'glyphicon',

					up: 'glyphicon-chevron-down',

					down: 'glyphicon-chevron-up'
				},

				headings: {

					name:'Name',

					managed_by: 'Managed by',

					used_by : 'Used by',

					action: 'Action',
				},

				columnsClasses : {

					name : 'associate-name',

					managed_by : 'associate-manage',

					used_by : 'associate-used',

					contract : 'associate-contract',

					action : 'associate-action',
				},

				texts : {

					filter : '',

					limit : ''
				},

				templates: {

					name: function(createElement, row) {

						 return createElement('a', {

							attrs: {

								href: self.basePath() + '/service-desk/'+ self.category + '/' + row.id + '/show',
							}
						}, row.name);
					},

					managed_by(h,row){  return row.managed_by ? row.managed_by.full_name : '--'; },

					used_by(h,row){  return row.used_by ? row.used_by.full_name : '--'; },

					contract: function(createElement, row) {
						
						if(row.contract){

							return createElement('a', {
								attrs:{

									href : self.basePath()+'/service-desk/contracts/'+row.contract.id+'/show',
									target : '_blank'
								},
							}, row.contract.name);
						} else {
							return '--'
						}
					},

				action: function(createElement, row) {
				  
					let span = createElement('i', {  attrs:{ 'class' : 'fa fa-unlink' } });
				
					return createElement('a', {

						attrs: { href: 'javascript:;', class : 'btn btn-primary' },

						on: {
							
							click: function() {
						  
								self.showModal = true;

								self.fieldId = row.id
							}
						}
					}, [span]);
				},
				},

				sortable:  ['name', 'managed_by', 'used_by'],

				sortable:  ['name', 'managed_by', 'used_by'],

				pagination:{chunk:5,nav: 'scroll'},

				requestAdapter(data) {

					return {

						'sort-field': data.orderBy ? data.orderBy : 'name',

						'sort-order': data.ascending ? 'desc' : 'asc',

						'search-query':data.query,

						page:data.page,

						limit:data.limit,
					}
				},

				responseAdapter({data}) {

					return {

						data: data.data.assets.map(data => {

						data.detach = true;

						data.release_id = self.releaseId;

						data.compName = self.componentTitle;

						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		methods : { 

			onClose(){
		       
		      this.showModal = false;
		    },
		},

		components : {

			'data-table': require('components/Extra/DataTable'),
			
			'release-asset-detach': require('./Child/ReleaseDetach'),
		}
	};
</script>

<style type="text/css">
	
	.table-bordered { font-size: 14px !important; }
	
	.associate-name{
		width: 23%;
		word-break: break-all;
	}

	.associate-manage{
		width: 22%;
		word-break: break-all;
	}

	.associate-use{
		width: 22%;
		word-break: break-all;
	}

	.associate-contract{
		width: 23%;
		word-break: break-all;
	}

	.associate-action{
		width: 10%;
		word-break: break-all;
	}
</style>