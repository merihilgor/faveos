<template>
	
	<div>
		
		<alert componentName="UserExport"/>

		<div class="box box-primary">
		
			<div class="box-header with-border">
			
				<h3 class="box-title">{{lang('users')}}</h3>
			</div>
			
			<div class="box-body">
			
				<div v-if="!loading" class="row">
			
					<date-time-field :label="lang('select_date_range')"
						:value="date_range"
						type="date"
						name="date_range"
						:required="false"
						:onChange="onChange" range
						:currentYearDate="false"
						format="YYYY-MM-DD" classname="col-xs-6"
						:clearable="true" :editable="true" :disabled="false" :pickers="getPickers()">
					</date-time-field>

					<a class="btn btn-primary" @click="onSubmit()" id="range-submit">
					
						<i class="fa fa-save"></i> {{lang('submit')}}
					</a>
				</div>

				<div v-if="loading" class="row">
					
					<div class="col-md-12 col-sm-12 col-xs-12">

						<loader :animation-duration="4000" :size="60"/>
					</div>
				</div>
		</div>
	</div>
	</div>
</template>

<script>
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	import moment from 'moment'

	export default {

		name : 'user-export',

		description : 'User export component',

		data() {

			return{

				date_range :'',

				start : '',

				end : '',

				loading : false,
			}
		},

		methods : {

			getPickers(){

				var date = new Date(); 

				let pickers = [

					{ text: 'Today', start : date, end : date },

					{ text: 'Yesterday', start : date - 3600 * 1000 * 24, end : date - 3600 * 1000 * 24 },
						
					{ text: 'Last 7 days', start : date - 3600 * 1000 * 24 * 7, end : date },
					
					{ text: 'Last 30 days', start : date - 3600 * 1000 * 24 * 30, end : date },
					
					{ text: 'Next 7 days', start : date, end : new Date(Date.now() + 3600 * 1000 * 24 * 7) },
					
					{ text: 'Next 30 days', start : date, end : new Date(Date.now() + 3600 * 1000 * 24 * 30) },
					
					{ text: 'This month', start : new Date(date.getFullYear(), date.getMonth(), 1), 
																end : new Date(date.getFullYear(), date.getMonth() + 1, 0)
					},

					{
						text: 'Last month', start : new Date(date.getFullYear(), date.getMonth() -1 , 1), 
																end : new Date(date.getFullYear(), date.getMonth()-1 + 1, 0)
					},
				]

				return pickers;
			},

			onChange(value,name){

				this[name] = value;

				this.start = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD') : '';

				this.end =  value[1] !== null ? moment(value[1]).format('YYYY-MM-D') : '';	
			},

			onSubmit(){

				this.loading = true;

				axios.get('/user-export-download?start_date='+this.start+'&end_date='+ this.end).then(res=>{

					this.loading = false;

					window.location.href = this.basePath() + '/user-export-download?start_date='+this.start+'&end_date='+ this.end;
				
				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'UserExport')
				})
			}
		},

		components : {

			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),

			"alert": require("components/MiniComponent/Alert"),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>
	
	#range-submit{
		margin: 24px;
		cursor: pointer;
	}
</style>