<template>
	
	<div class="box-body" >
		
		<template>
			
			<ul class="timeline" v-if="!loading && releaseUpdates.length > 0">
				
				<template v-for="(data,index) in releaseUpdates">

					<li class="time-label"> 

						<span class="bg-green"> {{trans(data.key)}} </span>
					</li>
					
					<li>
						
						<i :class="'fa fa-' + getFont(data.key) + ' bg-blue'"></i>

						<div class="timeline-item">

							<span class="time pull-left">

								<i class="fa fa-clock-o"></i> {{formattedTime(data.updated_at)}}</span>

							<h3 class="timeline-header" id="time_head">

								<button class="btn btn-danger btn-xs pull-right" id="update_btn" @click="deleteMethod(data.key)">
									<i class="fa fa-trash"> </i> {{trans('delete')}}
								</button>

								<button class="btn btn-primary btn-xs pull-right" id="update_btn" @click="updateMethod(data.key)">
									<i class="fa fa-edit"> </i> {{trans('edit')}}
								</button>
							</h3>

							<div class="timeline-body break">
								
								<span class="text-muted" id="log_desc" v-html="data.description"> </span>

							</div>
							
							<div class="timeline-footer">
                 			
                 			<span v-if="data.attachment">

		                 		<span v-tooltip="data.attachment.value">
		              				<img :src="data.attachment.icon_url" class="attach_img"/> 
		              					{{data.attachment.value}} {{'('+data.attachment.size+')'}}
		              			</span>
												
									<a id="download" v-tooltip="'Click to download'" @click="downloadFile(data.attachment.id,data.attachment.owner)"> 
										
										<i class="fa fa-download"></i>
									</a>
								</span>
                		</div>
						</div>
					</li>
				</template>
				<li>
					
					<i class="fa fa-clock-o bg-gray"></i>
				</li>
			</ul>
		</template>

		<template v-if="!loading && releaseUpdates.length === 0">
			
				<h4 class="text-center">{{trans('no_data_found')}}</h4>
		</template>

		<div v-if="loading" class="row">

			<loader :animation-duration="4000" :size="60"/>
		</div>

		<transition  name="modal">

			<release-update-modal v-if="showReleaseUpdate" :onClose="onClose" :showModal="showReleaseUpdate" 
				:identifier="identifier" :releaseId="releaseId" alertCompName="release-view">

			</release-update-modal>
		</transition>

		<transition  name="modal">

			<release-planning-delete v-if="showUpdateDelete" :onClose="onClose" :showModal="showUpdateDelete" 
				:identifier="identifier" :releaseId="releaseId" alertCompName="release-view">

			</release-planning-delete>
		</transition>
	</div>					
</template>

<script>
	
	import axios from 'axios'

	import { mapGetters } from 'vuex'

	export default {

		name : 'release-planning',

		description : 'Release planning component',

		props :  {

			releaseId : { type : String | Number, default : ''}
		},

		data(){

			return {

				releaseUpdates : '',

				loading : false,

				showReleaseUpdate : false,

				showUpdateDelete : false,

				identifier : ''
			}
		},
		
		beforeMount(){

			this.getDetails();
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		methods : {

			getDetails(){

				this.loading = true;

				axios.get('/service-desk/api/release-planning/'+this.releaseId).then(res=>{

					this.loading = false;

					this.releaseUpdates = res.data.data.planning_popups;

				}).catch(err=>{

					this.loading = false;
				})
			},

			getFont(key){

				return key === 'build-plan' ? 'cubes' : 'adjust'
			},

			downloadFile(id,key){

				window.open(this.basePath() + '/service-desk/download/'+id+'/'+key+'/attachment')

			},

			updateMethod(identifier){

				this.showReleaseUpdate = true;

				this.identifier = identifier;
			},

			deleteMethod(identifier){

				this.showUpdateDelete = true;

				this.identifier = identifier;
			},

			onClose(){
			
				this.showReleaseUpdate = false;

				this.showUpdateDelete = false;

				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'release-update-modal' : require('./Child/ReleasePlanningModal'),

			'release-planning-delete' : require('./Child/ReleasePlanningDelete'),
		}
	};
</script>

<style scoped>

	#update_btn{
		margin-right: 2px;
	}
	#update_header{
		padding: 0px !important;
	}
	#download{
		cursor: pointer;
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

	.timeline>li>.timeline-item>.timeline-header {
		margin: 0;
		color: #555;
		border-bottom: 1px solid #ddd;;
		padding: 10px;
		font-size: 16px;
		line-height: 1.1;
	}

	.timeline>li>.timeline-item{
		background: #fbfbfb;
	}

	.timeline>li>.timeline-item>.timeline-body, .timeline>li>.timeline-item>.timeline-footer {
		padding: 10px;
	}
	.timeline>li>.timeline-item>.timeline-body, .timeline>li>.timeline-item>.timeline-footer {
		padding: 10px;
	}

	.timeline>li>.timeline-item>.time {
		color: #999;
		float: right;
		padding: 10px;
		font-size: 12px;
	}
	#time_head{
		padding-bottom: 30px !important; 
	}

	.break { word-break: break-all; }

	.attach_img { width: 15px; height: 15px; }
</style>