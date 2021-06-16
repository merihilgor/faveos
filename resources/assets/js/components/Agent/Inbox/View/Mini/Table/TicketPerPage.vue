<template>
	
	<div class="dropdown pull-right" id="ticket-count">

		<button class="btn btn-sm btn-default" data-toggle="dropdown" :style="buttonStyle">{{perpage}}
			
			<span class="caret"></span>
		</button>
		
		<ul class="dropdown-menu">
			
			<li v-for="(count,index) in ticketCounts" :key="index">
				
				<a @click="changeCount(count)" href="javascript:;">{{count}}</a>
			</li>
		</ul>
	</div>
</template>
<script>

	export default{

		props : {

			tableHeader : { type : String, default : ''},

			perpage : { type : Number|String, default : ''}
		},

		data(){
			
			return{
				
				buttonStyle : { 'background-color':this.tableHeader + ' !important','color':'#fff' },
				
				ticketCounts:[10,25,50,100],
			}
		},

		methods:{

			changeCount(x){

				this.$emit('ticketCount', x);
			}
		}
	};
</script>

<style scoped>
	
	#ticket-count{

		margin: 2px;
	}
</style>