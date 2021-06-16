<template>

	<div>

		<modal v-if="showModal" :showModal="showModal" :onClose="onClose">

			<div slot="title">

				<h4>{{lang('description')}}</h4>
			</div>

			<div slot="fields" id="product_desc">

				<div class="row">

          <div class="col-md-12" id="desc">

            <span v-html="description"></span>
          </div>
        </div>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">

	export default {

		name : 'change-description',

		description : 'Change description Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function, default : ()=>{}},

      description : { type : String, default : ''}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
		}
	};
</script>

<style type="text/css">
  #product_desc{
    margin-left: 15px;
    margin-right: 15px;
  }
  #desc{
  	overflow: auto;
    word-break: break-all;
    min-height: 150px;
    max-height: 300px;
  }
 
  .table{
		overflow-x: auto;
	}

	 #desc table{
	 	overflow-x: auto;
	 }

	 #desc table, #desc table td, #desc table tr, #desc table th {  
	  border: 1px solid #ddd;
	  text-align: left;
	}

	 #desc table {
	  border-collapse: collapse;
	  width: max-content;
	}

	 #desc table th,  #desc table td {
	  padding: 15px;
	}
</style>