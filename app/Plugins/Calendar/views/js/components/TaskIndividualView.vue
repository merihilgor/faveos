<template>

	<div>

		<alert componentName="tasks-view"/>

		<template v-if="!loading">
			<task-details :task="task" :key="taskId" />
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

		data(){

			return {

				task : '',

				taskId : '',

                loading : true,

                assigneeIds: [],

                assigneeNames: []
                
			}
		},

		beforeMount(){

			this.getValues();
		},

		methods : {

			getValues(){

				const path = window.location.pathname

				this.taskId = getIdFromUrl(path)
				
				axios.get('/tasks/get-task-by-id/'+this.taskId).then(res=>{

					this.loading = false;

                    this.task = res.data.data;
                    
				}).catch(error=>{

					this.loading = false;
                });

			},
		},

		components : {

			'task-details' : require('./TaskDetails'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			"alert": require("components/MiniComponent/Alert"),

		}
	};
</script>
