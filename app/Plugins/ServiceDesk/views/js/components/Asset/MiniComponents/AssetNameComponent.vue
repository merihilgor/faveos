<template>
		
		<a id="asset-name"  :href="url" target="_blank">
			{{data.name}}
		</a>

</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {
		
		name:"asset-name",

		description: "Asset name component.",		
		
		props: {
			
			/**
			 * component will display buttons based on the indexes present in the `data` object
			 * For eg : if you want this component to display delete button, `data` should have 
			 * an index `delete` which should be the API endpoint which can be used to delete 
			 * the data.
			 * @type {Object}
			 */
			data : { type : Object, required : true },

		
		},

		data(){
			return{
				url : window.axios.defaults.baseURL + '/service-desk/assets/'+this.data.id+'/show'
			}
		},
		
		methods:{

		},

	};

</script>

<style type="text/css">
	
</style>