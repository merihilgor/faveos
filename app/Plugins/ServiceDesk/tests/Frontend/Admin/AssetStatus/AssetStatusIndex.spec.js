import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import AssetStatusIndex from '../../../../views/js/components/Admin/AssetStatus/AssetStatusIndex.vue';

import Vue from 'vue'

import Vuex from 'vuex'

let wrapper;

describe('AssetStatusIndex', () => {

	beforeEach(()=>{

    let store;

    let getters;

    getters = {
        
      formattedTime: () => () => {return ''},
    }

    store = new Vuex.Store({
    
      getters
    })

		wrapper = mount(AssetStatusIndex,{
			
			stubs:['data-table','alert'],
		   
		  mocks:{ trans: (string) => string },

		  methods : { basePath : jest.fn() },

      store
		})
	})

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->updated_at for `updated_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.updated_at('test', {'updated_at': '2012-10-12'})).toEqual("")
  })

  it("return row->created_at for `created_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.created_at('test', {'created_at': '2012-10-12'})).toEqual("")
  })

  it("requestAdapter method should return `sort`, `order`, `search`, `page` & `limit`", () => {
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

  it("`responseAdapter` set edit_url, delete_url to the data property", () => {

    let responseAdpData = {
      "data": {
        "data": {
          "data": [
            {"delete_url": "something","edit_url": "something", "id": 1},
          ],
          "total": 1
        }
      }
    }

    let responseAdpDataReturn = 
      {"count": 1, "data": [
        {"edit_url": "undefined/service-desk/asset-status/1/edit", "delete_url": "undefined/service-desk/api/asset-status/1", "id": 1}
      ]}
      expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });
})