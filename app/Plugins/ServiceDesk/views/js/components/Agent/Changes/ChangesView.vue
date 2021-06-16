<template>

	<div>

		<alert componentName="changes-view"/>

		<template v-if="!loading">

			<change-details :change="change" :key="change_id"></change-details>

			<change-associates :changeId="change_id" :change="change"></change-associates>

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

		name : 'changes-view',

		description : 'Changes view page',

		data(){

			return {

				change : '',

				change_id : '',

				loading : true,
			}
		},

		created(){

			 window.eventHub.$on('updateChangeData',this.getValues);
		},

		beforeMount(){

			this.getValues();
		},

		methods : {

			getValues(){

				const path = window.location.pathname

				this.change_id = getIdFromUrl(path)

				axios.get('/service-desk/api/change/'+this.change_id).then(res=>{

					this.loading = false;

					this.change = res.data.data.change;

				}).catch(error=>{

					this.loading = false;
				});
			},
		},

		components : {

			'change-details' : require('./View/ChangeDetails'),

			'change-associates' : require('./View/ChangeAssociates'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style scoped>

</style>