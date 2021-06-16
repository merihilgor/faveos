import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ChangesIndex from '../../../../views/js/components/Agent/Changes/ChangesIndex.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

window.eventHub = new Vue();

let wrapper;

describe('ChangesIndex', () => {

	beforeEach(()=>{

		let store;

    let getters;

    getters = {
        
      formattedTime: () => () => {return ''},
        
      formattedDate:()=> () => {return ''},
    }

    store = new Vuex.Store({
    
      getters
    })

		wrapper = mount(ChangesIndex,{
			
			stubs:['data-table','data-table-actions','alert','changes-filter'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

		  	basePath : jest.fn()
		  },

		  store
		})
	})

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->created_at for `created_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.created_at('test', {'created_at': '2012-10-12'})).toEqual("")
  })

  it("requestAdapter method should return `sort-field`, `sort-order`, `search-query`, `page` & `limit`", () => {
    let reqAdptData = {
      "orderBy": "id",
      "ascending": true,
      "query": "something",
      "page": 10,
      "limit": 10
    }
    let reqAdptDataReturn = {
      "sort-field": "id",
      "sort-order": "desc",
      "search-query": "something",
      "page": 10,
      "limit": 10
    }
    expect(wrapper.vm.options.requestAdapter(reqAdptData)).toEqual(reqAdptDataReturn)
  });

  it("`responseAdapter` set edit_url, delete_url and view_url to the data property", () => {

    let responseAdpData = {
      "data": {
        "data": {
          "changes": [
            {"delete_url": "something","view_url": "something","edit_url": "something", "id": 1},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = 
      {"count": 1, "data": [
      	{"delete_url": "undefined/service-desk/api/change/1", "edit_url": "undefined/service-desk/changes/1/edit", "id": 1, "view_url": "undefined/service-desk/changes/1/show"}, 
      ]}
      expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("`isShowFilter` will false if function `selectedFilters` triggered with closeEvent", () => {
  
    wrapper.vm.selectedFilters("closeEvent")
  
    expect(wrapper.vm.isShowFilter).toBe(false)
  });

  it("call resetFilter function if `selectedFilters` is triggered with resetEvent", (done) => {
    
    wrapper.vm.selectedFilters("resetEvent")
    
      setTimeout(()=>{
    
        expect(wrapper.vm.isShowFilter).toBe(false)
    
        done();
      },1)
  })

  it("function `resetFilter` will set the value to blank", (done) => {
    
    wrapper.vm.filterOptions = [{section : [{value: "something"}]},{section : [{value: "something"}]},]
    
    wrapper.vm.selectedFilters("resetEvent")
    
    setTimeout(()=>{
    
    	expect(wrapper.vm.filterOptions[0].section[0].value).toBe("")
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/change-list")
    
    	done()
    },1)
  })

  it("function `resetFilter` will set the url to default", (done) => {
    
    wrapper.vm.filterOptions = []
    
    setTimeout(()=>{
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/change-list")
    
      done()
    },1)
  })

  it("`toggleFilterView` should toggle the isShowFilter value", () => {
    
    wrapper.vm.isShowFilter = false
    
    wrapper.vm.toggleFilterView()
    
    expect(wrapper.vm.isShowFilter).toBe(true)
  })
})