<template>
	
	<div class="dropdown pull-right" id="ticket-sort">

		<button class="btn btn-default btn-sm dropdown-toggle sortval" type="button" data-toggle="dropdown" 
			:style="buttonStyle">

			<i class="glyphicon glyphicon-sort"></i>&nbsp;
			
			<span class="caret"></span>
		</button>
		
		<ul class="dropdown-menu" style="z-index:9999;text-transform: initial;">
			
			<li class="dropdown-header">{{ lang('sorting Menu') }}</li>
			
			<li v-for="(sorting,index) in sortingMenu">

				<a href="javascript:;" @click="sort(sorting,index)">

					<i :class="sorting.class"></i>{{sorting.name}} in {{sorting.order_name}}
				</a>
			</li>
		</ul>
	</div>
</template>

<script>
	
	export default {
	
	props : {

		tableHeader : { type : String, default : ''},
	},

	data() {
		
		return {

			buttonStyle : { 'background-color':this.tableHeader + ' !important','color':'#fff', 'margin-top' : '2px' },

			sortingMenu: [
				{
					name: "Ticket Number",
					value: "ticket_number",
					order: "asc",
					class: "fa fa-sort-amount-asc",
					order_name: "Ascending"
				},
				{
					name: "Updated Date",
					value: "updated_at",
					order: "asc",
					class: "fa fa-sort-amount-asc",
					order_name: "Ascending"
				},
				{
					name: "Created Date",
					value: "created_at",
					order: "asc",
					class: "fa fa-sort-amount-asc",
					order_name: "Ascending"
				}
			],
		};
	},

	methods: {

		sort(x, y) {
		
			if (x.order == "asc") {
		
				this.$emit("sort", x);
		
				$(".sortval").html(
					x.name + " in " + x.order_name + '&nbsp<span class="caret"></span>'
				);
		
				this.sortingMenu[y].order = "desc";
		
				this.sortingMenu[y].class = "fa fa-sort-amount-desc";
		
				this.sortingMenu[y].order_name = "Descending";
			} else {
		
				this.$emit("sort", x);
		
				$(".sortval").html(
		
					x.name + " in " + x.order_name + '&nbsp<span class="caret"></span>'
				);
		
				this.sortingMenu[y].order = "asc";
		
				this.sortingMenu[y].class = "fa fa-sort-amount-asc";
		
				this.sortingMenu[y].order_name = "Ascending";
			}
		}
	}
};
</script>