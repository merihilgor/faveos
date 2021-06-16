<style scoped>
.text-red {
  color: red;
}
.ticket-title {
  clear: both;
  padding-top: 20px;
}
.time-period {
  padding-bottom: 5px;
}
</style>
<template>
	 <div>
	 	 <h4 class="box-title time-period" style="padding-left:7px">{{lang('time_period')}}</h4>
	 	 <!-- interval -->
<div class="row col-sm-12">
     <div class="col-sm-2">
      <label>{{lang('name')}}<span class="text-red"> *</span></label>
      <!-- select with validation -->
            <text-field  :unique="'unique'" :validate="{required:true}" :fieldName="'Name'" :fieldValue="recur.name"  v-on:assignToModel="setModel" :objKey="'name'"></text-field>
     </div>

	 	 <div class="col-sm-2">
	 	 	<label>{{lang('interval')}}<span class="text-red"> *</span></label>
	 	 	<!-- select with validation -->
            <select-field  :validate="{required:true}" :fieldName="'Interval'" :fieldValue="recur.interval" v-on:assignToModel="setModel" :options="interval" :objKey="'interval'" :valueFor="'id_with_name'"></select-field>
	 	 </div>
	 	 <!-- value -->
     <div class="col-sm-2" v-if="recur.interval != 'daily' && recur.interval != ''">
 	      <label>{{lang('every')}}</label>
        <!-- select with validation -->
        <select-field :validate="{required:true}" :fieldName="'Every'" :fieldValue="recur.delivery_on" v-on:assignToModel="setModel" :options="every" :objKey="'delivery_on'" :valueFor="'id_with_name'" :disableField="disableEvery"></select-field>
     </div>
     <!-- start date with datetime picker-->
     <div class="col-sm-3">
     	
      <date-time-field :label="lang('start_with')" :value="start_date" type="date" name="start_date"
          :onChange="onChange" :required="true" format="YYYY-MM-DD" :clearable="true" 
          :disabled="false" :not-after="startDateBefore">
            
        </date-time-field>
     </div>
     <!-- end selection -->
     <div class="col-sm-3">
        <label>{{lang('end')}}</label>
        <!-- select with validation -->
        <select-field :validate="{required:false}" :fieldName="'End'" :fieldValue="recur.end_value" v-on:assignToModel="setModel" :options="end" :objKey="'end_value'" :valueFor="'id_with_name'"></select-field>
     </div>
   </div>

   <div class="row col-sm-12">
     <!-- end date with datetime picker -->
     <div class="col-sm-3" v-if="showEndDate">

      <date-time-field :label="lang('end_date')" :value="end_date" type="date" name="end_date"
          :onChange="onChange" :required="true" format="YYYY-MM-DD" :clearable="true" 
          :disabled="false" :not-before="endDateAfter">
            
        </date-time-field>
     </div>

     <div class="col-sm-3">

      <date-time-field :label="lang('execution_time')" :value="execution_time" type="time" name="execution_time"
          :onChange="onChange" format="HH:mm:ss" :required="false" :clearable="true" 
          :disabled="false">
            
        </date-time-field>
     </div>
   </div>
     <h4 class="box-title ticket-title" style="padding-left:7px">{{lang('ticket')}}</h4>
	 </div>
</template>
<script>

import moment from 'moment'

export default {
  props: ["recur"],
  data() {
    return {

      start_date : this.recur.start_date,

      startDateBefore : '',

      end_date : this.recur.end_date,

      endDateAfter : '',

      execution_time : this.recur.execution_time,

      //disable every(label name)
      disableEvery: true,
      //end intial time
      showEndDate: false,
      // interval options
      interval: [
        { id: "daily", name: "Daily" },
        { id: "weekly", name: "Weekly" },
        { id: "monthly", name: "Monthly" },
        { id: "yearly", name: "Yearly" }
      ],
      every: [],
      // weekly options
      weekly: [
        { id: "sunday", name: "Sunday" },
        { id: "monday", name: "Monday" },
        { id: "tuesday", name: "Tuesday" },
        { id: "wednesday", name: "Wednesday" },
        { id: "thursday", name: "Thursday" },
        { id: "friday", name: "Friday" },
        { id: "saturday", name: "Saturday" }
      ],
      // monthly options
      monthly: [],
      //  yearly options
      yearly: [
        { id: "january", name: "January" },
        { id: "february", name: "February" },
        { id: "march", name: "March" },
        { id: "april", name: "April" },
        { id: "may", name: "May" },
        { id: "june", name: "June" },
        { id: "july", name: "July" },
        { id: "august", name: "August" },
        { id: "september", name: "September" },
        { id: "october", name: "October" },
        { id: "november", name: "November" },
        { id: "december", name: "December" }
      ],
      //end by
      end: [{ id: "by", name: "By" }],
    };
  },
  components: {
    "select-field": require("../../FormFields/SelectFieldWithValidation.vue"),
    "text-field": require("../../FormFields/TextFieldWithValidation.vue"),
    
    'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
  
  },
  mounted() {
    // set monthly option
    for (var i = 0; i <= 30; i++) {
      this.monthly.push({ id: i + 1, name: i + 1 });
    }
  },
  watch: {
    recur(newvalue) {
      
      this.start_date = this.endDateAfter = newvalue['start_date'];
      
      this.end_date = this.startDateBefore = newvalue['end_date'];

      this.execution_time =newvalue['execution_time'] ? this.getExecutionTime(newvalue['execution_time']) : '';
      
      if (newvalue.end_date != null) {
        this.recur.end_value = "by";
        this.showEndDate = true;
        this.every = this[newvalue.interval];
        if (newvalue.interval != null) {
          this.disableEvery = false;
        }
      }
    }
  },
  methods: {

    getExecutionTime(dStr) {
     
       return moment(dStr, 'HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');
    },

    onChange(value,name) {

      this[name] = value;

      if(name == 'start_date') {

        this.recur['start_date'] = moment(value).format("YYYY-MM-DD");

        this.endDateAfter = value;
      }

      if(name == 'end_date') {

         this.recur['end_date'] = moment(value).format("YYYY-MM-DD");

        this.startDateBefore = value;
      } 

      if(name == 'execution_time') {

          this.recur['execution_time'] = moment(value).format("HH:mm:ss");
      }
    },

    //set model
    setModel(x, y) {
      this.recur[x] = y;
      if (x == "interval") {
        if (y == "monthly" || y == "yearly" || y == "weekly") {
          this.every = this[y];
          this.disableEvery = false;
        } else {
          this.every = [];
          this.disableEvery = true;
        }
      } else if (x == "end_value") {
        if (y == "by") {
          this.showEndDate = true;
        } else {
          this.showEndDate = false;
        }
      }
    }
  }
};
</script>
