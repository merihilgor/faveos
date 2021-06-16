import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ReleasesIndex from '../../../../views/js/components/Agent/Release/ReleasesIndex.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

window.eventHub = new Vue();

let wrapper;

describe('ReleasesIndex', () => {

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

		wrapper = mount(ReleasesIndex,{
			
			stubs:['data-table','table-actions','alert','releases-filter'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

		  	basePath : jest.fn()
		  },
      store
		})
	})

  it('`planned_start_date` should return `---` if `planned_start_date` value empty', () => {
    
    expect(wrapper.vm.options.templates.planned_start_date('test', {'planned_start_date': ''})).toEqual("---");
  })

  it('`planned_end_date` should return `---` if `planned_end_date` value empty', () => {
    
    expect(wrapper.vm.options.templates.planned_end_date('test', {'planned_end_date': ''})).toEqual("---");
  })

  it('`status` should return `---` if `status` value empty', () => {
    
    expect(wrapper.vm.options.templates.status('test', {'status': ''})).toEqual("---");
  })

  it('`priority` should return `value` if `priority` value present', () => {
    
    expect(wrapper.vm.options.templates.priority('test', {'priority': {id:1,name:'value'}})).toEqual("value");
  })

  it('`release_type` should return `value` if `release_type` value present', () => {
    
    expect(wrapper.vm.options.templates.release_type('test', {'release_type': {id:1,name:'value'}})).toEqual("value");
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
          "releases": [
            {"delete_url": "something","view_url": "something","edit_url": "something", "id": 1,'is_edit':true,'is_delete':true},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = 
      {"count": 1, "data": [
      	{"delete_url": "undefined/service-desk/api/release/1", "edit_url": "undefined/service-desk/releases/1/edit", "id": 1, "view_url": "undefined/service-desk/releases/1/show",'is_edit':true,'is_delete':true}, 
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
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/release-list")
    
    	done()
    },1)
  })

  it("function `resetFilter` will set the url to default", (done) => {
    
    wrapper.vm.filterOptions = []
    
    setTimeout(()=>{
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/release-list")
    
      done()
    },1)
  })

  it("`toggleFilterView` should toggle the isShowFilter value", () => {
    
    wrapper.vm.isShowFilter = false
    
    wrapper.vm.toggleFilterView()
    
    expect(wrapper.vm.isShowFilter).toBe(true)
  })

  it("updates `planned_start_date` value in `filterOptions` when `selectedFilters` called with `planned_start_date`", () => {
   
    let value = {
      "planned_start_date": "last::10~minute"
    }

    wrapper.vm.selectedFilters(value);

    expect(wrapper.vm.filterOptions[2].section[1].value).toEqual(value.planned_start_date)
    
    expect(wrapper.vm.isShowFilter).toBe(false);
  })

  it("updates `planned_end_date` value in `filterOptions` when `selectedFilters` called with `planned_end_date`", () => {
   
    let value = {
      "planned_end_date": ['Fri Jan 31 2020 00:00:00 GMT+0530 (India Standard Time)','Fri Jan 31 2020 00:00:00 GMT+0530 (India Standard Time)']
    }

    wrapper.vm.selectedFilters(value);

    expect(wrapper.vm.filterOptions[2].section[2].value).toEqual(value.planned_end_date)
    
    expect(wrapper.vm.isShowFilter).toBe(false);
  })


  it("set 0th index of `filterOptions` to value.contract_ids if not null", () => {
   
    let value = {
      "release_ids": [{
        "id": 1,
        "name": "release"
      }]
    }
    wrapper.vm.selectedFilters(value);

    expect(wrapper.vm.filterOptions[0].section[0].value).toEqual(value.release_ids)
    
    expect(wrapper.vm.isShowFilter).toBe(false);
  })
})