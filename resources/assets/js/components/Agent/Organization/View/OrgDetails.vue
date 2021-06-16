<template>
	
	<div>

		<div class="box box-widget widget-user" id="border_top">
				
			<template v-if="loading || !orgData">
				
				<div id="load" class="row">

					<loader :animation-duration="4000" :size="40"/>
				</div>
			</template>

			<template v-if="orgData && !loading">

				<div class="widget-user-header bg-aqua-active">
					
					<a :href="basePath()+'/organizations/'+orgData.id+'/edit'" v-tooltip="lang('edit')" class="pull-right text-white">

						<i class="fa fa-edit"></i>
					</a>

					<h3 class="widget-user-username" id="org_name">
						<span  v-tooltip="orgData.name">{{subString(orgData.name,20)}}</span></h3>
						
					<h5 class="widget-user-desc">{{lang('organization')}}</h5>
				</div>
					
				<div class="widget-user-image">
						
					<faveo-image-element id="org_img" :source-url="orgImage" :classes="['img-circle']" alternative-text="Organization Avatar"/>
				</div>
				
				<div class="box-footer" id="pad40">
						
					<button type="button" class="btn btn-primary btn-xs" @click="showModal = true">
						<i class="fa fa-plus"></i> {{lang('add_manager')}}
					
					</button>
				</div>

				<div class="box-footer" id="pad">
						
					<b>{{lang('domain')}}</b>
						
					<span class="pull-right" v-tooltip="orgData.domain">
						{{subString(orgData.domain ? orgData.domain : '---',20)}}
					</span>
				</div>

				<div class="box-footer" id="pad">
						
					<b>{{lang('address')}}</b>
						
					<span class="pull-right" v-tooltip="orgData.address">
						{{subString(orgData.address ? orgData.address : '---',30)}}
					</span>
				</div>

				<div class="box-footer" id="pad">
						
					<b>{{lang('phone')}}</b>
						
					<span class="pull-right" v-tooltip="orgData.phone">
						{{subString(orgData.phone ? orgData.phone : '---',20)}}
					</span>
				</div>

				<div class="box-footer" id="pad">
						
					<b>{{lang('description')}}</b>
						
					<span class="pull-right" v-tooltip="orgData.internal_notes">
						{{subString(orgData.internal_notes ? orgData.internal_notes : '---',30)}}
					</span>
				</div>

				<div v-for="value in orgData.custom_field_values" class="box-footer" id="pad">
						
					<b>{{value.label}}</b>

					<a class="pull-right" v-if="Array.isArray(value.value)" v-tooltip="value.value.toString()">
						&nbsp;{{value.value.toString()}}
					</a>

					<a v-else class="pull-right" v-tooltip="value.value"> {{subString(value.value,30)}}</a>
				</div>
			</template>
		</div>
		

		<transition name="modal">

			<org-manager-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :orgId="orgData.id"
			:managerList="managerList">

			</org-manager-modal>
		</transition>

	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics';

	import { mapGetters } from 'vuex'

	export default{

		name : '',

		description : '',

		props : {

			orgData : { type : String|Number, default : ''},

			managerList : { type : Array, default : ()=>[] },
		},

		data(){

			return {

				showModal : false,

				loading : false,

				orgImage : '',
			}
		},

		beforeMount(){

			this.orgImage = this.orgData.logo ? this.orgData.logo : 
											this.basePath()+'/themes/default/common/images/org.png'; 
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			subString(name,length = 15){
			
				return getSubStringValue(name,length)
			},

			onClose(){

				this.showModal = false;

				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			'org-manager-modal' : require('./MiniComponents/OrgManagerModal.vue'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
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
	#pad{
		padding-top: 15px !important;
	}
	#pad40{
		padding-top: 40px !important;
		text-align: center;
	}
	#org_img{
		height: 90px !important;
		border: 1px solid #c1c1c1;
		background: white;
	}
	#org_name {
		font-size: 25px !important;
	}
</style>