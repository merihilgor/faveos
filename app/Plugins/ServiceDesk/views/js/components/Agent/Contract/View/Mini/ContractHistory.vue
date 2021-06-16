<template>

	<div class="row">

		<div v-if="loading" class="col-md-12" id="loader_log">

			<loader :animation-duration="4000" :size="60"/>
		</div>

		<div class="col-md-12">
		            
			<ul class="timeline" v-if="!loading && history.length > 0">

				<template v-for="(log,index) in history">

					<li class="time-label" v-if="checkDate(index)">

						<span class="bg-green">{{formattedDate(log.created_at)}}</span>
					</li>

					<li>

						<i class="fa fa-dot-circle-o"></i>

						<div class="timeline-item">

							<span class="time">

								<i class="fa fa-clock-o"></i> {{formattedTime(log.created_at)}}
							</span>

							<h3 class="timeline-header border-0">
									
							</h3>

							<div class="timeline-body">
								
								<div class="row">

						        <div class="col-md-12">

						        	<div class="col-md-6 info-row">

						            <div class="col-md-6"><label>{{ trans('contract_start_date') }}</label></div>

						            <div class="col-md-6">

						              <a v-tooltip="formattedTime(log.contract_start_date)"> {{formattedTime(log.contract_start_date)}}</a>
						            </div>
						         </div>

						         <div class="col-md-6 info-row">

						            <div class="col-md-6"><label>{{ trans('contract_end_date') }}</label></div>

						            <div class="col-md-6">

						              <a v-tooltip="formattedTime(log.contract_end_date)"> {{formattedTime(log.contract_end_date)}}</a>
						            </div>
						         </div>

						         <div class="col-md-6 info-row">

						            <div class="col-md-6"><label>{{ trans('created_by') }}</label></div>

						            <div class="col-md-6">

						              	<a v-tooltip="log.owner ? log.owner.name : '---'"> 

						              		{{subString(log.owner ? log.owner.name : '---')}}
						              	</a>
						            </div>
						         </div>

						          <div class="col-md-6 info-row">

						            <div class="col-md-6"><label>{{ trans('approver') }}</label></div>

						            <div class="col-md-6">

						              	<a v-tooltip="log.approver ? log.approver.name : '---'"> 
						              		
						              		{{subString(log.approver ? log.approver.name : '---')}}
						              	</a>
						            </div>
						         </div>

						          <div class="col-md-6 info-row">

						            <div class="col-md-6"><label>{{ trans('cost') }}</label></div>

						            <div class="col-md-6">

						              <a v-tooltip="log.cost.toString()"> {{subString(log.cost)}}</a>
						            </div>
						         </div>

						         <div class="col-md-6 info-row">

						         	<span v-if="log.type" class="label label-success log_type">{{ log.type }}</span>
						            
						            <span v-if="log.current" class="label label-success">{{ trans('current') }}</span>

										<span v-if="log.expired" class="label label-danger"> {{ trans('expired') }}</span>
						         </div>
						      </div> 
						   </div>
							</div>
						</div>
					</li>

					<li v-if="showThreadEnd(index)"> <i class="fa fa-history bg-gray"></i> </li>

				</template>
			</ul>
		</div>

		<template v-if="!loading && history.length === 0">
				
			<h4 class="text-center">{{trans('no_data_found')}}</h4>
		</template>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex';

	import axios from 'axios'

	import { getSubStringValue } from 'helpers/extraLogics'

	export default {

		name : 'contract-history',

		description : 'Contract history page',

		props : {

			contractId : { type : String | Number, default : '' },
		},

		data(){

			return {

				loading : true,

				apiUrl : '/service-desk/api/contract-threads/'+this.contractId,

				history : '',

			}
		},

		created(){

			window.eventHub.$on('updateHistory',this.getValues)
		},

		beforeMount() {

			this.getValues();
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			subString(value,length = 15){
    
	        return getSubStringValue(value,length)
	      },
			
			getValues(){

				axios.get(this.apiUrl).then(res=>{

					this.loading = false;

					this.history = res.data.data.contract_threads;

				}).catch(error=>{

					this.loading = false;
				})
			},

			checkDate(x){

				if(x==0){

					return true;

				}else{

					let date1=this.formattedDate(this.history[x-1].created_at);

					let date2=this.formattedDate(this.history[x].created_at);

					if(date1!=date2){

						return true;
					}
				}
			},

			showThreadEnd(x){

				return x === this.history.length-1 ? true : false
			}
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader')
		}
	};
</script>

<style scoped>
	
	.info-row{
	   border-top: 1px solid #f4f4f4; padding: 10px;
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

	#loader_log{
		margin-top:30px;margin-bottom:30px;
	}

	.border-0 { border-bottom: 0px solid #ddd !important; }

	.log_type { font-size: 13px;margin: 14px; }
</style>