import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ProductsIndex from '../../../../views/js/components/Admin/Products/ProductsIndex.vue';

import Vue from 'vue'

let wrapper;

describe('ProductsIndex', () => {

	beforeEach(()=>{

		wrapper = mount(ProductsIndex,{
			
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

   it("return row->product_status for `product_status` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.product_status('test', {'product_status': { id : 1, name : "staus"}})).toEqual("staus")
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
          "products": [
            {"delete_url": "something","edit_url": "something","view_url": "something","id": 1},
          ],
          "total": 1
        }
      }
    }

    let responseAdpDataReturn = 
      {"count": 1, "data": [

        {"edit_url": "undefined/service-desk/products/1/edit","view_url": "undefined/service-desk/products/1/show", "delete_url": "undefined/service-desk/api/product-delete/1","id": 1}

      ]}
      expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });
})