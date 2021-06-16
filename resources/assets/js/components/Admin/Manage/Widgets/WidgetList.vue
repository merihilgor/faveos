
<template>
<div id="app-admin">

<alert componentName="dataTableModal"/>



	<div class="box box-primary">
		
			<div class="box-header with-border">
			
				<div class="row">
					
					<div class="col-md-3">
						
            
						<h3 class="box-title ">{{lang('widget-settings')}}</h3>
						
					</div>
					
				</div>
				<div class="box-body">		
					<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="widget-list"></data-table>
				</div>
				</div>
	</div>
</div>
</template>

<script type="text/javascript">

    import Vue from 'vue'

	import { mapGetters } from 'vuex'

	Vue.component('widget-table-actions', require('./WidgetTableActions.vue'));


	export default
	 {
		name : 'widget-list',
		description : 'Widget list table component',
		
 
		components:{"data-table" :require("components/Extra/DataTable") 
		
		},

		data: () => {
      return {
		  apiUrl : 'api/list-widgets/footer',

				columns: ['name','title','action'],

				options : {},
 
      }
	},
	 
	computed:{
    
      ...mapGetters(['formattedTime','formattedDate'])
    },
		
		beforeMount(){

			const self = this;
			
			this.options = {

				headings: {

					name: 'Name',

                    title: 'Title',

					action:'Actions'

				
				},
				
				columnsClasses : {

					name: 'widget-name',

					title: 'widget-title',

					action: 'widget-action',
				},
					texts: { filter: '', limit: '' },

				

			
				templates: {

					
					action : 'widget-table-actions',
					},

					sortable: ['name','title'],

				filterable: ['name', 'title'],


				requestAdapter(data) {
					return {
						'sort-field': data.orderBy ? data.orderBy : 'name',
						'sort-order': data.ascending ? 'asc' : 'desc',
						'search-query': data.query.trim(),
						page: data.page,
						limit: data.limit,
					}
				},
				responseAdapter({data}) {
					
					return {
						
						data: data.data.widgets.map(data => {
							data.edit_url =self.basePath()+ '/WidgetTableaction/' + data.id;
							
							data.view_url = self.basePath()+'/widgets/footer/' + data.id ;

							
							return data;
						}),
						count: data.data.total
					}
				},

					components : { 

			"data-table" : require("components/Extra/DataTable"),

			"alert": require("components/MiniComponent/Alert"),
		},
			};
		},
			};

</script>

<style type="text/css" scoped>
.widget-name{
		width:30%;
		word-break: break-word;
	}
	.widget-title{
		width:30%;
		word-break: break-word;
	}

	.widget-value{
		white-space: nowrap;
	}
		.widget-action{
		white-space: nowrap;
	}
</style>