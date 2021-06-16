<template>

	<div>
		
		<alert componentName="cab"/>

		<div class="row" v-if="hasDataPopulated === false || loading === true">
			
			<custom-loader></custom-loader>
		</div>

		<div class="box box-primary">

			<div class="box-container" v-if="hasDataPopulated">
			
				<div class="box-header with-border">
			
					<h3 class="box-title">{{lang(title)}}</h3>
				</div>

				<div class="box-body">

					<div class="row margin-0" id="cab-name">

						<text-field :label="lang('name')" :value="name" name="name" :onChange="onChange" :required="true" 
							classname="col-md-4">

						</text-field>

						<dynamic-select :label="lang('status_on_approve')" name="action_on_approve" :multiple="false"
              :value="action_on_approve" classname="col-xs-4" :onChange="onChange"  apiEndpoint="/service-desk/api/dependency/change_statuses" :clearable="action_on_approve ? true : false"></dynamic-select>

            <dynamic-select :label="lang('status_on_deny')" name="action_on_deny" :multiple="false"
              :value="action_on_deny" classname="col-xs-4" :onChange="onChange" apiEndpoint="/service-desk/api/dependency/change_statuses" :clearable="action_on_deny ? true : false"></dynamic-select>
						</div>

					<div id="list-of-levels">

						<div class="margin-10 box-container" v-for="(level, levelIndex) in levels">

							<div class="box-header with-border">
							
								<h4 class="box-title">{{lang('level')}}&nbsp;{{levelIndex + 1}}</h4>

								<button v-if="levels.length > 1" :id="'cab-level-delete-'+levelIndex" 
									@click="deleting(levelIndex, level.id)" class="close">
									
									<span aria-hidden="true">&times;</span>
								</button>
							</div>

							<div class="margin-10">
								<alert :componentName="'cab-level-'+levelIndex"/>
							</div>

							<div class="row margin-auto margin-top-10">

								<text-field :label="lang('name')" :value="level.name" :name="'level-name-'+levelIndex" 
									:onChange="onChange" :required="true" classname="col-md-6">

								</text-field>

								<static-select :label="lang('matcher')" :elements="matcherOptions" :name="'level-match-'+levelIndex"  
									:onChange="onChange" :required="true" classname="col-md-6" :value="level.match">

								</static-select>
							</div>

							<div class="row margin-auto" :id="'cab-level-'+levelIndex">
							
								<dynamic-select :label="lang('users')" :value="level.approvers.users" :onChange="onChange" 
									:name="'cab-agent-'+levelIndex" apiEndpoint="/api/dependency/users?meta=true"  
									:multiple="true" classname="col-md-6" strlength="50">

								</dynamic-select>

								<dynamic-select :label="lang('user_types')" :value="level.approvers.user_types" :onChange="onChange" 
									:name="'cab-user-type-'+levelIndex" apiEndpoint="/service-desk/api/dependency/user_types"  
									:multiple="true" classname="col-md-6" strlength="50">

								</dynamic-select>
							</div>

							<modal v-if="showDeletePopUp" :showModal="true" :onClose="()=> showDeletePopUp = false">

								<div slot="title">
									
									<h4>{{lang('delete_cab_level')}}</h4>
								</div>

								<div slot="fields">
									
									<p class="margin-15">
										
										{{lang('you_are_about_to_delete_a_cab_level')}}.&nbsp;{{lang('please_confirm')}}
									</p>
								</div>
								
								<div slot="controls">

									<button :id="'confirm-delete-button-'+levelIndex" type="button" @click = "onDelete()" 
										class="btn btn-danger">
										
										<i class="glyphicon glyphicon-trash" aria-hidden="true"></i>&nbsp;{{lang('delete')}}
									</button>
								</div>
							</modal>
						</div>
					</div>

					<div class="margin-10">

						<button type="button" id="add-level" @click="addLevel" class="btn btn-primary">

							<span class="glyphicon glyphicon-plus"></span>&nbsp;{{lang('add_level')}}
						</button>

						<button type="button" id="submit-cab" @click="onSubmit" :disabled="loading" class="btn btn-primary">

							<span :class="iconClass"></span>&nbsp;{{lang(btnName)}}
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

	import {getIdFromUrl, extractOnlyId} from 'helpers/extraLogics';
	
	import {successHandler, errorHandler} from 'helpers/responseHandler';
	
	import {validateCabSettings} from '../../../validator/cabSettingRules'
	
	import axios from 'axios';

	export default {
	
		name: 'cab-create-edit',

		description: 'handles create/edit part of cab',

		data(){

			return {

				title  : 'create_cab',

				btnName : 'save',

				iconClass : 'fa fa-save',

				id: 0,

				name:'',

				action_on_approve: '',

        action_on_deny: '',
				
				levels:[{
					
					id:0,
			
					name:'',

					order: 1,

					match:'all',

					approvers : {

						users:[],	

						user_types:[],
					}
				}],

				loading: false,

				showDeletePopUp: false,

				matcherOptions: [{id: 'any', name: 'Any'}, {id: 'all', name: 'All'}],

				hasDataPopulated: true,

				deletingLevelIndex: null,

				deletingLevelId: null,
			}
		},

		mounted(){
			
			const path = window.location.pathname;
			
			const cabId = getIdFromUrl(path);

			if (path.indexOf("edit") >= 0) {
				
				this.hasDataPopulated = false;
				
				this.title  = 'edit_cab';

				this.btnName = 'update';

				this.iconClass = 'fa fa-refresh';

				this.getInitialValues(cabId);
			}
		},

		methods:{

			loadCabData(){
				
				const path = window.location.pathname;

				const cabId = getIdFromUrl(path);

				//if it is edit page, we get initial data to begin with
				if (path.indexOf("edit") >= 0) {
					
					this.getInitialValues(cabId);
				}
			},

			getInitialValues(cabId){
				
				this.loading = true;
				
				axios.get('/service-desk/api/cab/'+cabId).then(res => {
					
					this.id = res.data.data.id;
					
					this.name = res.data.data.name;

					this.action_on_approve = res.data.data.action_on_approve;
          
          this.action_on_deny = res.data.data.action_on_deny;
					
					this.levels = res.data.data.levels;
					
					this.loading = false;
					
					this.hasDataPopulated = true;

				}).catch(err => {
					
					this.loading = false;
					
					this.hasDataPopulated = true;
				})
			},

			addLevel(){
				
				// adds more empty entries into level
				this.levels.push({
					id:0,
					name:'',
					order: this.levels.length + 1,
					match: 'all',
					approvers : {
						users:[],
						user_types:[],					
					}
				})
				
				this.scrollToSubmitButton();
			},

			scrollToSubmitButton(){
				
				// scrolling to the bottom of the div
				let x = document.getElementById("submit-cab");
				
				setTimeout(() => {
					
					x.scrollIntoView({behavior: "smooth", block: "center", inline:"center"});
				}, 2);
			},
			
			onChange(value, name){
				
				//in case of cab name
				let nameArray = name.split('-')

				if(nameArray.length == 1){
					
					this[name] = value;
				}

				let index = nameArray[nameArray.length - 1]

				//level-name is the prefix that is given to the name of dynamic component of level
				if(name.includes('level-name-')){
					
					this.levels[index].name = value
				}

				 //cab-user_type- is the prefix that is given to the name of dynamic component of approver user_type
        if(name.includes('cab-user-type-')){
          this.levels[index].approvers.user_types = value
        }

				//cab-agent- is the prefix that is given to the name of dynamic component of approver agent
				
				if(name.includes('cab-agent-')){
					
					this.levels[index].approvers.users = value
				}
				
				//level-match- is the prefix that is given to static field component that represents matcher
				if(name.includes('level-match-')){
					
					this.levels[index].match = value
				}
			},

			isValid(){
				
				const { errors, isValid } = validateCabSettings(this.$data);
				
				if (!isValid) {
					
					return false;
				}

				return true;
			},

			onSubmit(){

				if(this.isValid()){

					this.loading = true;

					const data = {};

					data['id'] = this.id;

					data['name'] = this.name;

					data['levels'] = this.getFormattedLevelData();

					data['action_on_approve'] = this.action_on_approve ? this.action_on_approve.id : '';

					data['action_on_deny'] = this.action_on_deny ? this.action_on_deny.id : '';
					
					axios.post('/service-desk/api/cab',data).then(res =>{
						
						successHandler(res, 'cab');
						
						this.loading = false;

						//if it is in create mode it should redirect to index page
						if (!(window.location.pathname.indexOf("edit") >= 0)) {
							
							window.location = this.basePath() + '/service-desk/cabs';
						} else {

							this.loadCabData();
						}

					}).catch(err => {
						
						errorHandler(err, 'cab')
						
						this.loading = false;
					})
				}
			},

			getFormattedLevelData(){
				
				let levels = this.$data.levels.map(level => {
					
					return {
						
						id: level.id,
						
						name: level.name,
						
						order: level.order,
						
						match: level.match,
						
						approvers :{
							
							users: extractOnlyId(level.approvers.users),

							user_types: extractOnlyId(level.approvers.user_types)
						}
					}
				})
				return levels;
			},

			onDelete(){
				
				//if id is zero, removes it from the array
				if(this.deletingLevelId != 0){
					
					this.loading = true;
					// send a delete request to the server
					axios.delete('/service-desk/api/cab/'+ this.deletingLevelId +'/level').then(res =>{
						
						successHandler(res,'cab');
						
						this.loadCabData();
						
						this.loading = false;
					
					}).catch(err => {
						
						errorHandler(err,'cab');
					})				
				}

				//remove that particular index from the array
				this.levels.splice(this.deletingLevelIndex, 1);
				
				this.showDeletePopUp = false;
			},

			deleting(index, id){
				
				this.deletingLevelIndex = index;
				
				this.deletingLevelId = id;
				
				this.showDeletePopUp = true;
			}
		},

		watch : {
			
			showDeletePopUp(val){
			
				if(!val){
					
					this.deletingLevelIndex = null;
					
					this.deletingLevelId = null;
				}
			}
		},

		components: {
			
			"text-field": require("components/MiniComponent/FormField/TextField"),
			
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
			
			"static-select": require("components/MiniComponent/FormField/StaticSelect"),
			
			"modal": require('components/Common/Modal'),
			
			"alert": require("components/MiniComponent/Alert"),
			
			"custom-loader": require("components/MiniComponent/Loader"),
		}
	};

</script>

<style>
	.cab-levels{
		border: 1px solid #eee;
		padding: 10px;
		margin: 5px;
	}
	.box-header .close {
		margin-top: -4px;
		font-size: x-large;
	}
</style>
