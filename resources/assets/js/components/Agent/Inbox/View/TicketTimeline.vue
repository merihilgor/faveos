<template>
	
	<div id="tickets-index">

		<alert componentName="timeline"></alert>

			<faveo-box :title="lang('ticket-details')">

				<div v-if="hasDataPopulated" class="box-tools pull-right" slot="headerMenu">  
					
					<button type="button" class="btn btn-box-tool" @click="refreshDetails" v-tooltip="lang('click_to_refresh')">

						<i class="fa fa-refresh"></i>
					</button>

					<template v-if="from != 'timeline'">
						
						<button type="button" class="btn btn-box-tool" @click="backToInbox" v-tooltip="lang('back_to_inbox')">

							<i class="fa fa-arrow-up"></i>
						</button>

						<button type="button" class="btn btn-box-tool" @click="closeTimeline" v-tooltip="lang('close')">
					
							<i class="fa fa-times"></i>
						</button>	
					</template>
				</div>
				
				<template v-if="hasDataPopulated && !refresh">

					<div class="row">

						<div class="col-md-8">

							<span>
								<h3 class="box-title timeline_title"> 
									<i class="fa fa-ticket"> </i> [#{{ticketData.ticket_number}}]&rlm;{{ticketData.title}}
								</h3>
							</span>
							<span style="margin: 0.5rem">								
								<template v-if="ticketData.is_overdue || ticketData.due_today">
									
									<span v-if="ticketData.is_overdue" v-tooltip="lang('this_ticket_is_marked_as_overdue')" 
										class="label label-danger timeline_label">{{lang('overdue')}}
									</span>

									<span v-if="ticketData.due_today" class="label label-warning timeline_label"  
										v-tooltip="lang('this_ticket_is_marked_as_duetoday')"> 
										{{ lang('due_today') }}
									</span>&nbsp;
								</template>

								<template v-for="(label,index) in ticketData.labels">
												
									<a  target="_blank" :href="label.href" v-tooltip="lang(label.name)" class="label timeline_label"
										:style="{'backgroundColor' : label.color, 'color': '#FFF'}">{{subString(label.name)}}
									</a>&nbsp;
								</template>

								<template v-for="(tag,index) in ticketData.tags">			
									<a target="_blank" :href="tag.href" v-tooltip="lang(tag.name)" class="label" id="ticket_tags">
										<i class="fa fa-tag"></i> {{subString(tag.name)}}
									</a>&nbsp;
								</template>

							</span>
						</div>

						<div class="col-md-4" id="ratings_div">

							<rating-component v-if="system_ratings && !refresh" url="rating/" v-for="rating in system_ratings" 
								:key="rating.id" :rating="rating" :ticket_id="rating.ticket_id">
									
							</rating-component>
						</div>
					</div>
					  
				  <timeline-actions :id="ticketId" :data="ticketData" :afterAction="updateData" :key="'actions'+counter">
				  	
				  </timeline-actions>

				  <timeline-details :id="ticketId" :data="ticketData" :key="'details'+counter"></timeline-details>
				</template>

				<div v-if="loading">
					<VclTable :rows="10" :columns="4"></VclTable >
				</div>

				<div class="row" v-if="refresh">
					
					<custom-loader :duration="4000"></custom-loader>
				</div>
			</faveo-box>

			<inbox-associates v-if="hasDataPopulated" :ticketId="ticketId"></inbox-associates>
			
			<alert componentName="inboxActions"></alert>

			<inbox-actions v-if="hasDataPopulated" :ticketId="ticketId" :key="'action'+counter" :ticket="ticketData"
				:refreshThreads="updateThreads">
					
			</inbox-actions>

			<inbox-threads v-if="hasDataPopulated" :ticketId="ticketId" ref="threadsList"></inbox-threads>
		</div>
	</div>
</template>

<script>
	
	import { mapGetters } from 'vuex';

	import { getSubStringValue } from 'helpers/extraLogics'

	import { VclTable   } from 'vue-content-loading';

	import axios from 'axios';

	export default {

		props : {

			ticketId : { type : String | Number, default  :'' },

			hideTimeline : { type : Function },

			updateTable : { type : Function, default :()=>{} },

			from : { type : String, default : '' }
		},

		data() {

			return {

				ticketData : '',

				loading : true,

				hasDataPopulated : false,

				system_ratings:[],

				ticket_ratings:[],

				counter : 0,

				increment : 0,

				resetCounter : false,

				refresh : false,
			}
		},

		beforeMount() {

			this.getTicketData()
		},

		watch : {

			ticketId(newValue,oldValue){

				this.loading = true;

				this.hasDataPopulated = false;

				this.getTicketData();
			}
		},

		computed:{

			...mapGetters(['formattedTime','getRatingTypes'])
		},

		created(){

			window.eventHub.$on('actionDone',this.updateData);
	      
	      window.eventHub.$on('refreshActions',this.updateActions);

	      window.eventHub.$on('refreshTableAndData',this.updateTableAndData);

		},

		methods : {

			refreshDetails() {

				this.refresh = true;

				this.getTicketData()
			},

			updateTableAndData(value) {
				
				this.resetCounter = value;

				this.updateData()
			},

			updateThreads() {

				this.$refs.threadsList.updateThreads();
			},

			updateActions() {

				this.$store.dispatch('updateTicketActions',this.ticketId);
			},

			updateData(from) {

				window.eventHub.$emit('refreshTicket');
				
				this.refresh = true;

				this.getTicketData();

				this.updateTable(from);

				this.updateThreads();
			},

			backToInbox() {

				var elmnt = document.getElementById('tickets-index');
	  			
	  			elmnt.scrollIntoView({ behavior : "smooth"});

	  			this.loading = false;
			},

			closeTimeline() {

				this.hideTimeline()
			},

			subString(value){

				return getSubStringValue(value,10)
			},

			getTicketData() {

				this.$store.dispatch('setTicketId', this.ticketId);

				this.$store.dispatch('updateTicketActions',this.ticketId);

				axios.get('/api/agent/ticket-details/'+this.ticketId).then(res=>{

					if(!this.resetCounter){
						
						this.counter += 1;  
					}

					this.loading = false;

					this.refresh = false;

					this.hasDataPopulated = true;
					
					this.ticketData = res.data.data.ticket;
					
					this.ticket_ratings = this.ticketData.ratings;

					this.ratingTypes(this.getRatingTypes)

				}).catch(err=>{

					this.loading = false;

					this.hasDataPopulated = true;
				});
			},

			ratingTypes(types) {

				this.system_ratings = types;

				var ratingArr=[];

				if(this.ticket_ratings.length != 0){

					for(var i in this.system_ratings){

						for(var j in this.ticket_ratings){

							if(this.system_ratings[i].id == this.ticket_ratings[j].rating_id){
									
								if(this.system_ratings[i].restrict == '' || this.system_ratings[i].restrict == this.ticketData.departments.name || this.system_ratings[i].restrict == 'General'){

									this.system_ratings[i]['rating_value']=this.ticket_ratings[j].rating_value;

									this.system_ratings[i]['ticket_id']=this.ticketData.id;

									ratingArr.push(this.system_ratings[i])

								}
							}
						}
					}
				}
				
				this.system_ratings=ratingArr;
			},
		},

		components : {

			VclTable,

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'custom-loader': require('components/MiniComponent/Loader'),

			'rating-component':require('components/MiniComponent/RatingComponent'),
			
			'timeline-actions':require('./Mini/Timeline/TimelineActions'),

			'timeline-details':require('./Mini/Timeline/TimelineDetails'),

			'inbox-associates':require('./Mini/Associates/InboxAssociates'),

			'inbox-actions':require('./Mini/Actions/InboxActions'),

			'inbox-threads':require('./Mini/Logs/InboxThreads'),

			'alert' : require('components/MiniComponent/Alert'),

			'faveo-box': require('components/MiniComponent/FaveoBox'),
		}
	};
</script>

<style scoped>
	
	#ticket_tags{
		background-color : #eee; 
		color : #444;
		position: relative;
   	line-height: 2;
	}
	#ratings_div{
		margin-top: -10px;
	}	
	.timeline_title { margin: auto !important ;font-size: 18px !important; }

	.ticket-actions { margin-top: 10px; }

	#tags_div, #labels_div { margin-top: 5px; }
</style>

<style>
	
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