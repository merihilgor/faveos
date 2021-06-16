<template>

	<div class="pull-right">

		<div class="btn-group">

			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">

			 <i class="fa fa-paperclip"> </i> {{trans('associate_services')}} <span class="caret"></span>
			</button>

			<ul class="dropdown-menu">

				<li>
					
					<a href="javascript:;" @click="associateMethod('tickets')">{{trans('attach_tickets')}}</a>
				</li>

				<li>
					
					<a href="javascript:;" @click="associateMethod('problems')">{{trans('attach_problems')}}</a>
				</li>

				<li>
					
					<a href="javascript:;" @click="associateMethod('changes')">{{trans('attach_changes')}}</a>
				</li>

				<li>
					
					<a href="javascript:;" @click="associateMethod('releases')">{{trans('attach_releases')}}</a>
				</li>

				<li>
					
					<a href="javascript:;" @click="associateMethod('contracts')">{{trans('attach_contracts')}}</a>
				</li>	    
			</ul>
		</div>
		
		<a :href="basePath()+'/service-desk/assets/'+asset.id+'/edit'" class="btn btn-default"> 

			<i class="fa fa-edit"> </i> {{trans('edit')}}
		</a>

		<button class="btn btn-default" @click="showDeleteModal = true">
      	
      	<i class="fa fa-trash"> </i> {{trans('delete')}}
    	</button>

		<transition name="modal">

		 	<delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
				alertComponentName="asset-view" :deleteUrl="'/service-desk/api/asset-delete/' + asset.id" 
				redirectUrl="/service-desk/assets">

		 	</delete-modal>
		</transition>

		<transition name="modal">

			<asset-associate-modal  v-if="showAssociateModal" :onClose="onClose" :showModal="showAssociateModal" 
				:assetId="asset.id" :associate="associate">

			</asset-associate-modal>
		</transition>
	</div>
</template>

<script>

	import { getSubStringValue } from 'helpers/extraLogics'

	import axios from 'axios'

	export default {

		name : 'asset-actions',

		description : 'Asset actions component',

		props : {

			asset : { type : Object, default : ()=> {}}
		},

		data() {

			return {

				showDeleteModal : false,

				showAssociateModal : false,

				associate : ''
			}
		},

		methods : {

			associateMethod(associate){

				this.showAssociateModal = true;

				this.associate = associate;
			},

			onClose(){

				this.showDeleteModal = false;

				this.showAssociateModal = false;
			}
		},

		components : {

			'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),

			'asset-associate-modal' : require('./AssetAssociateModal'),
		}
	};
</script>

<style scoped>

	#more_actions {
		right: 0 !important;
		left: unset !important;
	}
</style>