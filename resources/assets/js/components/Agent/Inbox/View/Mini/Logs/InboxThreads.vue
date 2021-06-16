<template>

			<div>

				<faveo-box :title="lang('threads')">

					<a slot="headerMenu" class="text-muted pull-right refresh_a" @click="updateThreads"
						v-tooltip="lang('click_to_refresh')">

						<i class="fa fa-refresh"></i>
					</a>

					<div class="scrollable-ul">
				
						<ul class="timeline timeline-inverse" v-if="inboxThreads.length > 0">
				
							<template v-for="(thread,index) in inboxThreads">
								
								<li class="time-label" v-if="checkDate(index)">

									<span class="bg-green">{{formattedDate(thread.created_at)}}</span>
								</li>

								<thread-body :thread="thread" :key="'thread'+index" :index="index"></thread-body>
								

								<li v-if="showThreadEnd(index)"> <i class="fa fa-history bg-gray"></i> </li>

								
							</template>
								
								<infinite-loading @infinite="getThreads" ref="infiniteLoading">		
											<div slot="spinner"></div>								
											<div slot="no-results"></div>
											<div slot="no-more"></div>
									</infinite-loading>

								</ul>
							</div>

							<custom-loader :duration="4000" v-if="showLoader" />
					</faveo-box>
			</div>
</template>

<script>

	import { mapGetters } from 'vuex';
	
	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import axios from 'axios';

	export default {

		name : 'inbox-threads',

		description : 'Inbox Threads Component',

		props : {

			ticketId : { type : String | Number, default : '' }
		},

		data() {

			return {

				inboxThreads : [],
       	 	
				page : 1,
						
				showLoader: false
			}
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount() {

			this.getThreads()
		},

		methods : {

			updateThreads() {

				this.inboxThreads = [];
				
				this.page = 1;

				this.getThreads(undefined, true);
			},

			getThreads($state, isRefresh = false) {

				this.showLoader = true;

		  		axios.get('/api/agent/ticket-conversation/'+this.ticketId, { params: { page: this.page } }).then(res => {
			 		
						if(res.data.data.data.length) {

							if(isRefresh) {
								
								this.inboxThreads = res.data.data.data;
							
							} else {
								
								this.inboxThreads.push(...res.data.data.data);
							}
												  			
				  			this.page += 1;
						
						} else {
				  			
				  			$state && $state.complete();
						}
		  		}).catch(error => {

			 		errorHandler(error, 'inbox-threads');

			 		this.inboxThreads = [];
		  		
		  		}).finally(() => {
					
					$state && $state.loaded();
					
					this.showLoader = false;
		  		})
			},

			checkDate(x){

				if(x==0){

					return true;

				}else{

					var date1=this.formattedDate(this.inboxThreads[x-1].created_at);

					var date2=this.formattedDate(this.inboxThreads[x].created_at);

					if(date1!=date2){

						return true;
					}
				}
			},

			showThreadEnd(x){

				return x === this.inboxThreads.length-1 
			}
		},

		components : {

			'thread-body' : require('./Mini/ThreadBody'),

			'faveo-box': require('components/MiniComponent/FaveoBox'),

			'custom-loader': require('components/MiniComponent/Loader')
		}
	};
</script>

<style scoped>
	.timeline-inverse>li>.timeline-item {
	 background: #f7f7f77d;
	 border: 1px solid #ddd;
	 -webkit-box-shadow: none;
	 box-shadow: none;
	}

	.timeline:before {
	 content: '';
	 position: absolute;
	 top: 0;
	 bottom: 0;
	 width: 4px;
	 background: #ddd;
	 left: 31px;
	 margin: 0;
	 border-radius: 2px;
}
.timeline>li>.timeline-item>.time {
	 color: #999;
	 float: right;
	 padding: 10px;
	 font-size: 12px;
}
.timeline-inverse>li>.timeline-item>.timeline-header {
	 border-bottom-color: #ddd !important;
}
.timeline>li>.timeline-item>.timeline-header {
	 margin: 0;
	 color: #555;
	 border-bottom: 1px solid #f4f4f4;
	 padding: 10px;
	 font-size: 16px;
	 line-height: 1.1;
}
.timeline>li>.timeline-item>.timeline-body, .timeline>li>.timeline-item>.timeline-footer {
	 padding: 10px;
}

.scrollable-ul { max-height: 100%; overflow: auto; }

#thread_img{ margin-top: -3px; }

.pointer { cursor: pointer;color: #999 !important; }

.refresh_a { cursor: pointer;color: #777 !important; }
</style>