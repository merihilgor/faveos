<template>

	<div>

		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

			<div slot="title">

				<h4>{{lang(title)}}</h4>
			</div>

			<div v-if="!loading" slot="fields">

				<h5 v-if="title === 'delete'" id="H5">{{lang('are_you_sure_you_want_to_delete')}}</h5>

				<template v-else>

					<div v-html="data.description" id="desc"></div>
				</template>
			</div>

			<div v-if="loading" class="row" slot="fields" >

				<loader :animation-duration="4000" color="#1d78ff" :size="60" />
			</div>

			<div v-if="title === 'delete'" slot="controls">

				<button type="button" @click = "onSubmit" class="btn btn-danger" :disabled="isDisabled">

					<i class="fa fa-trash" aria-hidden="true"></i> {{lang('delete')}}
				</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import axios from 'axios';

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		name : 'vendor-modal',

		description : 'Vendor Modal component',

		props:{

			showModal : { type : Boolean, default : false},
			
			data : { type : Object, default : ()=>{}},

			title : { type : String, default : ''},

			onClose:{type: Function},
		},

		data:()=>({

			containerStyle : { width:'500px' },

			loading : false,

			isDisabled : false
		}),

		beforeMount(){

			if(this.title !== 'delete'){

				this.containerStyle.width = '800px';
			}
		},

		methods:{
		
			onSubmit(){
			
				this.loading = true
			
				this.isDisabled = true
			
				axios.delete('/service-desk/api/vendor/'+this.data.id).then(res=>{

					successHandler(res,'vendor-view');

					this.afterRespond();

					this.redirect('/service-desk/vendor')
				}).catch(err => {

					errorHandler(err,'vendor-view');

					this.afterRespond();
				})
			},

			afterRespond(){

				this.onClose();

				this.loading = false;

				this.isDisabled = true;

			}
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css">
	#H5{
		margin-left:16px;
	}
	#desc{
		overflow: auto;
		max-height: 250px;
		margin-left:16px;
		margin-right:16px;
	}
	.fulfilling-bouncing-circle-spinner{
		margin: auto !important;
	}
	#desc table {
    border-collapse: unset !important;
	}
</style>
