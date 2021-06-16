<style scoped>
.custom-menu{
   background: #fff;border: 1px solid rgba(100, 100, 100, .4);border-radius: 0 0 2px 2px;box-shadow: 0 3px 8px rgba(0, 0, 0, .25);color: #1d2129;overflow: visible;position: absolute !important;width: 250% !important;padding: 0px
}
.header>.cool{
	padding: 8px 10px;line-height: 16px;background: #f6f8fa;border-bottom: 1px solid #e1e4e8;
}
.searchstyle{
    padding: 10px 10px 0;background-color: #f6f8fa;padding-bottom: 2px;
}
.fileds{
    display: block;padding: 8px 8px 8px 20px;overflow: hidden;color: grey;cursor: pointer;border-bottom: 1px solid #eaecef;position: relative;
}
.loader{
    max-height: 400px;
    overflow-y: auto;
    width:100%;
}
.spinner1:before {
  content: '';
  box-sizing: border-box;
  position: absolute;

  left: 50%;
  width: 20px;
  height: 20px;
  margin-top: -10px;
  margin-left: -10px;
  border-radius: 50%;
  border: 2px solid #ccc;
  border-top-color: #333;
  animation: spinner .6s linear infinite;
}
.show{
    display: inline-block !important;
}
</style>
<template>
	<div class="pull-right dropdown">
    <!-- Add Custom Field Filters button -->
    <a class="dropdown-toggle" href="javascript:void()" @mouseenter="getCustomFields($event)" data-toggle="dropdown">
      <i class="fa fa-plus">&nbsp;{{ lang('add_custom_field') }}</i>
    </a>

    <!-- Custom field menu -->
   <ul class="dropdown-menu custom-menu">
     <li class="header"><div class="cool" ><b>{{ lang('filter_custom_field') }}</b></div></li>
     <li class="unbind-click">
       <div class="searchstyle">
         <input type="text" class="form-control" placeholder="filter here" v-model="search">
       </div>
     </li>

     <li v-show="visibileCustomFields.length" class="unbind-click">
        <div class="loader">
          <a :id="'form-field-'+index" role="menuitem" tabindex="-1" href="javascript:void(0)" class="fileds" v-for="(field,index) in visibileCustomFields" @click="toggleFormFieldVisibility(field)">
            <span v-if="field.selected==1">
              <span aria-hidden="true" class="glyphicon glyphicon-ok"></span>
            </span>&nbsp;
            <span style="position: absolute;margin-top: -1px">{{field.label}}</span>
          </a>
        </div>
     </li>

     <li v-show="!visibileCustomFields.length && !loading" class="unbind-click"><a href="javascript:void(0)">{{ lang('no_results_found') }}</a></li>

     <div v-show="loading" style="padding :35px;">
       <div class="spinner1" ></div>
     </div>

  </ul>
  </div>
</template>
<script>

  import axios from 'axios';
  import $ from 'jquery';

	export default{

          data(){
          	return{
              allCustomFields:[],
              selectedCustomFields: this.selectedFilters,
              searchedCustomFields:[],
              search:'',
              loading: false,
          	}
          },

          props:{
            selectedFilters: {type: Array, required: true},
          },

          mounted(){
        	    $('.dropdown-menu>.unbind-click').click(function(event){
                   event.stopPropagation();
              });
          },

          methods:{
            //set selected custom filed
            setSelectedField(data){

                if(data.data.length>0){

                  for(var i in data.data){

                    // if key `selected` is not present in the object,
                    // we initialise it with zero
                    if(data.data[i].selected == undefined){
                      data.data[i].selected = 0;
                    }

                    for(var j in this.selectedCustomFields){
                      if(this.selectedCustomFields[j].id == data.data[i].id){
                        data.data[i].selected = 1;
                        data.data[i].value=this.selectedCustomFields[j].value;
                      }
                    }
                  }

                  this.allCustomFields = data.data;
                  this.showloader=false;
                  this.nextUrl=data;
                }
            },

          	//get custom fields
          	getCustomFields(x){

                  this.toggleOptions();
                  this.loading = true;
                  axios.get('api/ticket-custom-fields-flattened')
                   .then(res => {
                       this.setSelectedField(res.data);
                       this.loading = false;
                   }).catch(err => {
                       errorHandler(err)
                       this.loading = false;
                   });

                  this.unbindClickInContainer(x)
          	},

            /**
             * * NOTE: this has been moved to a seperate method because jquery has been
             * defined as a global object and cannot be mocked in test cases
             * @param  {Object} x DOM element reference
             * @return {undefined}
             */
            unbindClickInContainer(x){
              $(x.target).attr('@click',null).unbind('click');
            },

            /**
             * Toggles option in the dropdown
             * NOTE: this has been moved to a seperate method because jquery has been
             * defined as a global object and cannot be mocked in test cases
             * @return {undefined}
             */
            toggleOptions(){
              $(".custom-menu").dropdown("toggle");
            },

            /**
             * Toggles form field visiblilty
             * @param  {Object} formFieldObject
             * @return {undefined}
             */
            toggleFormFieldVisibility(formFieldObject){
		          if(!formFieldObject.selected){
		             formFieldObject.selected=1;
		             formFieldObject.value='';
		             this.selectedCustomFields.push(formFieldObject);
		           }
		           else{
		              formFieldObject.selected=0;
		              this.selectedCustomFields = this.selectedCustomFields.filter(function(el) {
		                 return el.id != formFieldObject.id;
		               });
		           }
              this.$emit('custom',this.selectedCustomFields);
          	},

            /**
             * Filters fields which matches the query
             * @param  {string} searchQuery
             * @return {Array}
             */
            searchFilter(searchQuery){
              return this.allCustomFields.filter(field => {
                searchQuery = searchQuery.toLowerCase();
                let label = field.label.toLowerCase();
                return label.indexOf(searchQuery) !== -1;
              })
            },
          },

        watch : {
          search(val){
            this.searchedCustomFields = this.searchFilter(val);
          }
        },

        computed :{
          visibileCustomFields(){
            return this.search == '' ? this.allCustomFields : this.searchedCustomFields;
          }
        }
	}
</script>
