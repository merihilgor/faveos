<template>

	<div>

		<modal v-if="showModal" :showModal="showModal" :onClose="onClose">

			<div slot="title" id="alert_top">

				<alert componentName="change-ticket"/>
				
			</div>
			
			<div slot="title">

				<h4>{{associate === 'initiating' ? lang('associate_an_initiating_ticket') : lang('associate_a_causing_ticket')}}</h4>
			</div>

			<div slot="fields" v-if="!loading">

				<div class="row">

					<div class="col-xs-12">
						
						<dynamic-select :label="lang('tickets')" :multiple="true" name="ticket_ids" :prePopulate="false"
							classname="col-xs-12" :apiEndpoint="'/service-desk/api/dependency/tickets_based_on_change?change_id='+changeId" :value="ticket_ids" :onChange="onChange" :required="true">

						</dynamic-select>
					</div>
				</div>
			</div>

			<div v-if="loading" class="row" slot="fields" >

        <loader :animation-duration="4000" :size="60"/>
			</div>

			<div slot="controls">

				<button type="button" id="submit_btn" @click = "onSubmit()" class="btn btn-primary" :disabled="ticket_ids.length > 0 ? false : true">

          <i class="fa fa-paperclip"></i> {{lang('attach')}}</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from "axios"

	export default {

		name : 'change-ticket-associate',

		description : 'Change ticket associate Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

      changeId : { type : String | Number, default : ''},

      associate : { type : String , default : ''},
		},

		data(){

			return {

				ticket_ids : [],

				loading : false,
			}
		},

		methods : {

			onChange(value, name) {

				this[name] = value;
			},

			onSubmit() {

				this.loading = true 

				const data = {};

				data['change_id'] = this.changeId;

				var ticketIds = [];

				for(var i in this.ticket_ids){

					ticketIds.push(this.ticket_ids[i].id)
				}

				data['ticket_ids'] = ticketIds;
				
				data['type'] = this.associate;

				axios.post('/service-desk/api/attach-ticket/change',data).then(res => {

					window.eventHub.$emit('ChangeAssociatesAction','tickets');

					this.loading = false
					
					this.ticket_ids = [];
					
					this.onClose();

					window.eventHub.$emit('updateActivity');

					window.eventHub.$emit('ChangeTicketsrefreshData');

					successHandler(res,'changes-view')
						
				}).catch(err => {
						
					this.loading = false
						
					errorHandler(err,'change-ticket')
				});
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

			'alert' : require('components/MiniComponent/Alert'),

      "dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css">
	
	#alert_top{
		margin-top:20px
	}

	#alert_msg {
		margin: 0px 5px 30px 5px;
	}
</style>