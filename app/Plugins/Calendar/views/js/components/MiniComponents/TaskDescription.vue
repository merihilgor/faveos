<template>

    <div>

        <modal v-if="showModal" :showModal="showModal" :onClose="onClose">

            <div slot="title">

                <h4>{{lang('task_description')}}</h4>
            </div>

            <div slot="fields" id="task_description">

                <div class="row">

                    <div class="col-md-12" id="description">
                        <span v-html="description"></span>
                    </div>

                </div>

            </div>

        </modal>
    </div>
</template>

<script type="text/javascript">

    export default {

        name : 'task-description',

        description : 'Task description Modal component',

        props:{

            showModal:{type:Boolean,default:false},

            onClose:{type: Function, default : ()=>{}},

            description : { type : String, default : ''}
        },

        components:{'modal':require('components/Common/Modal.vue')}
    };
</script>

<style scoped>
    #task_description {
        margin-left: 15px;
        margin-right: 15px;
    }
    #description{
        overflow: auto;
        word-break: break-all;
        min-height: 150px;
        max-height: 300px;
    }

    .table{
        overflow-x: auto;
    }

    #description table{
        overflow-x: auto;
    }

    #description table, #description table td, #description table tr, #description table th {
        border: 1px solid #ddd;
        text-align: left;
    }

    #description table {
        border-collapse: collapse;
        width: max-content;
    }

    #description table th,  #description table td {
        padding: 15px;
    }
</style>