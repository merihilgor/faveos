<template>
	<div class="box box-primary">
				<form method="POST" >

				<div class="nav-tabs-custom">
				<!-- 	<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_1" data-toggle="tab">System</a></li>
					</ul> -->

					 <div class="box-header with-border">
        <h2 class="box-title">{{lang('system')}}</h2>
    </div>


					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
						<alert-notification  :successShow="SuccessAlert" :errorShow="ErrorAlert" :successMsg="lang(success)" :errorMsg="lang(error)" :extraData="''"/>
							<div class="row">
								<div class="col-md-3 form-group">
									<label for="pagination">{{lang('number_of_elements_to_display')}}</label> <span class="text-red"> *</span>
									<a><i class="fa fa-question-circle pagint" aria-hidden="true" :title="message"></i></a>
									<input type="text" @keypress="isNumber" class="form-control" name="Pagination" v-model="pagination" min="2" v-validate="'required|min_value:10|max_value:15'" :class="{'input': true, 'is-danger': errors.has('Pagination') }">
									<span v-show="errors.has('Pagination')" class="help is-danger">
								        <i v-show="errors.has('Pagination')" class="fa fa-warning"></i> {{ errors.first('Pagination') }}
								    </span>
								</div>
								<div class="col-md-9 form-group">
								<div id="box-head" class="box-header">
								<span class="lead">{{lang('knowledgebase_status')}}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<div v-if="status==0" class="btn-group" id="toggle_event_editing">
									<button @click="inActive" type="button" class="btn btn-info unlocked_inactive">{{lang('inactive')}}</button>
									<button @click="active" type="button" class="btn btn-default locked_active">{{lang('active')}}</button>
								</div>
								<div v-else class="btn-group" id="toggle_event_editing">
									<button @click="inActive" type="button" class="btn btn-default locked_active">{{lang('inactive')}}</button>
									<button @click="active" type="button" class="btn btn-info unlocked_inactive">{{lang('active')}}</button>
								</div>
							</div>
								</div>
							</div>

 						</div>
					</div>
					<div class="box-footer">
						<button id="post" @click="validateForm" class="btn btn-primary"><i :class="classname" aria-hidden="true">&nbsp;&nbsp;</i>{{lang(button)}}</button>
					</div>
				</div>
			</form>
	</div>
</template>

<script>
var moment = require('moment');
import axios from 'axios'
export default {
	data() {
		return {
			kbsettings:{},
			status:null,
			pagination:null,
			date_time:'',
			// alert
			SuccessAlert:'none',
		  success:'',
		  error:'',
		  ErrorAlert:'none',
		  button:'Save',
		  classname:'fa fa-floppy-o',
		  message:'This setting will display no of categories on KB home page and no of articles on articles page',
		  date:'',
		  bindDate:false
		}
	},
	created() {
		this.commonApi();
		setTimeout(()=>{
               $('.pagint').tooltip();
           },2000)
	},
	methods: {
		getValue(payload){
			this.date_time=payload;
		},
		commonApi() {
			axios.get('kb/settings/getvalue').then(res=>{
				this.kbsettings = res.data.data.kbsettings;
				this.status = this.kbsettings.status;
				this.pagination = this.kbsettings.pagination;
				this.date = this.kbsettings.date_format;
				this.bindDate=true;
			}).catch(res=>{
				console.log(res)
				this.bindDate=true;
			})
		},
		inActive() {
			const data = 'kb_status=' + 0;
			axios.post('kb/status/index/',data).then(res=>{
				this.commonApi();
				// Success alert
				window.scrollTo(0, 0);
		this.SuccessAlert="block";
		this.success="status_updated_successfully";
		setTimeout(()=>{
                  $('.alert-success, .alert-danger').not('.do-not-slide').slideUp( 2000, function() {});
             }, 2000);
		setTimeout(()=>{
                   location.reload();
             }, 5000);
			}).catch(res=>{

			})
		},
		active(){
			const data = 'kb_status=' + 1;
			axios.post('kb/status/index/',data).then(res=>{
				this.commonApi();
				// Success alert
				window.scrollTo(0, 0);
		this.SuccessAlert="block";
		this.success="status_updated_successfully";
		setTimeout(()=>{
                   $('.alert-success, .alert-danger').not('.do-not-slide').slideUp( 2000, function() {});
             }, 2000);
		setTimeout(()=>{
                   location.reload();
             }, 5000);
			}).catch(res=>{

			})
		},
			isNumber(evt) {
		    	 evt = (evt) ? evt : window.event;
			      var charCode = (evt.which) ? evt.which : evt.keyCode;
			      if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
			        evt.preventDefault();;
			      } else {
			        return true;
			      }
		    },
		    			validateForm(e){
			     e.preventDefault();

				    this.$validator.validateAll().then(result=>{
				    	if(result){
				  	      this.postData();
				    	}
				    });
    	    },
		postData() {
			window.eventHub.$emit('checkInbox');

			this.button=this.lang('saving');
			this.classname='fa fa-circle-o-notch fa-spin';
		// e.target.disabled=true;
			const data={};
				data['pagination']=this.pagination;
				// data['date_format_custom']=this.date_time;
				data['status']=this.status
			axios.post('postsettings',data).then(res=>{
				this.button='Save';
				this.classname='fa fa-floppy-o';
		// e.target.disabled=false;
			// Success alert
		this.SuccessAlert="block";
		this.success=res.data.data;
		setTimeout(()=>{
					$('.alert-success, .alert-danger').not('.do-not-slide').slideUp( 1000, function() {});
						setTimeout(()=>{
						 	this.SuccessAlert='none'
		                }, 1000);
		        }, 3000);
			}).catch(error=>{
				console.log(error.response)
			})

		}
	},
};
</script>

<style scoped>
#box-head {
	margin-top: 10px;
}
  .fa-warning{
     color: red;
     font-size: 14px;
  }
</style>
