import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import AssociatedChanges from '../../../../views/js/components/Ticket/Changes/AssociatedChanges.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

let wrapper;

let store;

let getters;

describe('AssociatedChanges', () => {

	beforeEach(()=>{

    getters = {
        
      getStoredTicketId: () => {return 1}
    }

    store = new Vuex.Store({
    
      getters
    })

		wrapper = mount(AssociatedChanges,{
			
			stubs:['data-table','loader','alert','change-detach-modal'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

		  	basePath : jest.fn()
		  },

		  store
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

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
      "search_query": "something",
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
            {"id": 1,name:'name'},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = {"count": 1, "data": [ {"id": 1, "name": "name"}]}
    
    expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("updates `changeId, change_type` values when `onDetach` method called",()=>{

    wrapper.vm.onDetach(1,'initiated',true);

    expect(wrapper.vm.changeId).toEqual(1);

    expect(wrapper.vm.change_type).toEqual('initiated')
  })

  it("makes `showModal` false when `onClose` method called",()=>{

    wrapper.vm.onClose();

    expect(wrapper.vm.showModal).toEqual(false)
  })
})