<template>
    <div>

        <Alert componentName="projectEdit" />

        <div class="box box-primary">

            <Loader v-if="loading" :animation-duration="4000" color="#1d78ff" :size="60"/>

            <div class="box-header with-border">
                <h2 class="box-title">{{ lang('project_edit') }}</h2>
            </div>

            <div class="box-body">

            <div class="row">

                <div class="col-sm-12">

                    <TextField 
                    :label="lang('project_name')" id="project_name" 
                    :onChange="onChange" :value="project_name" 
                    type="text" name="project_name" 
                    :required=required
                    />

                </div>

            </div> <!--row-->

            <div class="row">

                <div class="col-sm-12">
                <button class="btn btn-primary btn-small" @click.prevent="projectSubmit">
                    {{ lang('save') }}
                </button>
                </div>

            </div> <!--row-->

            </div>

        </div> <!--box-->
    </div>
</template>

<script>
import TextField from "components/MiniComponent/FormField/TextField";
import Loader from "components/MiniComponent/Loader";
import Alert from "components/MiniComponent/Alert";
import axios from "axios";
import {errorHandler, successHandler} from 'helpers/responseHandler';
import { getIdFromUrl } from 'helpers/extraLogics';

export default {
    components: {
        Alert,TextField,Loader
    },

    data() {
        return {
            project_name : '',
            required     : 'required',
            projectId    : '',
            loading: false
        }
    },

    beforeMount() {

        this.setUp();

    },

    methods: {

        setUp() {

            this.projectId = getIdFromUrl(window.location.pathname);
            let apiEndPoint = 'tasks/projects?'+'projectIds[]='+this.projectId;

            axios.get(apiEndPoint)
            .then(res => this.project_name = res.data.data.projects[0].name)
        },

        onChange(value, name){
            this[name]= value;
        },

        setAlert(msg) {
          errorHandler({
              response: {
                  status: 400,
                  data: {
                      message: msg
                  }
              }
          },'projectEdit');
      },

        projectSubmit() {

            if(!this.project_name) {
            this.setAlert(this.lang('project_name_empty'));
            return;
            } 

        let formData = {
          name : this.project_name
        }

        this.loading = true;

        axios.put('tasks/project/edit/'+this.projectId,formData)
        .then(res =>  {this.loading = false; successHandler(res,'projectEdit'); setTimeout(()=>window.location = this.basePath() + '/tasks/settings'),1500 })
        .catch(err => {this.loading = false; errorHandler(err,'projectEdit'); })

      }
    }
}
</script>