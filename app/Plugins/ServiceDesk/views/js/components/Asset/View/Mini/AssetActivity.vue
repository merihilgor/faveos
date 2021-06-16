<template>

	<div class="row" id="asset_activity_div">

		<div class="col-md-12">

			<div id="select_limit" v-if="!loading && activity_log.length > 0">

				<select class="form-control" v-model="perPage" @change="logLimit(perPage)">

					<option value="10">10</option>

					<option value="25">25</option>

					<option value="50">50</option>

					<option value="100">100</option>
				</select>
			</div>

			<div class="dropdown" id="choose_order" v-if="!loading && activity_log.length > 0">

				<button id="sort_btn" type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">{{lang('created_time')}} - {{lang(sort_key)}}

					<span class="caret"></span>
				</button>

				<ul class="dropdown-menu" style="z-index: 9999;">

					<li>
						<a href="javascript:;" @click="orderBy('asc')"><i class="fa fa-sort-amount-asc"></i> {{lang('asc')}}</a>
					</li>

					<li>
						<a href="javascript:;" @click="orderBy('desc')"><i class="fa fa-sort-amount-desc"></i> {{lang('desc')}}</a>
					</li>
				</ul>
			</div>

			<div class="pull-right" id="page_div" v-if="!loading && total > 10">

				<uib-pagination id="page2" :total-items="Records" v-model="pagination" :max-size="maxSize" class="pagination" :boundary-links="false" :items-per-page="PerPage" ></uib-pagination>
			</div>
		</div>

		<div v-if="filtering" class="col-md-12">

			<custom-loader :duration="4000"></custom-loader>
		</div>

		<div v-if="loading" class="col-md-12" id="loader_log">

			<loader :animation-duration="4000" :size="60"/>
		</div>

		<div class="col-md-12">
		            
			<ul class="timeline" v-if="!loading && activity_log.length > 0">

				<template v-for="(log,index) in activity_log">

					<li class="time-label" v-if="checkDate(index)">

						<span class="bg-green">{{formattedDate(log.created_at)}}</span>
					</li>

					<li>

						<i class="fa fa-dot-circle-o"></i>

						<div class="timeline-item">

							<span class="time">

								<i class="fa fa-clock-o"></i> {{formattedTime(log.created_at)}}
							</span>

							<h3 class="timeline-header">

								<faveo-image-element id="problem_img" :source-url="log.creator.profile_pic" :classes="['img-circle', 'img-bordered-sm']" alternative-text="User Image" :img-width="25" :img-height="25"/>

								<a :href="basePath()+'/user/'+log.creator.id">{{log.creator.full_name}}  </a>
									
							</h3>

							<div class="timeline-body">
								
								<span v-html="log.name" id="log_desc"></span>
							</div>
						</div>
					</li>

					<li v-if="showThreadEnd(index)"> <i class="fa fa-history bg-gray"></i> </li>

				</template>
			</ul>

			<div class="pull-right" id="page_div" v-if="!loading && total > 10">

				<uib-pagination id="page2" :total-items="Records" v-model="pagination" :max-size="maxSize" class="pagination" :boundary-links="false" :items-per-page="PerPage" ></uib-pagination>
			</div>
		</div>

		<template v-if="!filtering && !loading && activity_log.length === 0">
				
			<h4 class="text-center">{{lang('no_data_found')}}</h4>
		</template>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex';

	import Vue from 'vue';

	import axios from 'axios'

	Vue.use(require("vuejs-uib-pagination"));

	export default {

		name : 'asset-activity',

		description : 'Asset activity page',

		props : {

			assetId : { type : String | Number, default : '' },
		},

		data(){

			return {

				loading : true,

				apiUrl : '/service-desk/api/asset-log/'+this.assetId,

				activity_log : '',

				perPage:'10',

				total:0,

				maxSize:5,

				pagination:{currentPage: 1},

				paramsObj : {},

				filtering : false,

				sort_key : 'desc'
			}
		},

		created(){

			window.eventHub.$on('updateAssetLogs',this.getUpdateActivity)
		},

		beforeMount() {

			this.paramsObj['page'] = this.pagination.currentPage

			this.getValues(this.paramsObj);
		},

		watch:{

			"pagination.currentPage"(newValue,oldValue){

				this.filtering = true;

				var elmnt = document.getElementById('asset_activity_div');

				elmnt.scrollIntoView({ behavior : "smooth"});

				this.paramsObj['page'] = newValue;

				this.getValues(this.paramsObj);

				return newValue
			}
		},

		computed : {

			PerPage: function() {
				 return this.perPage ? parseInt(this.perPage) : 10;
			},

			Records: function() {
					return this.total ? parseInt(this.total) : 0;
			},

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			getUpdateActivity(){

				this.activity_log = [];

				this.filtering = true;

				this.pagination.currentPage = 1;

				this.sort_key = 'desc';

				this.perPage = 10 ;

				this.total = 0;

				this.paramsObj['page'] = 1;

				this.paramsObj['limit'] = 10;

				this.paramsObj['sort-order'] = 'desc';

				setTimeout(()=>{

					this.getValues(this.paramsObj);
				},1000)
			},

			commonFilter(){

				this.filtering = true;

				this.pagination.currentPage = 1;

				this.paramsObj['page'] = 1;

				this.getValues(this.paramsObj);
			},

			logLimit(limit){

				this.paramsObj['limit'] = limit;

				this.commonFilter();
			},

			orderBy(order){

				this.sort_key = order;

				this.paramsObj['sort-order'] = order;

				this.commonFilter();
			},

			getValues(params){

				axios.get(this.apiUrl,{params}).then(res=>{

					this.loading = false;

					this.filtering = false;

					this.activity_log = res.data.data.asset_activity_logs.data;

					this.total = res.data.data.asset_activity_logs.total;

					this.perPage = res.data.data.asset_activity_logs.per_page;

				}).catch(error=>{

					this.loading = false;

					this.filtering = false;
				})
			},

			checkDate(index){

				/**
				 * This method is for showing Time Label on Timeline when date1 and date2 are not same
				 * @type {Boolean}
				 */
				let date1 = '' , date2 = '';

				if(index) {

					date1=this.formattedDate(this.activity_log[index-1].created_at);

					date2=this.formattedDate(this.activity_log[index].created_at);
				}

				return !index || (date1!=date2) ? true : false;
			},

			showThreadEnd(x){

				return x === this.activity_log.length-1;
			}
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'custom-loader' : require('components/MiniComponent/Loader'),
			
			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>

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

	#mar_10{
		margin-top: 10px !important;
	}

	#select_limit{
		display: inline-block; float: left;
	}

	#choose_order{
		display: inline-block; margin-left: 10px;
	}

	#page_div{
		margin-top: -20px;
		margin-right: 25px;
	}

	#sort_btn{
		background-color: rgb(255, 255, 255);
	}

	#log_desc strong p {

		display: inherit !important;
	}

	#problem_img{
		margin-top: -3px;
	}
</style>

<style>
	#log_desc .table{
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