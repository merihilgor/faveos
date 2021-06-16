<template>
<div>
<!-- for search sort and pagination -->
<div class="row">
	<div class="col-md-4">
		<data-per-page :ticketper="perpage" v-on:message="handlePageCount"/>
		<kb-sorting name1="Created time" value1="created_at" v-on:sort="sorting"/>
	</div>
	<div class="col-md-8">
		<div class="pull-right">
			<span style="line-height: 2;">{{ lang('search') }}:&nbsp;</span>
			<input id="search" type="text"  class="form-control search-field" v-model="searchFilter" placeholder="search here" @keyup="refresh()">
		</div>
</div>
</div>
<!-- for comments -->
	<div class="callout callout-default" :style="callOutStyle">
				<span id="call"><p>{{calloutText}}.&nbsp;&nbsp;&nbsp;</p><a id="all" href="#" @click="seeAll">{{lang('click_to_see_all')}}</a></span>
		</div>

<div class="nav-tabs-custom">
	<!-- nav header -->
	<ul class="nav nav-tabs">
		<li v-for="section in tabs" v-bind:class="{ active: filter === section.id }">
			<a id="comment_tab" href="javascript:void(0)" data-toggle="tab" @click="comments(section.id)">
				<strong>{{lang(section.title)}}</strong>
				<span class="badge bg-yellow">{{section.count}}</span>
			</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="active tab-pane">
			<div class="row" v-if="length !=0">
				<!-- for loader -->
				<div class="col-md-12" v-if="loading==true">
					<div class="col-md-5"></div>
					<div class="col-md-2">
						<fulfilling-bouncing-circle-spinner :animation-duration="12000" :size="size" color="#1d78ff"/>
					</div>
					<div class="col-md-5"></div>
				</div>
				<!-- for comment data -->
				<div class="col-md-12" v-if="loading==false">
					<alert-notification  :successShow="SuccessAlert"  :successMsg="lang(success)" :errorShow="ErrorAlert"  :errorMsg="lang(error)" />
			
					<comments-box v-for="comment in comments_List"  :key="comment.id" :comment="comment"></comments-box>
					<!-- for pagination -->
					<div class="pull-right" v-if="records >  10">
						<uib-pagination :total-items="records" v-model="pagination" class="pagination" :boundary-links="true" :items-per-page="perpage" @change="pageChanged()" :rotate="true" :max-size="3" :force-ellipses="true"></uib-pagination>
					</div>
				</div>
			</div>
			<div v-if="show == true && records == 0">
				<p style="text-align:center">{{lang('no-data-to-show')}}</p>
			</div>
		</div>
	</div>
