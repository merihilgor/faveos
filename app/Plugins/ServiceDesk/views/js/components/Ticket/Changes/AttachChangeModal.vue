<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
	
		<alert componentName="change-attach-modal"/>

		<div slot="title">
			
			<h4>{{lang(title)}}</h4>
			
		</div>
			
		<!-- adding new change -->

		<div slot="fields" v-if="title === 'create_a_change_to_this_ticket'" id="create-box">

			<div class="row" v-if="createLoader" id="load_margin">

				<loader :duration="4000"></loader>
			</div>

			<change-create v-else ref="attachChange" from="attach-change-modal" :onComplete="onCompleted" 
			alertName="timeline" :ticket_id="ticketId">
				
			</change-create>	

		</div>

		<!-- adding existing change -->

		<div slot="fields" v-if="title === 'link_to_an_existing_change'" id="dynamic_select">

			<div class="row">
				
				<dynamic-select :label="lang('Changes')" :multiple="false" 
					name="change" :prePopulate="true"
					classname="col-xs-12" :apiEndpoint="'/service-desk/api/dependency/changes_based_on_ticket?meta=true&ticket_id='+ticketId" 
					:value="change" :onChange="onChange" :required="true" :clearable="change ? true : false">
				</dynamic-select>	
			</div>
		</div>

		<div class="row" slot="fields" v-if="loading === true">

			<custom-loader :duration="4000"></custom-loader>
		</div>
						
		<div slot="controls">
			
			<button type="button" id="submit_btn" @click = "onSubmit" class="btn btn-primary" :disabled="isDisabled">
				
				<i class="fa fa-paperclip"></i> {{lang('attach')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {
		
		name : 'attach-change-modal',

		description : 'Attach change Modal component',

		props:{
			
			showModal : { type : Boolean, default : false },

			title : { type : String, default : '' },

			type : { type : String, default : '' },

			onClose : { type : Function },

			data : { type : Object|String, required : true}

		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle:{ width:'800px' },

				loading:false,

				change : '',

				createLoader : false
			}
		},

		computed:{
			
			ticketId(){
			
				return JSON.parse(this.data).id;
			}
		},

		beforeMount(){
			
			this.isDisabled = this.title === 'link_to_an_existing_change' && !this.change ? true : false

			if(this.title == 'create_a_change_to_this_ticket'){

				this.createLoader = true;

				setTimeout(()=>{

					this.createLoader = false;
				},1000)
			}
		},

		methods:{
			
			onSubmit(){
			
				if(this.title === 'link_to_an_existing_change'){
					
					this.loading = true;

					const data = {};

					data['ticket_id'] = this.ticketId;

					data['change_id'] = this.change.id;

					data['type'] = this.type
			
					axios.post('/service-desk/api/attach-change/ticket',data).then(res=>{
				
						successHandler(res,'timeline')
				
						this.onCompleted()
					
					}).catch(error=>{
						
						errorHandler(error,'change-attach-modal')
						
						this.loading = false
					
					})
				} else {

					this.$refs.attachChange.onSubmit();
				}
			},

			onCompleted(){
				
				this.loading = false

				window.eventHub.$emit('AssociatedChangerefreshData');

	        window.eventHub.$emit('refreshActions');

				this.onClose();
			},

		onChange(value,name){
			
			this[name] = value
			
			this.isDisabled = !value ? true : false;		
		}
	},

	components:{

		'modal':require('components/Common/Modal.vue'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		'custom-loader' : require('components/MiniComponent/Loader'),

		'loader' : require('components/Client/Pages/ReusableComponents/Loader'),
		
		'change-create' : require('../../Agent/Changes/ChangesCreateEdit.vue'),
		
		"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
	
	}

};
</script>

<style type="text/css">
	
	#H5{
		margin-left:16px; 
	}

	#dynamic_select{
		
		margin-left: 15px;
		margin-right: 15px;
	}

	#create-box{
		max-height : 350px;
		overflow-y : scroll;
		overflow-x : hidden;
	}

	#alert_top{
		margin-top:20px
	}

	#alert_msg {
		margin: 0px 5px 30px 5px;
	}

	#load_margin { margin-top: 50px; margin-bottom: 50px; }
</style>