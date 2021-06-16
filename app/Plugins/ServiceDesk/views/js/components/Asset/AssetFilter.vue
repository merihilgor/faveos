<template>
	
	<div class="main-div">
	
		<div v-if="loading" class="box-body">
	
			<div class="row">
				
				<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
			</div>
		</div>

		<div v-else class="box-body">
		
			<div class="row" v-for="section in metaData">
				
				<template v-for="item in section.section">
					
					<dynamic-select v-if="item.type !== 'date'"
						:key="item.name"
						:id="item.name"
						:name="item.name"
						:apiEndpoint="item.url"
						:classname="item.className"
						:elements="item.elements"
						:multiple="item.isMultiple"
						:prePopulate="item.isPrepopulate"
						:label="lang(item.label)"
						:value="item.value"
						:onChange="onChange"
						strlength="35"
						>
					</dynamic-select>

					<date-time-field v-if="item.type === 'date'" 
						:label="lang(item.label)"
						:value="item.value" 
						type="datetime" 
						:key="item.name"
						:id="item.name"
						:name="item.name"
	          :onChange="onChange" 
	          range 
	          :required="false" 
	          format="YYYY-MM-DD HH:mm:ss" 
	          :classname="item.className"
	          :clearable="true" 
	          :disabled="false" 
	          :editable="true"
	          :pickers="item.pickers"
	          :currentYearDate="false" 
	          :time-picker-options="item.timeOptions"
	          >
	        </date-time-field>
				</template>
			</div>
		</div>

		<div class="btn-group">
			
			<span class="single-btn">
				
				<button id="apply-btn" class="btn btn-primary round-btn" type="button" @click="onApply">
					
					<span class="fa fa-check"></span>&nbsp; {{ lang('apply')}}
				</button>

			</span>
			
			<span class="single-btn">
				
				<button id="apply-btn" class="btn btn-primary single-btn round-btn" type="button" @click="onReset">
					
					<span class="fa fa-undo"></span>&nbsp; {{ lang('reset')}}
				</button>
			</span>
			
			<span class="single-btn" v-if="from !== 'report'">
				
				<button id="apply-btn" class="btn btn-danger single-btn round-btn" type="button" @click="onCancel">
					
					<span class="fa fa-times"></span>&nbsp; {{ lang('cancel')}}
				</button>
			</span>
		</div>
	</div>
</template>

<script>

	export default {
		
		name : "asset-filter",

		description : "Asset filter component",

		props:{
			
			metaData: { type:Array, required: true },

			from : { type : String, default  :''},
		},

		data(){
			
			return{
				
				selectedFilters: {},

				close_on_select: !this.multiple,

				isShowFilter: false,

				componentMetaData: this.metaData,

				loading : false
			}
		},

		methods:{

			onChange(value, name){

				this.selectedFilters[name] = value;
			},

			onCancel() {
				
				this.$emit('selectedFilters', 'closeEvent');
			},

			onApply() {

				if(!this.selectedFilters.assigned_on){

					this.selectedFilters['assigned_on'] = this.metaData[3].section[1].value;
				}

				this.$emit('selectedFilters',this.selectedFilters);
			},
			
			onReset() {

				this.loading = true;

				setTimeout(()=>{
					this.loading = false
				},1000)

				this.$emit('selectedFilters', 'resetEvent');
			},
		},

		components: {
			
			'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
		}
	};
</script>

<style scoped>
	.main-div {
		background-color: whitesmoke;
		padding: 5px;
		border-radius: 5px;
	}
	.btn-group{
		padding: 0px 0px 10px 5px;
	}
	.single-btn{
		padding-left: 5px;
	}
	.round-btn {
		border-radius: 3px;
	}
</style>
