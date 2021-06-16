<template>
	
	<div class="box-body" >
		
		<template>
			
			<ul class="timeline" v-if="!loading && problemUpdates.length > 0">
				
				<template v-for="(data,index) in problemUpdates">

					<li class="time-label"> 

						<span class="bg-green"> {{lang(data.key)}} </span>
					</li>
					
					<li>
						
						<i :class="'fa fa-' + getFont(data.key) + ' bg-blue'"></i>

						<div class="timeline-item">

							<span class="time pull-left">

								<span v-if="data.title" class="solution_title">{{data.title}} &nbsp;&nbsp;</span>

								<i class="fa fa-clock-o"></i> {{formattedTime(data.updated_at)}}</span>

							<h3 class="timeline-header" id="time_head">

								<button class="btn btn-danger btn-xs pull-right" id="update_btn" @click="deleteMethod(data.key)">
									<i class="fa fa-trash"> </i> {{lang('delete')}}
								</button>

								<button class="btn btn-primary btn-xs pull-right" id="update_btn" @click="updateMethod(data.key)">
									<i class="fa fa-edit"> </i> {{lang('edit')}}
								</button>
							</h3>

							<div class="timeline-body break">
								
								<span class="text-muted" id="log_desc" v-html="data.description"> </span>

							</div>
							<div class="timeline-footer">
                 <span v-if="data.attachment">

                 		<span v-tooltip="data.attachment.value">
              				<i :class="getIcon(data.attachment)"></i> {{data.attachment.value}} {{'('+formatBytes(data.attachment.size)+')'}}
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

		<template v-if="!loading && problemUpdates.length === 0">
			
				<h4 class="text-center">{{lang('no_data_found')}}</h4>
		</template>

		<div v-if="loading" class="row">

			<loader :animation-duration="4000" :size="60"/>
		</div>

		<transition  name="modal">

			<problem-update-modal v-if="showProblemUpdate" :onClose="onClose" :showModal="showProblemUpdate" 
				:identifier="identifier" :problemId="problemId" alertCompName="problem-view">

			</problem-update-modal>
		</transition>

		<transition  name="modal">

			<problem-update-delete-modal v-if="showUpdateDelete" :onClose="onClose" :showModal="showUpdateDelete" 
				:identifier="identifier" :problemId="problemId" alertCompName="problem-view">

			</problem-update-delete-modal>
		</transition>
	</div>					
</template>

<script>
	
	import axios from 'axios'

	import { mapGetters } from 'vuex'

	export default {

		name : 'problem-planning',

		description : 'Problem planning component',

		props :  {

			problemId : { type : String | Number, default : ''}
		},

		data(){

			return {

				problemUpdates : '',

				loading : false,

				showProblemUpdate : false,

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

				axios.get('/service-desk/api/problem-planning/'+this.problemId).then(res=>{

					this.loading = false;

					this.problemUpdates = res.data.data.planning_popups;

				}).catch(err=>{

					this.loading = false;
				})
			},

			getFont(key){

				return key === 'root_cause' ? 'ambulance' : key === 'impact' ? 'anchor' : key === 'symptoms' ? 'battery-0' : 'thumbs-up'
			},

			getIcon(attach){

				if(attach){
					
					if(attach.type === 'pdf'){

						return 'fa fa-file-pdf-o'
					
					} else if(attach.type === 'xls'){

						return 'fa fa-file-excel-o'
					
					} else if(attach.type === 'txt' || attach.type === 'text'){

						return 'fa fa-file-text-o'
					
					} else if(attach.type === 'png' || attach.type === 'jpg' || attach.type === 'jpeg' || attach.type === 'gif' || attach.type === 'PNG' || attach.type === 'JPG' || attach.type === 'JPEG' || attach.type === 'GIF'){

						return 'fa fa-file-picture-o'  
					
					} else {

						return 'fa fa-file'
					}     
				}
		 
			},

			 formatBytes(bytes,decimals) {
		
				if(bytes == 0) return '0 Bytes';
		
				var k = 1024,
		
				dm = decimals || 2,
		
				sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
		
				i = Math.floor(Math.log(bytes) / Math.log(k));
		
				return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
			},

			downloadFile(id,key){

				window.open(this.basePath() + '/service-desk/download/'+id+'/'+key+'/attachment')

			},

			updateMethod(identifier){

				this.showProblemUpdate = true;

				this.identifier = identifier;
			},

			deleteMethod(identifier){

				this.showUpdateDelete = true;

				this.identifier = identifier;
			},

			onClose(){
			
				this.showProblemUpdate = false;

				this.showUpdateDelete = false;

				window.eventHub.$emit('updateProblemActions');

				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'problem-update-modal' : require('../ProblemPlanningModal'),

			'problem-update-delete-modal' : require('./ProblemUpdateDelete'),
		}
	};
</script>

<style scoped>

	.solution_title { font-size: 16px;color: black; }
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
</style>

<style>
	.table{
		overflow-x: auto;
	}
	#log_desc table,#log_desc table td,#log_desc table tr,#log_desc table th {  
	  border: 1px solid #ddd;
	  text-align: left;
	}

	#log_desc table {
	  border-collapse: collapse;
	  width: 100%;
	}

	#log_desc table th, #log_desc table td {
	  padding: 15px;
	}
</style>