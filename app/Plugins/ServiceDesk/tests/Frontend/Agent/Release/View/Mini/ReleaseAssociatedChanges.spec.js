import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ReleaseAssociatedChanges from '../../../../../../views/js/components/Agent/Release/View/Mini/ReleaseAssociatedChanges.vue';

import Vue from 'vue'

 window.eventHub = new Vue();
 
let wrapper;

describe('ReleaseAssociatedChanges', () => {

	beforeEach(()=>{

		wrapper = mount(ReleaseAssociatedChanges,{
			
			stubs:['data-table','release-change-detach'],

		  methods : { basePath : jest.fn() },
		})
	})

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->status->name for `status` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.status('test', {'status': { id : 1, name : 'status'}})).toEqual('status')
  })

  it("return row->priority->name for `status` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.priority('test', {'priority': { id : 1, name : 'priority'}})).toEqual('priority')
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
          "changes": [],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = {"count": 1, "data": []}
  
    expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("updates `showModal` value when `onClose` method called",()=>{

    wrapper.vm.onClose();

    expect(wrapper.vm.showModal).toEqual(false)
  });
})