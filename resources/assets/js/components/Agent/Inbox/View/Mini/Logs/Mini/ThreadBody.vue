<template>
	
	<li>

		<i :class="thread.thread_type_icon_class" v-tooltip="thread.thread_type_text"></i>

		<div class="timeline-item">

			<span class="time thread_right">

				<span v-tooltip="lang('reply_rating')" class="thread_rating">

					<rating-component v-for="rating in ratings" :key="rating.id" :rating="rating" :labelStyle="labelStyle">
					
					</rating-component>
				</span>
				
				<span v-if="ratings.length > 0" class="mt_4">&nbsp;&#124;&nbsp;&nbsp;</span>

				<a v-if="thread.is_internal !== 1" class="pointer" v-tooltip="lang('reply_to_this_thread')"
					@click="replyThread(thread.body,thread.user.name,thread.created_at)">

					<i class="fa fa-reply"></i>
				</a>

				<span v-if="thread.is_internal !== 1" class="mt_4">&nbsp;&nbsp;&#124;&nbsp;&nbsp;</span>

				<span class="mt_4"><i class="fa fa-clock-o"></i> {{formattedTime(thread.created_at)}}</span>
			</span>

			<h3 class="timeline-header">

				<img :src="thread.user.profile_pic" class="img-circle img-bordered-sm" alt="User Image" width="25" 
					height="25" id="thread_img">

				<a :href="basePath()+'/user/'+thread.user.id">{{thread.user.name}} </a>
									
			</h3>

			<div class="timeline-body ck-content">
								
				<span v-html="thread.body" id="thread_desc"></span>
			</div>

			<div class="timeline-footer" style="margin-bottom:-5px">

				<ul class="mailbox-attachments clearfix">

					<template v-for="(attachment,index) in thread.attach">
						
						<template v-if="attachment.poster=='ATTACHMENT'">
							
							<attachment :attachment="attachment"></attachment>
						</template>
					</template>
				</ul>
			</div>
		</div>
	</li>
</template>

<script>
	
	import { mapGetters } from 'vuex';

	export default { 

		name : 'thread-body',

		description : 'Thread Body Component',

		props : {

			thread : { type : Object, default : ()=> {} },

			index : { type : String | Number, default : '' },
		},

		data() {

			return {

				ratings:[],

				labelStyle : { display : 'none' }
			}
		},

		computed:{

			...mapGetters(['formattedTime','getRatingTypes'])
		},

		beforeMount() {
			
			this.ratingTypes(this.getRatingTypes)
		},

		methods : {

			replyThread(data,name,date) {

				const quote = 'On ' + this.formattedTime(date) + ' ' +'<b>'+ name + '</b> ' + 'wrote :';
				
				var content;

				content = "<p>&nbsp;</p><blockquote>"+quote+"<figure  id='mine'>"+data+"</figure></blockquote>";

				this.thread['content'] = content;

				window.eventHub.$emit('threadReply', this.thread);
			},

			ratingTypes(types) {

				this.ratings = types;

				var ratingArr=[];

				if(this.thread.ratings.length != 0){

					for(var i in this.ratings){

						for(var j in this.thread.ratings){

							if(this.ratings[i].id == this.thread.ratings[j].rating_id){

								if(this.ratings[i].rating_area == 'Comment Area'){

									this.ratings[i]['rating_value']=this.thread.ratings[j].rating_value;

									this.ratings[i]['ticket_id']=this.thread.ticket_id;

									ratingArr.push(this.ratings[i])

								}
							}
						}
					}
				}
				this.ratings=ratingArr;
			},
		},

		components : {

			'rating-component':require('components/MiniComponent/RatingComponent'),

			'attachment':require('components/MiniComponent/AttachmentBlock'),
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

.scrollable-ul { padding: 1rem; height: 80%; overflow: auto; }

#thread_img{ margin-top: -3px; }

.pointer { cursor: pointer;color: #999 !important; }

.thread_rating { margin-top: -13px; }

.thread_right { display: flex; margin-top: 5px; }

.mt_4 { margin-top: -4px; }

.rating_dropdown { 
	background: transparent !important;
    border: none !important;
    box-shadow: unset !important;
    margin-top: -5px;
    font-size: 14px !important;
}

.rating_body {

	width: max-content;
    left: unset !important;
    right: -1px !important;
}
</style>

<style>
	
	blockquote p { font-size: 14px !important; }
</style>