<template>

	<div>
		
		<div class="form-group" id="reply_content_class">
		
			<div class="col-md-12">
				
				<label :style="[show_error ? {'color': '#dc3545 '} : {'color': 'black'}]" for="Reply Content">
					{{ lang('description') }}
				</label>
				
				<span class="text-red"> *</span>
					
				<media-gallery v-on:getAttach="getAttachment" v-on:inlineAttach="getInline" :apiUrl="filesApi" 
					:target_url="chunkApi" :page="page_name" :onInlinePdf="getPDF">

					<span slot="templateBtn">
						<slot name="template"></slot>
					</span>
						
				</media-gallery  :inlineFiles="inline" :attachmentFiles="attchments">

				<ck-editor :value="description" type="text" :onChange="onChange" :name="name" :label="lang('description')"
					classname="" :required="false" :labelStyle="labelStyle" :lang="'en'">
						
				</ck-editor>
				
				<span v-if="show_error" class="help-block">This field is required</span>
				
				<div v-if="page_name != 'kb'" id="file_details">
					
					<template v-for="(attachment,index) in attchments">
					
						<div id='hidden-attach' contenteditable='false' v-if="attachment.disposition !== 'inline'">
							
							{{attachment.filename ? attachment.filename : attachment.name}}({{attachment.size ? attachment.size : attachment.file_size}} bytes)
							
							<i class='fa fa-times close-icon' aria-hidden='true' @click='removeAttachment(index)'></i>
						</div>
					</template>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

	export default {

		name : 'add-media',

		description : 'Media files uploader component',

		props : {

			description : { type : String, default : ''},
			
			classname : { type : String, default : 'form-group'},
			
			show_error : { type : Boolean, default : false},
			
			noDropdown : { type : Boolean, default : false},
			
			page_name : { type : String, default : ''},

			attachments : { type : Array, default : ()=>[]},

			inlineFile : { type : Array, default : ()=>[]},

			onAttach : { type : Function, default : ()=> { return true}},

			onInline : { type : Function, default : ()=> { return true}},

			onInput : { type : Function, default : ()=> { return true}},

			chunkApi : { type : String, default : '/chunk/upload'},

			filesApi : { type : String, default : '/media/files'},

			name : { type : String, default : 'description'}
		},

		components : {		

			'ck-editor':require('components/MiniComponent/FormField/CkEditorVue'),
			
			'media-gallery':require('components/Common/ChunkUpload/ChunkUpload.vue'),
		},

		data(){
			
			return {
			
				attchments : this.attachments,
				
				inline : this.inlineFile,

				labelStyle : { display : 'none' }
			}
		},

		watch : {

			attachments(newValue,oldValue){

				this.attchments = newValue;
			}
		},

		methods : {

			onChange(value,name){

				this.onInput(value,name);
			},

			getPDF(value){

				this.onInput(this.description + '<br>' +value,this.name);
			},

			getAttachment(x){

				this.attchments.push(x);

				this.onAttach(this.attchments);
			},

			removeAttachment(x){

				this.attchments.splice(x,1);

				this.onAttach(this.attchments);
			},

			getInline(x){

				this.inline.push(x);

				this.onInline(this.inline);
			},
		}
	};
</script>

<style scoped>
	#hidden-attach{
		background-color: #f5f5f5;
		border: 1px solid #dcdcdc;
		font-weight: bold;
		margin-top:9px;
		overflow-y: hidden;
		padding: 4px 4px 4px 8px;
	}
	
	.close-icon{
		float:right;cursor: pointer;
	}
</style>