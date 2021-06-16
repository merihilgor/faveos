<template>

	<div>

		<alert componentName="product-view"/>

		<template v-if="!loading">

			<product-details :product="product" :key="product_id"></product-details>

			<product-associates :productId="product_id" :product="product"></product-associates>

		</template>

		<template v-if="loading">

			<div class="row">

				<loader :animation-duration="4000" :size="60"/>
			</div>
		</template>
	</div>
</template>

<script>

	import  { getIdFromUrl } from 'helpers/extraLogics';

	import axios from 'axios';

	export default {

		name : 'product-view',

		description : 'Product view page',

		data(){

			return {

				product : '',

				product_id : '',

				loading : true,
			}
		},

		created(){

			 window.eventHub.$on('updateProductData',this.getValues);
		},

		beforeMount(){

			this.getValues();
		},

		methods : {

			getValues(){

				const path = window.location.pathname

				this.product_id = getIdFromUrl(path)

				axios.get('/service-desk/api/product/'+this.product_id).then(res=>{

					this.loading = false;

					this.product = res.data.data.product;

				}).catch(error=>{

					this.loading = false;
				});
			},
		},

		components : {

			'product-details' : require('./View/ProductDetails'),

			'product-associates' : require('./View/ProductAssociates'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style scoped>

</style>