<template>

	<div>

		<alert componentName="release-view"/>

		<template v-if="!loading">

			<release-details :release="release" :updateData="refreshData"></release-details>

			<release-associates :releaseId="release_id" :release="release" :key="'release_associates'+counter">
					 
			</release-associates>

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

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import axios from 'axios';

	export default {

		name : 'release-view',

		description : 'Release view page',

		data(){

			return {

				release : '',

				release_id : '',

				loading : true,

				counter : 0
			}
		},

		beforeMount(){

			this.getValues();
		},

		  created() {

				window.eventHub.$on('updateReleaseAssociates',this.updateAssociates);
		  },

		methods : {

			refreshData() {

				this.getValues()
			},

			updateAssociates() {

				this.getValues(true);
			},

			getValues(value){

				const path = window.location.pathname

				this.release_id = getIdFromUrl(path)

				axios.get('/service-desk/api/release/'+this.release_id).then(res=>{

					this.loading = false;

					this.release = res.data.data.release;

					if(value) { this.counter += 1; }

				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'release-view');
				});
			},
		},

		components : {

			'release-details' : require('./View/ReleaseDetails'),

			'release-associates' : require('./View/ReleaseAssociates'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>