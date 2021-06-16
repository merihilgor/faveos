<template>
	<ul v-if="userInfo" class="timeline">
		<li>
			<div class="timeline-item" id="timeline" v-on:mouseenter="showButton(article.id)" v-on:mouseleave="hideButton(article.id)">
				<span v-if="langs=='ar'" id="ar" class="pull-left">
					<a href="javascript:void(0)" v-popover:right><i class="fa fa-gears"></i> {{lang('article_info')}}</a>
						<popover v-if="showId == article.id" name="right" style="top: !42px;left:!927px;width:!160px">
							<p v-if="article.author"><i class="fa fa-user"></i>&nbsp;&nbsp;
								<a href="javascript:void(0)" v-if="article.author.first_name" >{{article.author.first_name}}  {{article.author.last_name}} </a>
								<a href="javascript:void(0)" v-else>{{article.author.user_name}} </a>
							</p>
							<p><i class="fa fa-newspaper-o"></i>&nbsp;
								<strong> Categories  </strong> 
								<a href="javascript:void(0)" v-for="(category,index) in article.categories.slice(0,20)"><li id="cat">{{category.name | convert1}} </li>
								</a>
							</p>
						</popover>
					</span>
					<span v-else class="time">
					<a href="javascript:void(0)" v-popover:left><i class="fa fa-gears"></i> {{lang('article_info')}}</a>
						<popover v-if="showId == article.id" name="left" style="top: !42px;left:!927px;width:!160px" >
							<p v-if="article.author"><i class="fa fa-user"></i>&nbsp;&nbsp;
								<a href="javascript:void(0)" v-if="article.author.first_name" >{{article.author.first_name}}  {{article.author.last_name}} </a>
								<a href="javascript:void(0)" v-else>{{article.author.user_name}} </a>
							</p>
							<p><i class="fa fa-newspaper-o"></i>&nbsp;
								<strong> Categories : </strong> 
								<a href="javascript:void(0)" v-for="(category,index) in article.categories.slice(0,20)"><li id="cat" :title="category.name">{{category.name | convert1}} </li>
								</a>
							</p>
						</popover>
					</span>

					<h3 class="timeline-header" style="border-top:1px solid #f2f2f2;">
						<a :href="'show/'+article.slug">{{article.name | to-up}}</a>
						<div>
							<span id="span" class="time">{{lang('published')}} - {{article.publish_time}}</span>
						</div>
					</h3>
					<div id="status1" class="timeline-body" v-html="convert(article.description)"></div>
					<div class="timeline-footer" >
						<span v-if="langs=='ar'" id="commentsar" class="time pull-left">
							<a :href="'comment'"><i id="ico" class="fa fa-commenting-o" title="pending comments"></i>
								<span v-if="article.pending_comments_count < 10" id="commar" class="label label-danger">{{article.pending_comments_count}}</span>
								<span v-else id="commar" class="label label-danger">9+</span>
								<span >{{article.all_comments_count}} comments</span>
							</a>
						</span>
						<span v-else id="comments" class="time pull-right">
							<a :href="'comment'"><i id="ico" class="fa fa-commenting-o" title="pending comments"></i>
								<span v-if="article.pending_comments_count < 10" id="comm" class="label label-danger" title="pending comments">{{article.pending_comments_count}}</span>
								<span v-else id="comm" class="label label-danger" title="pending comments">9+</span>
								<span id="all" title="total comments">{{article.all_comments_count}} comments</span>
							</a>
						</span>
						<a id="btn" :href="'article/'+article.id+'/edit'" class="btn btn-default btn-xs"><i class="fa fa-edit"></i> {{lang('edit')}}</a> 
						<a v-if="status==1" id="view"  target="_blank" @click="viewArticle(article.id)" :href="'show/'+article.slug" class="btn btn-default btn-xs"><i class="fa fa-eye"></i> {{lang('view')}}</a> 
						<a id="btn" href="javascript:void(0)" class="btn btn-default btn-xs" data-toggle="modal" data-target="#deletelib" @click="getDeleteId(article.slug)"><i class="fa fa-trash"></i> {{lang('delete')}}</a>
					</div>
				</div>
<!-- popup for article delete -->
        <delete-popup v-on:deleteMedia="deleteFile()"></delete-popup>
			</li>
		</ul>
</template>
<script>
import Popover from 'vue-js-popover';
Vue.use(Popover);
import Vue from 'vue'
export default {
	props:['article','status'],
	data() {
		return {
			showId:null,
			deleteId:'',
			user_data:'',
			langs:''
		}
	},
	filters : {
	// to capitalize for letter
		toUp(value){
			return value.charAt(0).toUpperCase() + value.slice(1);
		},
	// showing only 25 characters if the length is more than 50
		convert1(value) {
			if(value.length > 25){
				return value.substring(0,25) + '..';
			} else {
				return value;
			}
		}
	},
	computed: {
     // getting user data
     	userInfo(){
			if(this.$store.getters.getUserData){
				this.user_details(this.$store.getters.getUserData);
				return this.$store.getters.getUserData;
			}
		}
	},	
	methods: {
	// getting user data
		user_details(user) {
			this.user_data = user.user_data;
			this.langs=this.user_data.user_language;
		},
// for popover
		showButton(x) {
			this.showId = x;
		},
		hideButton(x) {
			this.showId = null;
		},
// for deleting comment
		deleteFile() {
				axios.get('article/delete/'+this.deleteId).then(res=>{
					window.scrollTo(0, 0);
					window.eventHub.$emit('ShowAlert');
							})
		},
//test
		getDeleteId(x){
      this.deleteId=x;
		},
// for view comment
		viewArticle(article) {
			this.$store.dispatch('setArticleId', article);
		},
// showing only 250 characters in article description
		convert(x) {
			if(x.length > 100){
				return x.substring(0,250) + '.............';
			} else {
				return x;
			}
		}
	},

	components :{

		'delete-popup': require('components/Agent/kb/common/deletePopup')
	}
};
</script>
<style scoped>
	#timeline {
		margin-right: -9px;
		margin-left:0px
	} 
	#span {
		color: #999;font-size: 13px;line-height:2;
	}
		#status1 {
		word-wrap:break-word;border-bottom: 1px solid #eee;
	}
	#comments {
		color: #999;font-size: 13px;line-height:2;margin-right:6px
	}
	#commentsar {
		color: #999;font-size: 13px;line-height:2;margin-left:6px
	}
	#commar {
		position: absolute;left: 88px;text-align: center;font-size: 9px;padding: 1px 2px;line-height: .9;border-radius: 10px;
	}
	#comm {
		position: absolute;
    right: 88px;
    text-align: center;
    font-size: 9px;
    padding: 1px 2px;
    line-height: .9;
    border-radius: 10px;
	}
	#btn {border-radius:2px;}
	#view {border-radius:2px;}
	#all {margin-left:10px}
	#cat {margin-left: 18px;}
	#ar {margin-top: 21px;margin-left:10px}
	div[data-popover="right"] {margin-right:-66px;margin-top:3px;width:160px;}
	div[data-popover="left"] {margin-left:-75px;margin-top:3px;width:160px;}
	#ico {margin-right:-4px}
</style>