<template>
	
	<div>
	
		<div class="row" v-if="release.length > 0">

			<div class="col-md-12">

				<div class="callout callout-info">

					<div class="row">

						<div class="col-md-4" :title="formattedTime(release[0].created_at)">

							<b>{{lang('created_date')}} : </b> {{formattedTime(release[0].created_at)}}
						</div>

						<div class="col-md-4" :title="release[0].planned_start_date && release[0].planned_start_date !== '--' && release[0].planned_start_date !== '0000-00-00 00:00:00' ? 
							formattedTime(release[0].planned_start_date) : '--'">

							<b>{{lang('planed_start_date')}} : </b> 

							{{release[0].planned_start_date && release[0].planned_start_date !== '--' && release[0].planned_start_date !== '0000-00-00 00:00:00' ? formattedTime(release[0].planned_start_date) : '--'}}
						</div>

						<div class="col-md-4" :title="release[0].planned_end_date && release[0].planned_end_date !== '--' && release[0].planned_end_date !== '0000-00-00 00:00:00' ? formattedTime(release[0].planned_end_date) : '---'">

							<b>{{lang('planed_end_date')}} : </b> 

							{{release[0].planned_end_date && release[0].planned_end_date !== '--' && release[0].planned_end_date !== '0000-00-00 00:00:00' ? formattedTime(release[0].planned_end_date) : '---'}}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div v-if="release.length > 0" class="row">

			<div class="col-md-12">

				<div class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('subject') }}</label></div>

					<div class="col-md-6">

						<a :title="release[0].subject" href="javascript:void(0)">{{subString(release[0].subject)}}
						</a>
					</div>

				</div>

				<div class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('status') }}</label></div>

					<div class="col-md-6">

						<a :title="release[0].status.name" href="javascript:void(0)">{{subString(release[0].status.name)}}
						</a>
					</div>

				</div>

				<div class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('priority') }}</label></div>

					<div class="col-md-6">

						<a :title="release[0].priority.name" href="javascript:void(0)">
						{{subString(release[0].priority.name)}}</a>
					</div>

				</div>

				<div class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('location') }}</label></div>

					<div class="col-md-6">

						<a :title="release[0].location ? release[0].location.name : '---'" href="javascript:;">
							{{release[0].location ? subString(release[0].location.name) : '---'}}
						</a>
					</div>
				</div>

				<div class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('release_type') }}</label></div>

					<div class="col-md-6">

						<a :title="release[0].release_type.name" href="javascript:void(0)"> 
							{{subString(release[0].release_type.name)}}
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="row" v-else>
			
			<h4 class="text-center">{{lang('no_data_found')}}</h4>
		</div>
	</div>
</template>

<script>

	import { getSubStringValue } from 'helpers/extraLogics'

	import { mapGetters } from 'vuex'

	export default {

		name : 'change-release',

		description : 'Release description page',

		props : {

			release : { type : Array, default : ()=>[]}
		},

		computed :{

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			subString(value,length = 15){

				return getSubStringValue(value,length)
			},
		}
	};
</script>

<style scoped>
	
	.info-row {
    border-top: 1px solid #f4f4f4;
    padding: 10px;
	}
</style>