<template>
	
	<div>
	
		<div class="row" v-if="!Array.isArray(change)">

			<div class="col-md-12">

				<div class="callout callout-info">

					<div class="row">

	              <div class="col-md-4">

	                <b>{{lang('created_date')}} : </b> 

	                <span  v-tooltip="formattedTime(change.created_at)"> {{formattedTime(change.created_at)}} </span>
	              </div>

	              <div class="col-md-4">

	                <b>{{lang('status')}} : </b> 

	                <span v-tooltip="change.status.name"> {{subString(change.status.name)}}</span>
	              </div>

	              <div class="col-md-4">

	                <b>{{lang('requester')}} : </b> 

	                <span v-tooltip="change.requester.full_name ? change.requester.full_name : change.requester.email"> {{change.requester.full_name ? subString(change.requester.full_name) : subString(change.requester.email)}}
	                </span>
	              </div>
	            </div>
				</div>
			</div>
		</div>

		<div v-if="!Array.isArray(change)" class="row">

			<div class="col-md-12">

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('subject') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="change.subject" href="javascript:void(0)">{{subString(change.subject)}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('change_type') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="change.change_type.name" href="javascript:void(0)">{{change.change_type.name}}</a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('impact') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="change.impact_type ? change.impact_type.name : '---'" href="javascript:void(0)"> 
                {{change.impact_type ? subString(change.impact_type.name) : '---'}}
              </a>
            </div>

          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('location') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="change.location ? change.location.title : '---'" href="javascript:void(0)">
                {{change.location ? subString(change.location.title) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('department') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="change.department ? change.department.name : '---'" href="javascript:void(0)"> 
                {{change.department ? subString(change.department.name) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('team') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="change.team ? change.team.name : '---'" href="javascript:void(0)">
                {{change.team ? subString(change.team.name) : '---'}}
              </a>
            </div>
          </div>

          <div class="col-md-6 info-row">

            <div class="col-md-6"><label>{{ lang('priority') }}</label></div>

            <div class="col-md-6">

              <a v-tooltip="change.priority ? change.priority.name : '---'" href="javascript:void(0)">
                {{change.priority ? subString(change.priority.name) : '---'}}
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

		name : 'problem-change-details',

		description : 'Problem Change details page',

		props : {

			change : { type : Array | Object, default : ()=>[]}
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