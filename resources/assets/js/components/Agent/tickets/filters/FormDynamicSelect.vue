<template>
    <div :ref="reference" class="form-dynamic-select">
       <!--  -->
      <v-select
        ref="faveoDynamicSelect"
        :dir="direction"
        :class="[{ 'field-danger': errors.has(holder)}, 'faveo-dynamic-select']"
        :id="api"
        v-model="modelValue"
        v-validate="validate"
        :filterable="false"
        label="name"
        :options="filter_options"
        placeholder='Search or Select'
        :data-vv-name="holder"
        :disabled="disableField"
        :multiple="multiple"
				@search="onSearch"
				@open="onOpen"
    		@close="onClose"
        @search:blur="clearSearchQuery"
        >

        <template slot="no-options" slot-scope="{search, loading, searching}">
          <template v-if="searching">No results found for <em>{{ search }}</em>. </template>
          <template v-else><span v-if="!isLoading">No options found.</span> </template>
        </template>

          <template slot="filter_option" slot-scope="filter_option">
            <div>
              {{ filter_option.label }}
              </div>
          </template>

          <template slot="filter_selected" slot-scope="filter_option">
            <div>
              {{ filter_option.label }}
            </div>
          </template>

          <template slot="option" slot-scope="option">

            <div class="d-center">
              <faveo-image-element id="img"  v-if="option.profile_pic" :source-url="option.profile_pic" :classes="['profile-picture']"/>
              {{ option.name }}
            </div>
          </template>

          <template slot="selected-option" slot-scope="option">

            <div class="selected d-center">

              <faveo-image-element id="img" v-if="option.profile_pic" :source-url="option.profile_pic" :classes="['profile-picture']"/>

              {{ option.name }}
            </div>
          </template>

          <template slot="list-footer" v-if="hasNextPage && api">
            <li ref="load" class="loader-area">
              <loader :duration="4000" :size="25"></loader>
            </li>
          </template>

       </v-select>
       <span v-show="errors.has(holder)" class="help is-danger"> {{ errors.first(holder) }} </span>
    </div>
</template>
<script>
import axios from 'axios';
import _ from 'lodash-core';
import { mapGetters } from 'vuex';

import { findObjectByKey, boolean } from "helpers/extraLogics.js";
import { assignLabel } from "helpers/assignCustomFieldLabel.js";
import { store } from "store";
import { errorHandler } from 'helpers/responseHandler';

import vSelect from "vue-select";