</div>
</div>  
</template>
<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
export default {
	data() {
		return {
			comments_List:[],
			paramsObj:{},
			length:0,
			// pagination
			perpage:10,
			pagination:{currentPage: 1},
			records:0,
			// loader
			loading:false,
			size:60,
			// search
			search:'',
			// for comment 
			SuccessAlert:'none',success:'',ErrorAlert:'none',error:'',
			show:false,
			tab:'',
			calloutText:'',
			callOutStyle:{display:'none'},
			filter:2,
			tabs:[{id:2,title:'All',count:0},{id:1,title:'Approved',count:0},{id:0,title:'Unapproved',count:0}],
			commentFilterData:{}
		}
		},
		created() {
			 window.eventHub.$on('ShowAlert', this.showAlert);
			 window.eventHub.$on('ErrorAlert', this.errorAlert);
			 if(this.getCommentFilterData){
				if(this.getCommentFilterData.id !== undefined){
					this.commentFilterData = this.getCommentFilterData
					if(this.commentFilterData.to === 'all'){
						this.filter = 2
						this.callOutStyle.display = 'block';
					} else if(this.commentFilterData.to === 'unapprove') {
						this.filter = 0
						this.callOutStyle.display = 'block';
					} else {
						this.filter = 1
					}
				}
			}
		},
		beforeMount(){
			if (performance.navigation.type == 1) {
				this.seeAll()
			} else {
				if(this.getCommentFilterData){
					if(this.getCommentFilterData.id !== undefined){
						this.calloutText = 'Comments of ' + this.getCommentFilterData.name
						const data = { condition: this.getCommentFilterData.to, article_id: this.getCommentFilterData.id, filter_by:this.filter }
						this.commonApi(data);
						} else {
							this.commonApi()
						}
					}else {
						this.commonApi();
				}
			}
		},

		computed: {
			// for search
			searchFilter : {
				get() {
					return this.search;
				},
				set(value) {
					this.search=value;
					this.paramsObj['search-option']=value;
					if(this.getCommentFilterData){
						if(this.getCommentFilterData.id !== undefined){
							this.commonFunction('search-option',value)
						} else {
							this.commonApi(this.paramsObj);
						}
					}
					else {
					this.commonApi(this.paramsObj);
					}
				} 
			},
			...mapGetters(['getCommentFilterData'])
		},
		methods: {
			commonApi(x) {
				this.loading = true;
					var params = x;
					axios.get('get-comment',{params}).then(res=>{
						this.loading = false;
						this.comments_List=res.data.message.data;
						if( res.data.message.total == 0){
							this.show=true;
						}
						this.records = res.data.message.total;
						this.perpage = res.data.message.per_page;
						this.tabs[0].count = res.data.data.all;
						this.tabs[1].count = res.data.data.approved;
						this.tabs[2].count= res.data.data.pending;
						this.length =  res.data.message.total;
					}).catch(res=>{});
			},
			//per page change function
			handlePageCount(payload){
				this.pagination.currentPage = 1;
				this.paramsObj['pagination']=payload;
				setTimeout(()=>{
					if(this.getCommentFilterData){
						if(this.getCommentFilterData.id !== undefined){
							this.commonFunction('pagination',payload)
						} else {
							this.commonApi(this.paramsObj);
						}
					}
					else {
					this.commonApi(this.paramsObj);
					}
				},100)
			},
			//sorting
			sorting(payload){
				this.show=false;
				this.paramsObj['sort-by']=payload.value;
				if(this.getCommentFilterData){
					if(this.getCommentFilterData.id !== undefined){
						this.commonFunction('sort-by',payload.value)
					} else {
						this.commonApi(this.paramsObj);
					}
				}
				else {
					this.commonApi(this.paramsObj);
				}
			},
			//search option
			refresh() {
				this.pagination.currentPage = 1;
			},
			// for showing approved onapproved and all comments
			comments(x) {
				this.tab =x;
				this.paramsObj['filter_by']=x;
				this.pagination.currentPage = 1;
				this.paramsObj['page']=1;
				if(this.getCommentFilterData){
					if(this.getCommentFilterData.id !== undefined){
						this.commonFunction('filter-by',x)
					} else {
						this.commonApi(this.paramsObj);
					}
				}
				else {
					this.commonApi(this.paramsObj);
				}	
			},

			// for pagination
			pageChanged() {
				this.paramsObj['page']=this.pagination.currentPage;
				let filter = '';
				if(this.getCommentFilterData){
					if(this.getCommentFilterData.id !== undefined){
						this.commonFunction('page',this.pagination.currentPage)
					} else {
						this.commonApi(this.paramsObj);
					}
				}
				else {
				this.commonApi(this.paramsObj);
				}
			},
			commonFunction(param,value){
				if(param === 'filter-by'){
					this.filter = value
				}
				const data = { condition: this.getCommentFilterData.to, article_id: this.getCommentFilterData.id, filter_by:this.filter}
				
				if(param === 'filter-by'){
					data['page']= 1
				} else {
					data[param]= value
				}
				this.commonApi(data);
			},

			seeAll(){
				this.$store.dispatch('commentFilterData',{});
				this.paramsObj['filter_by']=this.filter;
				this.pagination.currentPage = 1;
				this.paramsObj['page']=1;
				this.commonApi(this.paramsObj);
				this.callOutStyle.display = 'none';
			},

			showAlert(x) {
				this.SuccessAlert="block";
					this.success=x;
					setTimeout(()=>{
					$('.alert-success, .alert-danger').not('.do-not-slide').slideUp( 1000, function() {});
						setTimeout(()=>{
							this.SuccessAlert='none';
				this.paramsObj['filter_by']=this.tab;
				this.commonApi(this.paramsObj);
										}, 1000);
						}, 3000);
			},
			errorAlert() {
				this.SuccessAlert="block";
					this.success="comment_deleted";
					setTimeout(()=>{
					$('.alert-success, .alert-danger').not('.do-not-slide').slideUp( 1000, function() {});
						setTimeout(()=>{
							this.SuccessAlert='none'
									this.paramsObj['filter_by']=this.tab;
				this.commonApi(this.paramsObj);
										}, 1000);
						}, 3000);
			},
			
		},

		components : {

			'comments-box': require('components/Agent/kb/comment/ReusableComponents/commentBox'),

			'kb-sorting': require('components/Agent/kb/common/kbSorting'),

			'data-per-page': require('components/Agent/kb/common/dataPerPage')
		}
	};
</script>
<style scoped>
#search {
width: 78%;display:inline;
}
#call{
	display:inline-flex;margin-bottom: -10px;
}
#all{
	color:#72afd2 !important;
}
</style>