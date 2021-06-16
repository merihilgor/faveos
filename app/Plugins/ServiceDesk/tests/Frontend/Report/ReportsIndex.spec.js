import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ReportsIndex from '../../../views/js/components/Report/ReportsIndex.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import moxios from 'moxios'

let wrapper;

describe('ReportsIndex', () => {

	beforeEach(()=>{

    moxios.install()

    let store;

    let getters;

    getters = {
        
      formattedTime: () => () => {return ''},
        
      formattedDate:()=> () => {return ''},
    }

    store = new Vuex.Store({
    
      getters
    })

		wrapper = mount(ReportsIndex,{
			
			stubs:['data-table','alert'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

		  	basePath : jest.fn()
		  },

      store
		})
	})

  afterEach(() => {
    moxios.uninstall()
  })

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->updated_at for `updated_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.updated_at('test', {'updated_at': '2012-10-12'})).toEqual("")
  })

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
          "reports": {
            data : [  {"id": 1} ],
            "total": 1
          }
        }
      }
    }
    
    let responseAdpDataReturn = { data: { data: { assets: [ {"id": 1}], total: 1 } }, count: 1 }

    expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual({"count": 1, "data": [{"delete_url": "undefined/service-desk/api/reports/1", "edit_url": "undefined/service-desk/reports/assets/edit/1", "id": 1, "view_url": "undefined/service-desk/reports/assets/1"}]})
  });
})