export default {
  props: {
    /**
     * dependency type (for eg. `help-topics`, `departments`,`types`, `priorities`)
     */
    api : { type : String, default: null },

    /**
     * the key by which validation can be identified
     */
    holder : {type : String, required: true},

    /**
     * String that can be used to refer to this component from outside
     * NOTE: Not really aware of why it is written
     */
    reference : {type : String, default : ""},

    /**
     * Validation object needed for validating inputs
     */
    validate :  {type : Object, default: null},

    /**
     * Form field node which will be used only in forms not in filters
     */
    node : {type: Object, default: null},

    /**
     * The panel who is accessing the component `agent` or `user`
     */
    person : {type: String, default: 'user'},

    /**
     * If field needs to be disabled
     */
    disableField : {type: Boolean, default: false},

    /**
     * If multiple selections are allowed
     */
    multiple : {type: Boolean, default: false},

    /**
		 * An array of strings or objects to be used as dropdown choices.
		 * If you are using an array of objects, vue-select will look for a label key (ex. [{label: 'Canada', value: 'CA'}]).
		 * A custom label key can be set with the label prop.
		 */
    elements: { type: Array, default () { return [] } },
    
    keyToBind: { type: String }
  },


  data() {
    return {
      filter_options: [],
      isLoading: false,
      modelValue: null,
      lang: "en",
      linkingObject: this.$store.getters.getLinkedFormValues,
      observer: null,
      page: 0,
      next_page_url: ''
    };
  },
  components: {
    'v-select': vSelect,
    'faveo-image-element': require('components/Common/FaveoImageElement'),
    'loader': require('components/Extra/Loader'),
  },

  beforeMount() {
		this.filter_options = Boolean(this.elements) ? this.elements : [];
	},

  computed: {
    ...mapGetters({mode: 'getFormMode'}),

    hasNextPage() {
			return this.next_page_url !== null;
		},

    direction(){
      let language = localStorage.getItem('LANGUAGE');
      if(language == 'ar'){
        return 'rtl';
      }
      return 'ltr';
    },

    /**
     * Decides whether supplements should be appended to the API or not.
     * For eg. if mode is workflow-listner, linking is not required, so it will return false
     */
    shallAppendSupplements(){
      return (this.mode != 'workflow-listener');
    },
  },

  watch: {
    "node.value"(newvalue, oldValue) {
      /**
       * Fixes #6252
       * no need to call `updateModalValue` if newvalue and old value are same
       */
      if(newvalue && newvalue.id === oldValue) {
        return;
      }
      this.updateModelValue(newvalue);
    },

    modelValue(newvalue, oldValue) {
      this.tagsChanged(newvalue)
    },

    elements(newValue, oldValue) {
			if (newValue) {
				this.filter_options = newValue;
			}
		},
  },

  mounted(){

    if(this.api) {
      this.observer = new IntersectionObserver(this.infiniteScroll);
    }

    // make sure linking object is reset, so that helptopic and department is not linked in intial state
    this.$store.dispatch('resetLinkingObject');

    if(boolean(this.node)){

      this.updateModelValue(this.node.value);

      if(this.api) {
        this.urlAutoFillHandling();
      }
    }
  },

  methods: {

    /**
     * Updates `modelValue` with `node` updation or initialization
     * @param  {String|Object} nodeValue
     * @return {undefined}
     */
    updateModelValue(nodeValue){
      if (nodeValue != undefined && (nodeValue.name != undefined || this.isValidArrayOfObject(nodeValue))) {
        this.modelValue = nodeValue;
      } else if (this.node.title === 'Api') {
        this.modelValue = this.elements.find(elem => elem[this.keyToBind] == this.node.value)
      } else if (nodeValue == "") {
        this.modelValue = null;
        this.$validator.reset();
      }
    },

    /**
     * Checks if the value is valid array of objects with id and name as a must key
     * @return {Boolean}
     */
    isValidArrayOfObject(values){

      if(!Array.isArray(values)){
        return false;
      }

      let validObjectCount = values.filter(value => {
        if( value != undefined && value.id != undefined || value.name != undefined){
          return true;
        }
        return false;
      })

      return boolean(validObjectCount);
    },

    /**
     * Handles click and searches in v-select field
     * @param  {String} searchQuery search string
     * @return {Promise}
     */
    onClick(strictSearch = false, isRefresh = false, target) {

      let linkingObject = [];

      if (this.api.includes("help-topics") || this.api.includes("departments")) {

        // when selected helptopic is non-empty and current field is department
        if (this.linkingObject.help_topic !== "" && this.node.title == "Department") {
          linkingObject = [this.linkingObject.help_topic];
        }
        // when selected department is non-empty and current field is helptopic
        if (this.linkingObject.department !== "" && this.node.title == "Help Topic") {
          linkingObject = [this.linkingObject.department];
        }
      }
      return this.getOptionsFromServer(linkingObject, strictSearch, isRefresh, target)
    },

    /**
     * Clear search query on search:blur event
     */
    clearSearchQuery() {
      this.$refs.faveoDynamicSelect.onEscape();

      // Juhaad -- Maaf kr dena
      if (boolean(this.node) && (this.node.title == 'Help Topic' || this.node.title == 'Department')) {
        this.filter_options = [];
        this.page = 0;
        this.next_page_url = '';
      }
    },

    //searching
    onSearch(search) {
      this.page = 1;
      this.searchQuery = search;
      this.search(this);
    },

    search: _.debounce(function () {
			if (this.api) {
				this.onClick(false, true);
			} else {
				// Search elements locally
				this.filterListElements();
			}
		}, 350),

		/** Filter list elements */
		filterListElements() {
			this.filter_options = this.elements.filter((element) => element['name'].toLowerCase().includes(this.searchQuery.toLowerCase()));
		},

    /**
     * Changes value of the field and handles custom field manipulation for the same
     * @param  {Object} x
     * @return {undefined}
     */
    tagsChanged(x) {
      this.modelValue = x;

      if (boolean(this.modelValue) && boolean(this.node)) {
        this.node.options = [];
        this.node.options.push(this.modelValue);
        this.setLabelsOfForms(this.node.options);
      }

      this.$validator.validate(this.holder, x);
      this.$emit("getValue", x);

      if(boolean(this.node) && this.node.title == "Help Topic"){
        this.$store.dispatch('setSelectedHelptopic', this.modelValue);
      }

      if(boolean(this.node) && this.node.title == "Department"){
        this.$store.dispatch('setSelectedDepartment', this.modelValue);
      }
    },

    /**
     * Sets labels as required by child fields
     * @param {String} data
     */
    setLabelsOfForms(data) {
      // passing first iteration as false as it is child of helpTopic OR department
      assignLabel(data, this.lang, this.person, false);
    },

    /**
     * Makes Api call to the server for getting dependency options
     * @param  {String} searchQuery typed search query
     * @param  {Array} supplements extra parameters required by backend
     * @return {Promise}
     */
    getOptionsFromServer(supplements, strictSearch = false, isRefresh = false, target) {
      this.isLoading = true;

      let params = {}

      if(boolean(this.searchQuery)){
        params['search-query'] = this.searchQuery;
      }
      if(boolean(strictSearch)){
        params['strict-search'] = strictSearch;
      }
      if(boolean(supplements) && this.shallAppendSupplements){
        params['supplements'] = supplements
      }
      params.page = this.page;
      params.paginate = 1;

      return axios.get(this.api, { params : params}).then(resp => {
        this.postApiResponseOperations(resp.data.data, isRefresh, target);
      }).catch(err => {
        errorHandler(err);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },

    async postApiResponseOperations(responseData, isRefresh, target) {
      try {
        this.next_page_url = responseData.next_page_url;
        if(isRefresh) {
          this.filter_options = responseData.data;
        } else {
          const ul = target.offsetParent;
          const scrollTop = target.offsetParent.scrollTop;
          this.filter_options.push(...responseData.data);
          await this.$nextTick();
          ul.scrollTop = scrollTop;
        }
      } catch (error) {
        console.log(error);
      }
    },

    /**
     * Makes dependecies get autofilled when given in the url
     * @return {Void}
     */
    urlAutoFillHandling(){

      let parameterValue = this.getParameterFromUrl();

      if(boolean(parameterValue)){

        this.searchQuery = parameterValue;

        // append that as search param and make the first entry get selected
        this.onClick(true, true).then(res => {

          if(boolean(this.filter_options)){
            // picking first value of the array so that the best match of the
            // url can be selected
            this.modelValue = this.filter_options[0];
          }
        });
      }
    },

    async infiniteScroll ([{isIntersecting, target}]) {
      if (isIntersecting) {
				this.page += 1;
				this.onClick(false, false, target);
      }
		},

		async onOpen() {
			if (this.hasNextPage && this.api) {
        await this.$nextTick();
        this.observer.observe(this.$refs.load);
      }
		},

    onClose() {
      if(this.observer) {
				this.observer.disconnect();
			}
		},

    /**
     * Gets dependency parameter from url
     * @return {String|null}
     */
    getParameterFromUrl(){
      // if key in the url matches key here, it should populate that field
      let url = new URL(window.location.href);

      // extract url-key out of this.node.unique
      // since node.unique is in format helpTopic
      let urlkey = this.node.unique.replace('_id', "");
      return url.searchParams.get(urlkey);
    },
  }
};
</script>

<style scoped>
.profile-picture {
  border: 1px solid transparent;
  width: 18px;
  border-radius: 10px;
  margin-top: -1px;
}
.loader-area {
	padding-top: 3px;
	height: 37px;
}
</style>
