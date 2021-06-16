import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import VendorAssociates from '../../../../../views/js/components/Admin/Vendor/MiniComponents/VendorAssociates.vue';

import Vue from 'vue'

import Vuex from 'vuex'

let wrapper;

describe('VendorAssociates', () => {

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

		wrapper = mount(VendorAssociates,{
			
			stubs:['data-table'],
		   
		  mocks:{ lang: (string) => string },

		  methods : { basePath : jest.fn() },

      store
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

  it("return row->contract_start_date for `contract_start_date` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.contract_start_date('test', {'contract_start_date': '2012-10-12'})).toEqual("")
  })

  it("return row->contract_end_date for `contract_end_date` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.contract_end_date('test', {'contract_end_date': '2012-10-12'})).toEqual("")
  })

  it("return row->status.name for `status` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.status('test', {'product_status': {id: 1, name : 'status'}})).toEqual('status')
  })

  it("return row->department.name for `department` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.department('test', {'department': { id : 1, name : 'department'}})).toEqual("department")
  })

  it("`responseAdapter` set edit_url, delete_url to the data property", () => {

    wrapper.setProps({ category : 'products'});

    let responseAdpData = {
      "data": {
        "data": {
          "products": [
            {"view_url": "something", "id": 1},
          ],
          "total": 1
        }
      }
    }

    let responseAdpDataReturn = {"count": 1, "data": [{"id": 1, "view_url": "something"}]}
      expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });
})