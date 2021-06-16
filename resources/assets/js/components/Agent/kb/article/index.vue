<template>
<div>
<!-- for search sort and pagination -->
<div class="row">
	<div class="col-md-4">
		<data-per-page :ticketper="perpage" v-on:message="handlePageCount"/>
		<kb-sorting name1="Published time" value1="publish_time" name2="Article name" value2="name" v-on:sort="sorting"/>
	</div>
	<div class="col-md-8">
		<div class="pull-right">
			<span style="line-height: 2;">{{ lang('search') }}:&nbsp;</span>
			<input id="search" type="text"  class="form-control search-field" v-model="searchFilter" placeholder="search here" @keyup="refresh">
		</div>
	</div>
</div>
<div class="row" v-if="length !=0">
	<div class="col-md-12" v-if="loading==true">
		<div class="col-md-5"></div>
		<div class="col-md-2">
			<fulfilling-bouncing-circle-spinner :animation-duration="4000" :size="size" color="#1d78ff"/>
		</div>
		<div class="col-md-5"></div>
	</div>
	<!-- for article -->
	<div class="col-md-12" v-if="loading==false">
		<alert-notification  :successShow="SuccessAlert"  :successMsg="lang(success)" :errorShow="ErrorAlert"  :errorMsg="lang(error)" />
	<articles-box v-for="article in article_List"  :key="article.id" :article="article" :status="status"></articles-box>
		<div class="pull-right" v-if="records >  10">
			<uib-pagination :total-items="records" v-model="pagination" class="pagination" :boundary-links="true" :items-per-page="perpage" @change="pageChanged()"  :rotate="true" :max-size="3" :force-ellipses="true">></uib-pagination>
		</div>
	</div>
</div>
<div v-if="show == true && records == 0">
	<p style="text-align:center">{{lang('no-data-to-show')}}</p>
</div>
</div>  
</template>
<script>
import axios from 'axios'
export default {
	data() {
		return {
			article_List:'',
			paramsObj:{},
			length:0,
			// for pagination
			perpage:10,
			pagination:{currentPage: 1},
			records:0,
			// for loader
			loading:false,
			size:60,
			// for search
			search:'',
			status:'',
			SuccessAlert:'none',
		  success:'',
		  ErrorAlert:'none',
		  error:'',
		  show:false
				}
		},
		created() {
			 window.eventHub.$on('ShowAlert', this.showAlert);
		},
		mounted() {
			this.commonApi();
			axios.get('kb/settings/getvalue').then(({data})=>{
				this.status = data.data.kbsettings.status;
			}).catch(res=>{
				console.log(res)
			})
		},

		computed: {
			// for search
      searchFilter : {
				get() {
          return this.search;
        },
        set(value) {
          this.search=value;
          this.paramsObj['search-query']=value;
          this.commonApi(this.paramsObj);
        }
      },
		},
		methods: {
			commonApi(x) {
				this.loading = true;
					var params = x;
					axios.get('get-articles',{params}).then(res=>{
						this.loading = false;
						this.article_List=res.data.message.data;
						if(res.data.message.total == 0){
							this.show =true;
						}
						this.records = res.data.message.total;
						this.perpage = res.data.message.per_page;
						this.length = res.data.message.total;
					},err=> {
						console.log(err);
					}).catch(res=>{
                       console.log(res);
                   })
			},
			//per page change function
		handlePageCount(payload){
				this.pagination.currentPage = 1;
				this.paramsObj['pagination']=payload;
				setTimeout(()=>{
	            this.commonApi(this.paramsObj);
					},100)
			},
			//sorting
        sorting(payload){
	        this.paramsObj['sort-by']=payload.value;
	        this.commonApi(this.paramsObj);
	    },
	    // refresh 
	    refresh() {
	    	this.pagination.currentPage = 1;
	    },
	      // for page changing
		pageChanged() {
			this.paramsObj['page']=this.pagination.currentPage;
			this.commonApi(this.paramsObj);
		},
		showAlert() {
				this.SuccessAlert="block";
					this.success="article_deleted_successfully";
					setTimeout(()=>{
					$('.alert-success, .alert-danger').not('.do-not-slide').slideUp( 1000, function() {});
						setTimeout(()=>{
						 	 this.SuccessAlert ='none';
			                  this.commonApi();
			                  this.pagination.currentPage = 1;
		                }, 1000);
		        }, 3000);
			},

		},

		components : {

			'articles-box': require('components/Agent/kb/article/ReusableComponents/indexBox'),

			'kb-sorting': require('components/Agent/kb/common/kbSorting'),

			'data-per-page': require('components/Agent/kb/common/dataPerPage')
		}
};
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
		transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
		opacity: 0;
}
.badge{
    position: absolute;
    background: red;
    height:14px;
    right:9.4rem;
    width:16px;
    text-align: center;
    line-height: 1;
    font-size: 1rem;
    border-radius: 50%;
    color:white;
}
#search {
width: 78%;display:inline;
}
</style>