<template>
  <a id="create-form-group-button" class="btn btn-primary pull-right" :href="base + '/service-desk/form-group/create'">
    <span style="padding-right: 5px;" class="fa fa-plus" aria-hidden="true"></span>
    {{ lang('new_asset_form_group') }}
    </a>
</template>

<script>
export default {
    name: 'create-form-group-button',

    data: () => ({

    /**
     * base url of the application
     * @type {String}
     */
    base: window.axios.defaults.baseURL
    })
}
</script>
