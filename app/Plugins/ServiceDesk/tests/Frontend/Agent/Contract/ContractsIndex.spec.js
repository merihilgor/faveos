import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ContractsIndex from '../../../../views/js/components/Agent/Contract/ContractsIndex.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

window.eventHub = new Vue();

jest.mock('helpers/extraLogics');

let wrapper;

describe('ContractsIndex', () => {

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

    wrapper = mount(ContractsIndex,{
      
      stubs:['data-table','table-actions','alert','contracts-filter','faveo-box'],
       
      mocks:{ lang: (string) => string },

      methods : {

        basePath : jest.fn()
      },
      store
    })
  })

  it('`contract_status` should return `---` if `contract_status` value empty', () => {
  	
    expect(wrapper.vm.options.templates.contract_status('test', {'contract_status': ''})).toEqual("---");
  })

  it('`expiry` should return `---` if `expiry` value empty', () => {
    
    expect(wrapper.vm.options.templates.expiry('test', {'expiry': ''})).toEqual("---");
  })

  it('`contract_renewal_status` should return `---` if `contract_renewal_status` value empty', () => {
    
    expect(wrapper.vm.options.templates.contract_renewal_status('test', {'contract_renewal_status': ''})).toEqual("---");
  })

  it('`vendor` should return `value` if `vendor` value present', () => {
    
    expect(wrapper.vm.options.templates.vendor('test', {'vendor': {id:1,name:'value'}})).toEqual("value");
  })

  it('`contract_type` should return `value` if `contract_type` value present', () => {
    
    expect(wrapper.vm.options.templates.contract_type('test', {'contract_type': {id:1,name:'value'}})).toEqual("value");
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
          "contracts": [
            {"delete_url": "something","view_url": "something","edit_url": "something", "id": 1, 'status' : true, 'is_edit' : true, 'is_delete' : true},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = 
      {"count": 1, "data": [
        {"delete_url": "undefined/service-desk/api/contract/1", "edit_url": "undefined/service-desk/contracts/1/edit", "id": 1,'is_edit' : true, 'is_delete' : true, "view_url": "undefined/service-desk/contracts/1/show", 'status' : true}, 
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
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/contract-list")
    
      done()
    },1)
  })

  it("function `resetFilter` will set the url to default", (done) => {
    
    wrapper.vm.filterOptions = []
    
    setTimeout(()=>{
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/contract-list")
    
      done()
    },1)
  })

  it("`toggleFilterView` should toggle the isShowFilter value", () => {
    
    wrapper.vm.isShowFilter = false
    
    wrapper.vm.toggleFilterView()
    
    expect(wrapper.vm.isShowFilter).toBe(true)
  })
})