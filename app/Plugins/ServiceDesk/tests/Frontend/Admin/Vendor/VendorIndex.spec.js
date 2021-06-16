import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import VendorIndex from '../../../../views/js/components/Admin/Vendor/VendorIndex.vue';

import Vue from 'vue'

let wrapper;

describe('VendorIndex', () => {

	beforeEach(()=>{

		wrapper = mount(VendorIndex,{
			
			stubs:['data-table','alert'],
		   
		  mocks:{ lang: (string) => string },

		  methods : { basePath : jest.fn() }
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

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
          "vendors": [
            {"delete_url": "something","edit_url": "something","view_url": "something", "id": 1},
          ],
          "total": 1
        }
      }
    }

    let responseAdpDataReturn = 
      {"count": 1, "data": [
        {"edit_url": "undefined/service-desk/vendor/1/edit", "delete_url": "undefined/service-desk/api/vendor/1","view_url": "undefined/service-desk/vendor/1/show", "id": 1}
      ]}
      expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });
})