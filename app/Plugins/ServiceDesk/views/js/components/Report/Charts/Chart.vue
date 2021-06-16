<template>
	
	<div class="container">
		
		<div class="row">
			
			<div v-if="type === 'bar'" class="Chart">
				
				<h1 style="text-align:center;" id="type">Barchart</h1>
				
				<bar-chart :data="chartData" :colors="colors"/>
			</div>

			<div v-if="type === 'pie'" class="Chart">
				
				<h1 style="text-align:center;" id="type">Piechart</h1>
				
				<pie-chart :data="chartData" :colors="colors"/>
			</div>

			<div v-if="type === 'horizontal'" class="Chart">
				
				<h1 style="text-align:center;" id="type">Horizontalchart</h1>
				
				<horizontal-chart :data="chartData" :colors="colors"/>
			</div>

			<div v-if="type === 'doughnut'" class="Chart">
				
				<h1 style="text-align:center;" id="type">Doughnutchart</h1>
				
				<doughnut-chart :data="chartData" :colors="colors"/>
			</div>
		</div>
	</div>
</template>

<script>

	import BarChart from './BarChart'
	
	import PieChart from './PieChart'

	import HorizontalChart from './HorizontalBar'
	
	import DoughnutChart from './DoughnutChart'
	
	export default {
	
		props : {
	
			chartData : { type : Array|String , default : ()=>[]},
			
			type : { type : String, default : 'pie'}
		},

		components: {
			
			'bar-chart' : BarChart,

			'pie-chart' : PieChart,

			'horizontal-chart' : HorizontalChart,
			
			'doughnut-chart' : DoughnutChart,
		},

		data () {
			
			return {
				
				colors : []
			}
		},

		beforeMount(){

			this.getRandomColor();
		},

		methods : {

			getRandomColor() {

	      while (this.colors.length < 100) {
	        
	        do {
	          
	          var color = Math.floor((Math.random()*1000000)+1);

	        } while (this.colors.indexOf(color) >= 0);
	        
	        this.colors.push("#" + ("000000" + color.toString(16)).slice(-6));
	      }
	    }
		}
	};
</script>

<style>
	
	.container {
		max-width: 800px;
		margin: 0 auto;
	}
	
	.Chart {
		/*padding: 20px;*/
		/*box-shadow: 0px 0px 20px 2px rgba(0, 0, 0, .4);*/
		/*border-radius: 20px;*/
		margin-bottom: 30px;
	}
</style>