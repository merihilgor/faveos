<template>

	<div class="box box-primary">

		<template v-if="!loading">

			<div class="box-header with-border">

				<h3 class="box-title" v-tooltip="assetData.name"> {{ subString(assetData.name,50) }}</h3>

				<asset-actions :asset="assetData"></asset-actions>
			</div>

			<div class="box-body">

				<div class="row">

					<div class="col-md-12">

						<div class="callout callout-info">

							<div class="row">

								<div class="col-md-4">

									<b>{{trans('created_date')}} : </b>

									<span  v-tooltip="formattedTime(assetData.created_at)"> {{formattedTime(assetData.created_at)}}</span>
								</div>

								<div class="col-md-4">

									<b>{{trans('department')}} : </b>

									<span v-tooltip="assetData.department.name"> {{subString(assetData.department.name)}}</span>
								</div>

								<div class="col-md-4">

									<b>{{trans('asset_type')}} : </b>

									<span v-tooltip="assetData.asset_type.name"> {{subString(assetData.asset_type.name)}}</span>
								</div>
							</div>
						</div>
				  </div>
				</div>

				<div class="row">

					<div class="col-md-12">

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('asset_status') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.asset_status ? assetData.asset_status.name : '---'"> 
									{{assetData.asset_status ? subString(assetData.asset_status.name) : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('organization') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.organization ? assetData.organization.name : '---'"> 
									{{assetData.organization ? subString(assetData.organization.name) : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('description') }}</label></div>

							<div class="col-md-6">

								<a href="javascript:;" class="btn btn-info btn-xs" @click="showDescription = true">

									<i class="fa fa-file-text">&nbsp;&nbsp;</i>{{trans('show_description')}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('managed_by') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.managed_by ? assetData.managed_by.full_name : '---'"> 
									{{assetData.managed_by ? subString(assetData.managed_by.full_name) : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('used_by') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.used_by ? assetData.used_by.full_name : '---'"> 
									{{assetData.used_by ? subString(assetData.used_by.full_name) : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('identifier') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.identifier ? assetData.identifier : '---'"> 
									{{assetData.identifier ? assetData.identifier : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('assigned_on') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.assigned_on ? formattedTime(assetData.assigned_on) : '---'"> 
									{{assetData.assigned_on ? formattedTime(assetData.assigned_on) : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('product') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.product ? assetData.product.name : '---'"> 
									{{assetData.product ? subString(assetData.product.name) : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('impact_type') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.impact_type ? assetData.impact_type.name : '---'"> 
									{{assetData.impact_type ? subString(assetData.impact_type.name) : '---'}}
								</a>
							</div>
						</div>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('location') }}</label></div>

							<div class="col-md-6">

								<a v-tooltip="assetData.location ? assetData.location.name : '---'"> 
									{{assetData.location ? subString(assetData.location.name) : '---'}}
								</a>
							</div>
						</div>

						<template v-if="assetData.custom_field_values_for_asset_form_builder_only && assetData.custom_field_values_for_asset_form_builder_only.length > 0">
					
							<div v-for="customField in assetData.custom_field_values_for_asset_form_builder_only" 
								class="col-md-6 info-row">

								<div class="col-md-6">

									<label>{{ customField.label }}</label>

								</div>

								<div class="col-md-6">

									<div v-if="Array.isArray(customField.value)">

										<a  v-for="(value,index) in customField.value" onClick="return false" v-tooltip="value">
										{{subString(value)}}<span v-if="index != Object.keys(customField.value).length - 1">,</span>&nbsp;
									</a>

									</div>

									<a v-else onClick="return false"  v-tooltip="customField.field_type == 'date' ?formattedTime(customField.value) : customField.value">
					
										{{ customField.field_type == 'date' ? formattedTime(customField.value) : subString(customField.value) }}
									</a>
								</div>
							</div>
						</template>

						<div class="col-md-6 info-row">

							<div class="col-md-6"><label>{{ trans('attachments') }}</label></div>

							<div class="col-md-6">

								<span v-if="assetData.attachments.length > 0">

									<template v-for="attach in assetData.attachments">
										
										<span class="line">

											<span v-tooltip="attach.value">
												
												<img :src="attach.icon_url" class="attach_img"> {{subString(attach.value,15)}} {{'('+attach.size+')'}}
											</span>
										 
											<a id="download" v-tooltip="trans('click_to_download')" @click="downloadFile(attach.id)"> 
												
												<i class="fa fa-download"></i>
											</a>
										</span>
										<br>
									</template>
								</span>
							  
								<span v-else>---</span>
							</div>
						</div>          			
					</div>
				</div>
				<hr>	
				<template v-if="assetData.custom_field_values_for_asset_type && assetData.custom_field_values_for_asset_type.length > 0">
					
					<div class="box box-default box-solid">
		            
		            <div class="box-header with-border">
		            
		              <h3 class="box-title">{{assetData.asset_type.name + ' '+trans('properties')}}</h3>
		            </div>

		            <div class="box-body">
		              	
		              	<div class="row asset_row">

		              		<div class="col-md-12">

									<div v-for="customField in assetData.custom_field_values_for_asset_type" 
										class="col-md-6 info-row">

										<div class="col-md-6">

											<label>{{ customField.label }}</label>

										</div>

										<div class="col-md-6">

											<div v-if="Array.isArray(customField.value)">

												<a  v-for="(value,index) in customField.value" onClick="return false" v-tooltip="value">
												{{subString(value)}}<span v-if="index != Object.keys(customField.value).length - 1">,</span>&nbsp;
											</a>

											</div>

											<a v-else onClick="return false"  v-tooltip="customField.field_type == 'date' ?formattedTime(customField.value) : customField.value">

													{{ customField.field_type == 'date' ? formattedTime(customField.value) : subString(customField.value) }}
											</a>
										</div>
									</div> 
								</div>
							</div>
		            </div>
		         </div>
				</template>

				<template v-if="assetData.custom_field_values_for_department && assetData.custom_field_values_for_department.length > 0">
					
					<div class="box box-default box-solid">
		            
		            <div class="box-header with-border">
		            
		              <h3 class="box-title">{{assetData.department.name + ' '+trans('properties')}}</h3>
		            </div>

		            <div class="box-body">
		              	
		              	<div class="row asset_row">

		              		<div class="col-md-12">

									<div v-for="customField in assetData.custom_field_values_for_department" 
										class="col-md-6 info-row">

										<div class="col-md-6">

											<label>{{ customField.label }}</label>

										</div>

										<div class="col-md-6">

											<div v-if="Array.isArray(customField.value)">

												<a  v-for="(value,index) in customField.value" onClick="return false" v-tooltip="value">
												{{subString(value)}}<span v-if="index != Object.keys(customField.value).length - 1">,</span>&nbsp;
											</a>

											</div>

											<a v-else onClick="return false"  v-tooltip="customField.field_type == 'date' ?formattedTime(customField.value) : customField.value">

												{{ customField.field_type == 'date' ? formattedTime(customField.value) :  subString(customField.value) }}
											</a>
										</div>
									</div> 
								</div>
							</div>
		            </div>
		         </div>
				</template>
			</div>

			<transition name="modal">

				<asset-description  v-if="showDescription" :onClose="onClose" :showModal="showDescription" 
					:description="assetData.description">

				</asset-description>
			</transition>
		</template>

		<template v-if="loading">
			
			<div class="box-body" id="loader_margin">

				<loader :animation-duration="4000" :size="60"/>
			</div>	
		</template>
	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics'

	import axios from 'axios';

	import { mapGetters } from 'vuex'

	export default {

		name : 'asset-details',

		description : 'Asset details Component',

		props : { 

			assetId : { type : String | Number, required : true }
		},

		data() {

			return {

				assetData : '',

				loading : true,

				showDescription : false,
			}
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		beforeMount() {

			this.getData();
		},

		methods : {

			getData() {

				axios.get('/service-desk/api/asset/'+this.assetId).then(res=>{

					this.loading = false;

					this.assetData = res.data.data;
					
				}).catch(error=>{

					this.loading = false;
				});
			},

			subString(value,length = 15){
	 
				return getSubStringValue(value,length)
			},

			onClose(){
		
				this.showDescription = false;
			},

			downloadFile(id){
				
				window.open(this.basePath() + '/service-desk/download/'+id+'/sd_assets:'+this.assetData.id+'/attachment')
			}
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'asset-description' : require('./Mini/AssetDescription'),

			'asset-actions' : require('./Mini/AssetActions'),

			'faveo-box': require('components/MiniComponent/FaveoBox'),
		}
	};
</script>

<style scoped>
	
	.info-row { border-top: 1px solid #f4f4f4; padding: 10px; }
	
	#download { cursor: pointer; }

	.line { line-height: 2; }

	#loader_margin { margin-top: 75px;margin-bottom: 75px; }

	.asset_row { margin-top: -11px; }

	.attach_img { width: 15px; height: 15px; }
</style>