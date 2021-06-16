<template>
	
	<div>

		<a href="javascript:;"  ref="right_click_menu"  v-on:contextmenu=" openContextMenu($event)" v-tooltip="details.title">
			
			<span class="ticket-no" :data-pjax="details.id">[#{{details.ticket_number}}]&nbsp;&rlm;</span>
				
				{{subString(details.title,50)}}&nbsp;&rlm;(&lrm;{{details.thread_count}}) 
		</a>

		<a href="javascript:;" v-popover="{ name: 'details' + details.id }" @click="getPopData(details.id)">
		
			<i class="fa fa-question-circle" style="{fontSize:'medium', color:'#3c8dbc'}" 
			 	v-tooltip="lang('click_to_see_details')">
			 		
			</i>
		</a>
		<span v-if="details.attachment_count !== 0"> &nbsp;<i class="glyphicon glyphicon-paperclip"></i></span>

		<span v-if="details.collaborator_count !== null">&nbsp;<i class="fa fa-users"></i></span>

		<template v-for="(label,index) in details.labels">
								
			<a href="javascript:;" v-tooltip="lang(label.value)" class="label ticket_label"  
				:style="{'backgroundColor' : label.color, 'color': '#FFF'}">{{subString(label.value)}}
								
			</a>&nbsp;
		</template>

		<popover :name="'details'+details.id" :pointer="true" event="click">
			
			<template v-if="hasData">

				<div class="box box-widget widget-user-2 w_body">
					
					<div class="widget-user-header w_header" :style="bgStyle">
					
						<div class="widget-user-image">
							
							<faveo-image-element :source-url="details.from.profile_pic" :classes="['img-circle', 'w_35']"/>
						</div>
						
						<h3 class="widget-user-username w_name" 
							v-tooltip="popUpData.user_name ? popUpData.user_name : popUpData.email">
							{{popUpData.user_name ? popUpData.user_name : popUpData.email }}

						</h3>

						<h5 class="widget-user-desc w_time"><span>{{lang('created_at')}} :</span> 
							{{formattedTime(popUpData.created_at)}}
						</h5>
					</div>
					
					<div class="box-footer">
						
						<div class="w_content" v-html="popUpData.body"></div>
					</div>
				</div>
			</template>

			<template v-else>
				
				<div class="row" id="load_margin">
					
					<loader :animation-duration="4000" color="#1d78ff" :size="30"/>
				</div>
			</template>
		</popover>
	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics'

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {

		name : 'ticket-popover',

		props : {

			details : { type : Object, default : ()=> {} },

			tableHeader : { type : String, default : ''},

			onTicketClick : { type : Function }
		},

		data () {

			return {

				bgStyle : { background : this.tableHeader },

				hasData : false,

				popUpData : '',

				delay: 300,
				
				clicks: 0,
				
				timer: null
			}
		},

		computed:{
				
			...mapGetters(['formattedTime'])
		},

		methods : {

			subString(value,length = 10){

				return getSubStringValue(value,length)
			},

			getPopData(id) {

				this.hasData = false;

				axios.get('/ticket/tooltip?ticketid='+id).then(res=>{

					this.hasData = true;

					this.popUpData = res.data;

				}).catch(err=>{

					this.hasData = false;
				})
			},

			onClick(id){
				
				this.$store.dispatch('setTicketId', id);

				this.clicks++ 
				
				if(this.clicks === 1) {

					this.timer = setTimeout(()=> {
						
						this.clicks = 0

						this.onTicketClick(id)

					}, this.delay);
				} else{
					
					this.redirectMethod(id);
			 }        	
		  },

		  redirectMethod(id) {

		  		this.clicks = 0;

				clearTimeout(this.timer);  

				window.open(this.basePath()+'/thread/' + id, "_blank");
		  },

			openContextMenu(e){
				
				let titleEl=this.$refs.right_click_menu;
				
				titleEl.href = this.basePath()+"/thread/"+this.details.id;
			},
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>
	
	.vue-popover{ width: 60% !important; top:auto !important; left:auto !important; }

	.w_35 { width: 35px !important; height: 35px !important; margin-top: 1px;  }

	.w_name { color: white !important; margin-left: 45px !important; font-size: 17px !important; }

	.w_time { color: white !important; margin-left: 45px !important; font-size: 13px !important; }

	.w_header { padding: 10px !important; padding-bottom: 2px !important; }

	.w_body { border-top: 0px solid #d2d6de !important; margin-bottom: 0 !important;}

	#load_margin { margin-top: 15px;margin-bottom: 15px; }

	.w_content { font-weight: 500;font-size: 14px; }

	.ticket_label{ position: relative; top: -2px; font-size: 10px; }
</style>