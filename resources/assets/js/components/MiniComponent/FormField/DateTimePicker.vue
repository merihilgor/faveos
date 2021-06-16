<template>

    <form-field-template :label="label" :name="name" :labelStyle="labelStyle"  :classname="classname" :hint="hint" :required="required">

        <date-picker v-if="label === 'Publish immediately'" v-model="changedValue" lang="en"  :type="type" :time-picker-options="timePickerOptions" :format="format" :placeholder="place" :disabled="disabled" :range="range" input-class="form-control" v-on:input="onChange(changedValue, name)" :clearable="clearable" :shortcuts="pickers" :not-before="notBefore" :confirm="true"></date-picker>

        <date-picker v-if="!currentYearDate && label !== 'Publish immediately'" v-model="changedValue" lang="en"  :type="type" :time-picker-options="timePickerOptions" :format="format" :placeholder="place" :disabled="disabled" :range="range" input-class="form-control" v-on:input="onChange(changedValue, name)" :clearable="clearable" :confirm="true" :editable="editable" :shortcuts="pickers" :not-before="notBefore" :not-after="notAfter"></date-picker>

        <date-picker v-if="currentYearDate" v-model="changedValue" lang="en"  :type="type" :time-picker-options="timePickerOptions" :format="format" :placeholder="place" :disabled="disabled" :editable="editable" :range="range" input-class="form-control" v-on:input="onChange(changedValue, name)" :clearable="clearable" :not-before="moment(moment().year()+'-01-01').format('YYYY-MM-DD')" :not-after="moment(moment().year()+'-12-31').format('YYYY-MM-DD')" :shortcuts="pickers" :confirm="true"></date-picker>
    </form-field-template>

</template>

<script type="text/javascript">

import DatePicker from 'vue2-datepicker'
import moment from 'moment'
    export default {
        name:'date-time-field',

        description:'date time field component along with error block',

        props:{

            /**
             * the label that needs to be displayed
             * @type {String}
             */
            label: { type: String, required: true },

            /**
             * Hint regarding what the field is about (it will be shown as tooltip message)
             * @type {String}
             */
            hint: { type:String, default: '' }, //for tooltip message

            /**
             * selected value of the field.
             * list of already selected element ids that has to be displayed
             * @type {Number|Boolean}
             */
            value: { type: String|Date, required: true },

            /**
             * the name of the state in parent class
             * @type {String}
             */
            name: { type: String|Number, required: true },

            /**
             * Type of the text field. Available options : text, textarea, password, number
             * @type {String}
             */
            type: {type: String, default: 'text'},


            /**
             * The function which will be called as soon as value of the field changes
             * It should have two arguments `value` and `name`
             *     `value` will be the updated value of the field
             *     `name` will be thw name of the state in the parent class
             *
             * An example function :  
             *         onChange(value, name){
             *             this[name]= selectedValue
             *         }
             *         
             * @type {Function}
             */
            onChange:{type: Function, Required: true},
            
            /**
             * classname of the form field. It can be used to give this component any bootstrap class or a custom class
             * whose css will be defined in parent class
             * @type {String}
             */
            classname : {type: String, default:''},

            /**
             * for show labels of the fields
             * @type {Object}
             */
            labelStyle:{type:Object},

            /**
             * Whether the given field is required or not.
             * If passed yes, an asterik will be displayed after the label
             * @type {Boolean}
             */
            required: { type: Boolean, default: true},

            /**
             * time picker options
             * type {Object}
             */
            timePickerOptions : { type: Object, default:()=>{}} ,

            /**
             * date time picker format
             * @type {String}
             */
            format : { type: String,default: ''},

            /**
             * date time picker disabled status
             * @type {Boolean}
             */
            disabled : { type: Boolean, default: false},

            /**
             * date time picker clearable status
             * @type {Boolean}
             */
            clearable : { type: Boolean, default: false},

            /**
             * date time picker range attribute 
             * @type {Boolean}
             */
            range: { type : Boolean},

            /**
             * placeholde
             * @type {String}
             */
            place: { type : String},
            /**
             * placeholde
             * @type {String}
             */
            notBefore: { type : String|Date },

            notAfter: { type : String|Date },

            currentYearDate : { type : Boolean , default : false},

            confirm : { type : Boolean , default : false},

            editable : { type : Boolean , default : true},

            pickers : { type : Boolean | Array , default : false},

        },
        data(){
            return {
                
                /**
                 * The updated value in the text field
                 * @type {String}
                 */
                changedValue: this.value,

                moment:moment
            }
        },

        watch:{
            /**
             * returns new date time value
             * @return {Void}
             */
            value(newValue,oldValue){
                this.changedValue = newValue === '' ? null : newValue
            }
        },

        components:{

            DatePicker,

            'form-field-template' : require('./FormFieldTemplate')
        }
    };

</script>

<style>
    .mx-input{
        border-radius: 0 !important;
    }
    .mx-input-append {
        /*height: 30px !important;
        background: none !important*/
    }
    .mx-shortcuts-wrapper{
        /*display: none !important;*/
    }
    .mx-shortcuts-wrapper .mx-shortcuts {
        text-transform: capitalize;
    }
    .mx-calendar-content {
        width: 224px !important;
    }
    .mx-datepicker{
            width: 100% !important;
    }
    .mx-datepicker-range {
        width: 100% !important;
    }
    .mx-input-wrapper input {
        background-color: transparent !important;
        /*height :30px !important;*/
    }
    .mx-calendar-icon{
        height: auto !important;
    }
    .mx-input-append{
        background-color: transparent !important;
    }
</style>