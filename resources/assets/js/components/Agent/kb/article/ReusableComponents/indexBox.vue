<template>
	<ul v-if="userInfo" class="timeline">
		<li>
			<div class="timeline-item" id="timeline" v-on:mouseenter="showButton(article.id)" v-on:mouseleave="hideButton(article.id)">
				<span v-if="lang=='ar'" id="ar" class="pull-left">
					<a href="javascript:void(0)" v-popover:right><i class="fa fa-gears"></i> Article Info</a>
						<popover v-if="showId == article.id" name="right" style="top: !42px;left:!927px;width:!160px">
							<p v-if="article.author"><i class="fa fa-user"></i>&nbsp;&nbsp;
								<a href="javascript:void(0)" v-if="article.author.first_name" >{{article.author.first_name | snippet}}  {{article.author.last_name | snippet}} </a>
								<a href="javascript:void(0)" v-else>{{article.author.user_name}} </a>
							</p>
							<p><i class="fa fa-newspaper-o"></i>&nbsp;
								<strong> Categories  </strong> 
								<a href="javascript:void(0)" v-for="(category,index) in article.categories.slice(0,20)"><li id="cat">{{category.name | snippet}} </li>
								</a>
							</p>
							<p><i class="fa fa-newspaper-o"></i>&nbsp;
								<strong> Tags : </strong> 
								<a href="javascript:void(0)" v-for="(tag,index) in article.tags.slice(0,20)"><li id="tag" :title="tag.name">{{tag.name | snippet}} </li>
								</a>
							</p>
						</popover>
					</span>
					<span v-else class="time">
					<a href="javascript:void(0)" v-popover:left><i class="fa fa-gears"></i> Article Info</a>
						<popover v-if="showId == article.id" name="left" style="top: !42px;left:!927px;width:!160px" >
							<p v-if="article.author"><i class="fa fa-user"></i>&nbsp;&nbsp;
								<a href="javascript:void(0)" v-if="article.author.first_name" >{{article.author.first_name | snippet}}  {{article.author.last_name | snippet}} </a>
								<a href="javascript:void(0)" v-else>{{article.author.user_name}} </a>
							</p>
							<p><i class="fa fa-newspaper-o"></i>&nbsp;
								<strong> Categories : </strong> 
								<a href="javascript:void(0)" v-for="(category,index) in article.categories.slice(0,20)"><li id="cat" :title="category.name">{{category.name | snippet}} </li>
								</a>
							</p>
							<p><i class="fa fa-newspaper-o"></i>&nbsp;
								<strong> Tags : </strong> 
								<a href="javascript:void(0)" v-for="(tag,index) in article.tags.slice(0,20)"><li id="tag" :title="tag.name">{{tag.name | snippet}} </li>
								</a>
							</p>
						</popover>
					</span>

					<h3 class="timeline-header" style="border-top:1px solid #f2f2f2;">
						<a :href="'show/'+article.slug" target="_blank" style="word-break: break-all;">{{article.name | to-up}}</a>
						<div>
							<span id="span" class="time">Published - {{formattedTime(article.publish_time)}}</span>
						</div>
					</h3>
					<div id="status1" class="timeline-body" v-html="convert(article.description)"></div>
					<div class="timeline-footer" >
						<span v-if="lang=='ar'" id="commentsar" class="time pull-left">
							<a :href="'comment'"  @click="pendingComments(article.id,article.name)"><i id="ico" class="fa fa-commenting-o" title="pending comments"></i>
								<span v-if="article.pending_comments_count < 10" id="commar" class="label label-danger">{{article.pending_comments_count}}</span>
								<span v-else id="commar" class="label label-danger">9+</span>
								<span >{{article.all_comments_count}} comments</span>
							</a>
							<a :href="'comment'"  @click="allComments(article.id,article.name)">
								<span  title="total comments">{{article.all_comments_count}} comments</span>
							</a>
						</span>
						<span v-else id="comments" class="time pull-right">
							<a :href="'comment'"  @click="pendingComments(article.id,article.name)"><i id="ico" class="fa fa-commenting-o" title="pending comments"></i>
								<span v-if="article.pending_comments_count < 10" id="comm" class="label label-danger" title="pending comments">{{article.pending_comments_count}}</span>
								<span v-else id="comm" class="label label-danger" title="pending comments">9+</span>
							</a>
							<a :href="'comment'" @click="allComments(article.id,article.name)">
								<span id="all" title="total comments">{{article.all_comments_count}} comments</span>
							</a>
						</span>
						<a id="btn" :href="'article/'+article.id+'/edit'" class="btn btn-default btn-xs"><i class="fa fa-edit"></i> Edit</a> 
						<a v-if="status==1" id="view"  target="_blank" @click="viewArticle(article.id)" :href="'show/'+article.slug" class="btn btn-default btn-xs" style="word-break: break-all;"><i class="fa fa-eye"></i> View</a> 
						<a id="btn" href="javascript:void(0)" class="btn btn-default btn-xs" data-toggle="modal" data-target="#deletelib" :disabled="isDisabled" @click="getDeleteId(article.slug)"><i class="fa fa-trash"></i> {{btn}}</a>
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
import { mapGetters } from 'vuex'
export default {
	props:['article','status'],
	data() {
		return {
			showId:null,
			deleteId:'',
			user_data:'',
			lang:'',
			btn:'Delete',
			isDisabled:false
		}
	},
	filters : {
	// to capitalize for letter
		toUp(value){
			return value.charAt(0).toUpperCase() + value.slice(1);
		},
	// showing only 25 characters if the length is more than 50
				snippet(value) {
			if(value){
					if(value.length>15){
						return value.substring(0,20) + '...';
					} else {
						return value;
					}
				}
		}
	},
	created(){
		this.$store.dispatch('commentFilterData',{});
	},
	computed: {
     // getting user data
     	userInfo(){
			if(this.$store.getters.getUserData){
				this.user_details(this.$store.getters.getUserData);
				return this.$store.getters.getUserData;
			}
		},
...mapGetters(['formattedTime','formattedDate'])
	},	
	methods: {
	// getting user data
		user_details(user) {
			this.user_data = user.user_data;
			this.lang=this.user_data.user_language;
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
			var x = localStorage.getItem('Article');
				axios.delete('article/delete/'+x).then(res=>{
			this.btn ='Delete';
			this.isDisabled = false;
					window.scrollTo(0, 0);
					window.eventHub.$emit('ShowAlert');
							})
		},
//test
		getDeleteId(x){
      this.deleteId=x;
      localStorage.setItem('Article',this.deleteId)
		},
// for view comment
		viewArticle(article) {
			this.$store.dispatch('setArticleId', article);
		},
// showing only 250 characters in article description
		convert(x) {
			if(x){
		          if(x.length>250){
		            // return $("<p />", { html: x }).text().replace(/(<\/?(?:a|p|blockquote)[^>]*>)|<[^>]+>/ig, '').substring(0,250) + '....';
		             return x.replace(/(<\/?(?:a|p|blockquote)[^>]*>)|<[^>]+>/ig, '').substring(0,250) + '....';
		          } else {
		            // return $("<p />", { html: x }).text().replace(/(<\/?(?:a|p|blockquote)[^>]*>)|<[^>]+>/ig, '');
		             return x.replace(/(<\/?(?:a|p|blockquote)[^>]*>)|<[^>]+>/ig, '');
		          }
		        }
		},
		allComments(id,name){
			const data = {to:'all',id:id ,name:name}
			this.$store.dispatch('commentFilterData',data);
		},
		pendingComments(id,name){
			const data = {to:'unapprove',id:id ,name:name}
			this.$store.dispatch('commentFilterData',data);
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
    text-align: center;
    font-size: 9px;
    padding: 1px 2px;
    line-height: .9;
    border-radius: 10px;
	}
	#btn {border-radius:2px;}
	#view {border-radius:2px;}
	#all {margin-left:10px}
	#cat,#tag {margin-left: 18px;}
	#ar {margin-top: 21px;margin-left:10px}
	div[data-popover="right"] {margin-right:-66px;margin-top:3px;width:160px;}
	div[data-popover="left"] {margin-left:-75px;margin-top:3px;width:160px;}
	#ico {margin-right:-4px}
</style>