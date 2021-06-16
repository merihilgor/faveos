<template>
	<div id="backup-details-container">

		<!-- Datatable alert  -->
		<alert componentName="dataTableModal" />

		<!-- `this` component alert -->
		<alert componentName="system-backup-list" />

		<div class="box box-primary backup-table">
			<div class="box-header with-border row">

				<div class="col-md-12">

					<div class="row">
						<div class="col-md-9 form-group" style="display: flex">
							<label for="backup_storage_path" class="control-label">{{ lang('backup_storage_path')}}:</label>
							<div class="input-box__sbl col-sm-10">
								<input type="text" class="form-control" v-model="storagePath" id="backup_storage_path"
									placeholder="path/to/store/system-backup">
							</div>
						</div>

						<div class="col-md-3">

							<button id="create-backup__sb" class="btn btn-primary right" @click="perrformBackup()"
								:disabled="perrformBackupClicked">
								<span style="padding-right: 5px;"
									:class="perrformBackupClicked ? 'fa fa-spinner fa-spin' : 'fa fa-download'" aria-hidden="true"></span>
								{{lang('take_system_backup')}}
							</button>

							<button v-if="backupTriggeredSuccessfully" class="btn btn-default" style="float: right; margin-right: 0.5rem;" @click="refreshDatatable()" title="Refresh table">
								<span>
									<i :class="refreshingDatatable ? 'fa fa-refresh fa-spin' : 'fa fa-refresh'" id="refresh-data-table__sbl"></i>
								</span>
							</button>

						</div>
					</div>
				</div>
			</div>

			<div class="box-body" id="backup-list">
				<data-table :url="apiUrl" :dataColumns="columns" :option="options" />
			</div>
		</div>

	</div>
</template>

<script type="text/javascript">
	import { lang } from 'helpers/extraLogics';
	import axios from 'axios';
	import { errorHandler, successHandler } from "helpers/responseHandler";
	import FaveoBox from 'components/MiniComponent/FaveoBox';
	import { mapGetters } from 'vuex'

	export default {
		name: 'system-backup-list',
		description: 'System backup table component',
		components: {
			'data-table': require('components/Extra/DataTable'),
			'custom-loader': require("components/MiniComponent/Loader"),
			'alert': require('components/MiniComponent/Alert'),
			'faveo-box' : FaveoBox,
    },
		data: () => {
			return {

			loadingSpeed: 4000,

			/** Refreshing datatable to get new element */
			refreshingDatatable: false,

			/** Loading status(for the first entry to the page) */
			isLoading: true,

			/** Take backup button clicked by user */
			perrformBackupClicked: false,

			/** Backup started successfully in background*/
			backupTriggeredSuccessfully: false,

			/**
			 * backup storage location
			 */
			storagePath: '',
		
			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl: 'api/backup/list',
			/**
			* columns required for datatable
			* @type {Array}
			*/
			columns: ['filename', 'db_name', 'created_at', 'version', 'action'],
			
			/**
			* Options required for datatable
			* @type {Object}
			*/
			options: {}
			}
		},

		beforeMount() {

			this.configureOptions();

			/** Get default/saved storage location path */
			axios.get('api/get-backup-path')
        .then(response => {
					this.storagePath = response.data.data.path;
				})
				.catch(error => {
					errorHandler(err, 'system-backup-list');
				})
				.finally(()=> {
					this.isLoading = false;
				})
		},

		computed:{
			...mapGetters(['formattedTime'])
		},

		methods: {

			// Configure datatable option properties
			configureOptions() {
				const that = this;
				this.options = {
					headings: {
						filename: 'File name',
						db_name: 'DB name',
						created_at: 'Created At',
						version: 'Version',
						action:'Actions'
					},
					texts: {
						'filter': '',
						'limit': ''
					},
					templates: {
						filename: 'downloadable-row', // download icon will appear for this column
						db_name: 'downloadable-row', // download icon will appear for this column
						action: 'data-table-actions', // template for the ACTION column,

						created_at(h, row) {
							return that.formattedTime(row.created_at)
						}
					},
					sortable: ['created_at', 'version'],
					pagination: {
						chunk: 5,
						nav: 'scroll'
					},
					requestAdapter(data) {
						return {
							'sort-field': data.orderBy ? data.orderBy : 'version',
							'sort-order': data.ascending ? 'asc' : 'desc',
							'search-query': data.query.trim(),
							'page': data.page,
							'limit': data.limit,
						}
					},
					responseAdapter({data}) {
						return {
								data: data.message.data.map(data => {
									data.delete_url = window.axios.defaults.baseURL + '/api/delete-backup/' + data.id ;
									return data;
								}),
								count: data.message.total
							}
						}
					}
			},

			/** Start backup in background */
			perrformBackup() {
				this.perrformBackupClicked = true
				axios.post('api/backup/take-system-backup', { path: this.storagePath })
        .then(res => {
					successHandler(res, 'system-backup-list');
					this.backupTriggeredSuccessfully = true;
        })
        .catch(err => {
          errorHandler(err, 'system-backup-list');
				})
				.finally(()=> {
					this.perrformBackupClicked = false;
				})
			},

			/** Emit event to refresh datatable */
			refreshDatatable() {
				this.refreshingDatatable = true;
				window.eventHub.$emit('refreshData');
				/** Spin loader for user exp */
				setTimeout(() => {
					this.refreshingDatatable = false;
				}, 500);
			}

		},
	};
</script>

<style type="text/css" scoped>
.right{
  float: right;
}
.input-box__sbl > input {
  border: 1px solid #fff;
}

.control-label {
	padding-top: 7px;
}

.form-horizontal .control-label {
	text-align: inherit;
	padding-left: 1.5rem;
}
.backup-table {
	padding: 0px;
}

</style>