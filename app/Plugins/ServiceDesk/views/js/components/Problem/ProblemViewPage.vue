<template>

	<div>

		<alert componentName="problem-view"/>

		<template v-if="!loading">

			<problem-details :problem="problem" :key="problem_id" :updateData="refreshData"></problem-details>

			<problem-associates :problemId="problem_id" :problem="problem" :key="'problem_associates'+counter">
                
            </problem-associates>

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

		name : 'problem-view',

		description : 'Problem view page',

		data(){

			return {

				problem : '',

				problem_id : '',

				loading : true,

                counter : 0
			}
		},

		beforeMount(){

			this.getValues();
		},

        created() {

            window.eventHub.$on('updateProblemAssociates',this.updateAssociates);
        },

		methods : {

            refreshData() {

                this.getValues()
            },

            updateAssociates() {

                this.counter += 1;
            },

			getValues(){

				const path = window.location.pathname

				this.problem_id = getIdFromUrl(path)

				axios.get('/service-desk/api/problem/'+this.problem_id).then(res=>{

					this.loading = false;

					this.problem = res.data.data;

				}).catch(error=>{

					this.loading = false;
				});
			},
		},

		components : {

			'problem-details' : require('./View/ProblemDetail'),

			'problem-associates' : require('./View/ProblemAssociate'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style>
	
	.tooltip {
        display: block !important;
        z-index: 10000;
        font-weight:bold !important;
        max-width:1000px;
        word-wrap: break-word;
    }

    .tooltip .tooltip-inner {
        background: black;
        color: white;
        padding: 5px 10px 4px;
    }

    .tooltip .tooltip-arrow {
        width: 0;
        height: 0;
        border-style: solid;
        position: absolute;
        margin: 5px;
        border-color: black;
    }

    .tooltip[x-placement^="top"] {
        margin-bottom: 5px;
    }

    .tooltip[x-placement^="top"] .tooltip-arrow {
        border-width: 5px 5px 0 5px;
        border-left-color: transparent !important;
        border-right-color: transparent !important;
        border-bottom-color: transparent !important;
        bottom: -5px;
        left: calc(50% - 5px);
        margin-top: 0;
        margin-bottom: 0;
    }

    .tooltip[x-placement^="bottom"] {
        margin-top: 5px;
    }

    .tooltip[x-placement^="bottom"] .tooltip-arrow {
        border-width: 0 5px 5px 5px;
        border-left-color: transparent !important;
        border-right-color: transparent !important;
        border-top-color: transparent !important;
        top: -5px;
        left: calc(50% - 5px);
        margin-top: 0;
        margin-bottom: 0;
    }

    .tooltip[x-placement^="right"] {
        margin-left: 5px;
    }

    .tooltip[x-placement^="right"] .tooltip-arrow {
        border-width: 5px 5px 5px 0;
        border-left-color: transparent !important;
        border-top-color: transparent !important;
        border-bottom-color: transparent !important;
        left: -5px;
        top: calc(50% - 5px);
        margin-left: 0;
        margin-right: 0;
    }

    .tooltip[x-placement^="left"] {
        margin-right: 5px;
    }

    .tooltip[x-placement^="left"] .tooltip-arrow {
        border-width: 5px 0 5px 5px;
        border-top-color: transparent !important;
        border-right-color: transparent !important;
        border-bottom-color: transparent !important;
        right: -5px;
        top: calc(50% - 5px);
        margin-left: 0;
        margin-right: 0;
    }

    .tooltip[aria-hidden='true'] {
        visibility: hidden;
        opacity: 0;
        transition: opacity .15s, visibility .15s;
    }

    .tooltip[aria-hidden='false'] {
        visibility: visible;
        opacity: 1;
        transition: opacity .15s;
    }
</style>