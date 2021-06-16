<template>

	<div>

		<alert componentName="contract-view"/>

		<template v-if="!loading">

			<contract-details :contract="contract" :key="'contract_actions'+counter" :updateData="getValues">
				
			</contract-details>

			<contract-associates :contract="contract" :key="'contract_associates'+counter">
					 
			</contract-associates>

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

		name : 'contract-view',

		description : 'Contract view page',

		data(){

			return {

				contract : '',

				contract_id : '',

				loading : true,

				counter : 0
			}
		},

		beforeMount(){

			this.getValues();
		},

		 created() {

			 window.eventHub.$on('updateContractAssociates',this.updateAssociates);
		},

		methods : {

			updateAssociates() {

				this.getValues(true);
			},

			getValues(refresh){

				const path = window.location.pathname

				this.contract_id = getIdFromUrl(path)

				axios.get('/service-desk/api/contract/'+this.contract_id).then(res=>{

					if(refresh) { this.counter += 1; };

					this.loading = false;

					this.contract = res.data.data.contract;

				}).catch(error=>{

					this.loading = false;

					errorHandler(error)
				});
			},
		},

		components : {

			'contract-details' : require('./View/ContractDetails'),

			'contract-associates' : require('./View/ContractAssociates'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>