<template>

	<div class="box box-widget widget-user-2" id="border_top">

		<div class="widget-user-header bg-yellow" style="padding: 11px;">
			 
			<div class="widget-user-image">
				
				<faveo-image-element id="org_man" :source-url="manager.profile_pic" :classes="['img-circle']" alternative-text="User Avatar"/>
			</div>		

			<a :href="basePath()+'/user/'+manager.id+'/edit'" v-tooltip="lang('edit')" class="pull-right text-white">

				<i class="fa fa-edit"></i>
			</a>
			
			<h3 class="widget-user-username" >
				<span v-tooltip="manager.full_name">{{subString(manager.full_name)}}</span> 
			</h3>
			
			<h5 class="widget-user-desc">{{lang('organization_manager')}}</h5>
		</div>
		
		<div class="box-footer no-padding">
			
			<ul class="nav nav-stacked">
				
				<li>

					<a>{{lang('email')}} 
						
						<span class="pull-right"  v-tooltip="manager.email">{{manager.email ? subString(manager.email) : '---'}}</span>
					</a>
				</li>
				
				<li>

					<a>{{lang('phone')}} 

						<span class="pull-right" v-tooltip="manager.phone_number">{{manager.phone_number ? subString(manager.phone_number) : '---'}}</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics';

	export default{

		name : '',

		description : '',

		props : {

			manager : { type : Object, default : ()=>{}}
		},

		methods : {

			subString(name,length = 15){
			
				return getSubStringValue(name,length)
			},
		},

		components: {
			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>
	#border_top{
		border-top: 0px solid #d2d6de !important;
	}
	.text-white {
    color: #fff !important;
	}
	#org_man{
		height: 65px !important;
	}
</style>