<template>
	<ul v-if="userInfo" class="timeline">
		<li>
			<div class="timeline-item" id="timeline" v-on:mouseenter="showButton(comment.id)" v-on:mouseleave="hideButton(comment.id)">
				<span id="ar" v-if="lang=='ar'" class="pull-left">
					<a href="javascript:void(0)" v-popover:right><i class="fa fa-gears"></i>{{lang('user_info')}}</a>
					<popover v-if="showId == comment.id" name="right" style="top:!42px;right:!917px;width:!167px">
						<p><i class="fa fa-user"></i>&nbsp;&nbsp;
							<a href="javascript:void(0)" :title="comment.name">{{comment.name | snippet}}</a>
						</p>
						<p><i class="fa fa-envelope"></i>&nbsp;
							<a href="javascript:void(0)" :title="comment.email">{{comment.email | snippet}}</a>
						</p>
						<p v-if="comment.website != ''"><i class="fa fa-globe"></i>&nbsp;
							<a :href="comment.website" target="_blank" :title="comment.website">{{comment.website | snippet}}</a>
						</p>
					</popover>
				</span>
				<span v-else class="time" style="margin-right:10px">
					<a href="javascript:void(0)" v-popover:left><i class="fa fa-gears"></i>{{lang('user_info')}}</a>
					<popover v-if="showId == comment.id" name="left" style="top:!38px;left:!912px;width:!167px">
						<p><i class="fa fa-user"></i>&nbsp;&nbsp;
							<a href="javascript:void(0)" :title="comment.name">{{comment.name | snippet}}</a>
						</p>
						<p><i class="fa fa-envelope"></i>&nbsp;
							<a href="javascript:void(0)" :title="comment.email">{{comment.email | snippet}}</a>
						</p>
						<p v-if="comment.website != ''"><i class="fa fa-globe"></i>&nbsp;
							<a :href="comment.website" target="_blank" :title="comment.website">{{comment.website | snippet}}</a>
						</p>
					</popover>
				</span>
				<h3 class="timeline-header" style="border-top: 1px solid #f2f2f2;">
					<faveo-image-element id="proimg" :source-url="comment.profile_pic" :classes="['img-circle']" alternative-text="User Image"/>
					<a id="name" v-if="comment.name">{{comment.name | to-up}}</a>
					<span class="time" id="span">Posted - {{formattedTime(comment.created_at)}}</span>
				</h3>
				<div id="status0" v-if="comment.status == 0" class="timeline-body">
					<h5 id="h5">
						<i class="fa fa-newspaper-o"></i>&nbsp; 
						<a id="articlename" :href="'show/'+article.slug" v-for="(article,index) in comment.article" target="_blank">{{article.name | to-up}} 
							<span v-if="comment.article.length != index+1"> | </span>
						</a>
					</h5>
					<p v-html="convert(comment.comment)" style="word-wrap: break-word;"></p>
				</div>
				<div id="status1" v-if="comment.status == 1" class="timeline-body">
					<h5 id="h5">
						<i class="fa fa-newspaper-o"></i>&nbsp; 
						<a id="articlename" :href="'show/'+article.slug" v-for="(article,index) in comment.article" target="_blank">{{article.name | to-up}}
							<span v-if="comment.article.length != index+1"> | </span>
						</a>
					</h5>
					<p v-html="convert(comment.comment)" style="word-wrap: break-word;"></p>
				</div>
				<div id="footer" class="timeline-footer" v-for="(article,index) in comment.article" >
					<hr id="hr">
					<a id="btn" v-if="comment.status == 0" :disabled="isDisabled" @click="publish(comment.id)"  class="btn btn-default btn-xs"><i class="fa fa-check-circle"></i> {{lang(btn1)}} </a>
					<a id="btn" v-if="comment.status == 1" :disabled="isDisabled" @click="unapprove(comment.id)"  class="btn btn-default btn-xs"><i class="fa fa-check-circle"></i> {{lang(btn2)}} </a>
					<a id="views" @click="viewArticle(article.id)" target="_blank" :href="'show/'+article.slug" class="btn btn-default btn-xs"><i class="fa fa-eye"></i> {{lang('view')}} </a>
					<a id="btn" href="javascript:void(0)" class="btn btn-default btn-xs" data-toggle="modal" @click="getDeleteId(comment.id)" data-target="#deletelib"><i class="fa fa-trash"></i> {{lang('delete')}} </a>

				</div>
			</div>
		<!-- popup for media delete -->
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
		props:['comment'],
		data() {
			return {
				isDisabled:false,
				showId:null,
				deleteId:'',
				user_data:'',
				langs:'',
				btn1:'approve',
				btn2:'unapprove'
			}
		},
		created() {

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
		filters : {
			// for capitialize first letter
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
				var x = localStorage.getItem('Comment');
				axios.get('comment/delete/'+x).then(res=>{
					window.scrollTo(0, 0);
					window.eventHub.$emit('ErrorAlert');
				})	
				
			},
			//test
			getDeleteId(x){
                this.deleteId=x;
                localStorage.setItem('Comment',this.deleteId)
			},
			// for view comment
			viewArticle(article) {
				this.$store.dispatch('setArticleId', article);
			},
			publish(id) {
				this.isDisabled = true;
				this.btn1 = 'working...';
				axios.get('published/'+id).then(res=>{
				this.isDisabled = false;
				this.btn1 = 'approve';
					window.scrollTo(0, 0);
					window.eventHub.$emit('ShowAlert','comment_approved');
							})
			},
			unapprove(id) {
				this.isDisabled = true;
				this.btn2 = 'working...';
				axios.get('unapprove/'+id).then(res=>{
				this.isDisabled = false;
				this.btn2 = 'unapprove';
					window.scrollTo(0, 0);
				window.eventHub.$emit('ShowAlert','comment_unapproved');
							})
			},
			convert(x) {
				if(x){
		            return x.replace(/(<\/?(?:img)[^>]*>)|<[^>]+>/ig, '$1');
		      	}
			},
		},

		components :{

			'delete-popup': require('components/Agent/kb/common/deletePopup'),
			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>
<style scoped>
	#timeline {
		margin-right: -16px;
		margin-left:0px
	}
	#proimg {
		border: 1px solid #fff;border-radius: 52%;width: 34px;margin-bottom: 7px;
	}
	#name {
		margin-left:5px
	}
	#span {
		color: #999;font-size: 13px;line-height:2;
	}
	#status0 {
		word-wrap:break-word;background:#fef7f1;border-bottom: 1px solid #eee;
	}
	#status1 {
		word-wrap:break-word;border-bottom: 1px solid #eee;
	}
	#articlename {
		font-weight:700
	}
	#footer {
		margin-top:-30px
	}
	#hr {
		margin-top: 20px;
    margin-bottom: 9px;
    border: 0;
	}
	#h5 { margin-top: 2px; }
	#btn {border-radius:2px;}
	#views {border-radius:2px;}
	#ar {margin-top: 21px;margin-left:10px}
	div[data-popover="right"] {margin-right:-66px;margin-top:3px;width:160px;}
	div[data-popover="left"] {margin-left:-75px;margin-top:3px;width:160px;}
</style>