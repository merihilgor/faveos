import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ContractAssets from '../../../../../../views/js/components/Agent/Contract/View/Mini/ContractAssets.vue';

import Vue from 'vue'

 window.eventHub = new Vue();
 
let wrapper;

describe('ContractAssets', () => {

	beforeEach(()=>{

		wrapper = mount(ContractAssets,{
			
			stubs:['data-table','contract-asset-detach'],

		  methods : { basePath : jest.fn() },
		})
	})

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->managed_by->name for `managed_by` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.managed_by('test', {'managed_by': { full_name : 'manager'}})).toEqual('manager')
  })

  it("return row->used_by->name for `used_by` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.used_by('test', {'used_by': { full_name : 'user'}})).toEqual('user')
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

  it("`responseAdapter` set detach, compName and change_id to the data property", () => {

    let responseAdpData = {
      "data": {
        "data": {
          "assets": [
            {"detach": true,"compName": "change","contract_id": 1},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = {"count": 1, "data": [{"contract_id": "", "compName": "", "detach": true}]}
  
    expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("updates `showModal` value when `onClose` method called",()=>{

    wrapper.vm.onClose();

    expect(wrapper.vm.showModal).toEqual(false)
  });
})