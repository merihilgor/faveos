<template>

		<form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" :required="required">

			<input ref="fileInput" type="file" :accept="accept" @change="onFileSelected"  multiple style="display:none">
				
			<button data-toggle="modal" @click="$refs.fileInput.click()" class="btn-bs-file btn btn-xs btn-default" >
				<i class="fa fa-file-image-o"></i>&nbsp;{{ lang('choose_file') }}
			</button>
				
			<span v-if="selectedFile && selectedFile.name !== null "> {{selectedFile.name}}{{selectedFile.value}}  
					<i class="fa fa-times btn btn-xs btn-default" @click="removeFile" style="cursor:pointer">&nbsp;Close</i>
			</span>

			<span v-else> 
				No file choosen
			</span>
			
			<div v-if="preview" class="popover fade bottom in" id="preview">
				
				<div class="arrow" id="arrow"></div>
					
					<h3 class="popover-title"><strong>Preview</strong>
					
						<button type="button" style="font-size: initial;" @click="preview=false" class="close pull-right">x</button>
					
					</h3>

					<div class="popover-content">
						
						<img v-if="selectedFile.type=='image/jpeg' 
								|| selectedFile.type=='image/jpg' || selectedFile.type=='image/png' || selectedFile.type=='jpeg' 
								|| selectedFile.type=='jpg' || selectedFile.type=='png'" 
								id="preview_img" :src="imageSrc">

						<img v-else id="preview_img" :src="base+'/themes/default/common/images/doc.png'">

					</div>

				</div>

		</form-field-template>

</template>

<script type="text/javascript">

export default {

	name: "file-upload",

	description: "file upload component along with error block",

	props: {

		label: { type: String, required: true },

		hint: { type: String, default: "" }, //for tooltip message

		name: { type: String, required: true },

		onChange: { type: Function, Required: true },

		classname: { type: String, default: "" },

		required: { type: Boolean, default: false },

		labelStyle:{type:Object},

		id : {type: String|Number, default:'text-field'},

		value : { type: Object|String , default : '' },

		accept : { type : String, default : ''},

	},
	data() {
		return {

			changedValue: this.value,

			base:window.axios.defaults.baseURL,
			
			picture:this.value,
			
			selectedFile:this.value,
			
			imageSrc:'',
			
			preview:false,

		};
	},

	mounted() {

		this.selectedFile = this.value;
	},

	watch: {

		value(newVal) {

			this.selectedFile = newVal;
		}
	},

	methods:{
		
		onFileSelected(event) {

			if(event.target.files[0].size < 2097152) { 

				this.selectedFile = event.target.files[0];

				var element=this.$refs.fileInput;
			
				this.picture = this.selectedFile;
				
				this.preview = true;
				
				var input = event.target;
				
				if (input.files && input.files[0]) {
									
					var reader = new FileReader();
					
					reader.onload = (e) => {
						this.imageSrc = e.target.result;
					}
					reader.readAsDataURL(input.files[0]);
				}
				
				element.value="";
							
				this.onChange(this.selectedFile,this.name,false);

			} else {
				alert('Maximum File upload size is 2MB.')
			}
			
		},

		removeFile() {
			
			this.selectedFile = null;
			
			this.onChange(this.selectedFile,this.name,true);
			
			this.preview = false
		}
		
	},

	components: {
		"form-field-template": require("./FormFieldTemplate")
	}
};
</script>

<style>
#preview{
	top: 44px;left: 87.0469px;display: block;
}
#arrow{
	left: 50%;
}
#preview_img{
	max-width:200px;height:133px;
}
</style>
