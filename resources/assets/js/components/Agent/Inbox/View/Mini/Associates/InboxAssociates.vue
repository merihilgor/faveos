<template>

		<div>
			
			<div id="timeline-tab">{{timelineTabVisible()}}</div>	

			<div id="timeline-display-box-tasks">{{ timelineBoxVisibleTasks() }}</div>

		</div>
</template>

<script>
	
	import { mapGetters } from 'vuex';

	export default {

		name : 'inbox-associates',

		description : 'Inbox Assciates Component',

		props : {

			ticketId : { type : String | Number, default : '' }
		},

		computed : {

			...mapGetters(['getTicketActions'])
		},

		methods : {

			timelineTabVisible() {

    			if(this.getTicketActions){

					window.eventHub.$emit('ticket-tab-mounted',this.getTicketActions)
				}
    		},

    		timelineBoxVisibleTasks() {
				
				window.eventHub.$emit('ticket-timeline-mounted-tasks',this.ticketId);
			},
		}
	};
</script>