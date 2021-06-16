<template>

	<div class="btn-group block">

		<a v-if="data.edit_url" class="btn btn-primary btn-sm" id="edit-button" :href="data.edit_url" v-tooltip="lang('edit')">

			<span id="edit_btn">

				<i class="fa fa-edit"></i>
			</span>
		</a>

		<div v-if="data.role != 'user'" class="dropleft btn-group" id="down">

	    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown" v-tooltip="lang('view')"
	    	id="drop-button">

	    	<i class="fa fa-eye"></i></button>
	    
	    <ul class="dropdown-menu" id="action-menu">
	    
	      <li><a :href="data.view_url">{{lang('view_user')}}</a></li>
	    
	      <li><a :href="data.agent_view_url">{{lang('view_agent')}}</a></li>
	    </ul>
	  </div>

		<a v-if="data.role === 'user'" id="view-button" class="btn btn-primary btn-sm" :href="data.view_url" v-tooltip="lang('view')">

			<span id="view_btn">

				<i class="fa fa-eye"></i>
			</span>
		</a>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"user-table-actions",

		description: "User table edit componnet",

		props: {

			data : { type : Object, required : true },
		},
	};

</script>

<style type="text/css">

	.block{
		display: block !important;
	}

	#edit-button , #view-button, #drop-button{
		margin-left: 2px !important;
		margin-right: 2px !important;
	}

	#down{
		display: inline !important;
	}

	#action-menu{

		right: 0 !important;
		left: unset !important;
		top: auto !important;
		padding: 0 !important;
	}
</style>
