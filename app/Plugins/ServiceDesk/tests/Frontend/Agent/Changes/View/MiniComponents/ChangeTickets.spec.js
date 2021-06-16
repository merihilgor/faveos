import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ChangeTickets from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ChangeTickets.vue';

import sinon from 'sinon'

import Vue from 'vue'

let wrapper;

describe('ChangeTickets', () => {

	beforeEach(()=>{

		wrapper = mount(ChangeTickets,{
			
			stubs:['data-table','ticket-detach-modal'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

		  	basePath : jest.fn()
		  }
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->assigned for `assigned` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.assigned('test', { assigned : {'id': 1, full_name : 'name'}})).toEqual("name")
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
          "tickets": [
            {id: 1,subject:'name'},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = {"count": 1, "data": [ {"id": 1, "subject": "name"}]}
    
    expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("updates `ticketId, ticket_type` values when `onDetach` method called",()=>{

    wrapper.vm.onDetach(1,'initiated');

    expect(wrapper.vm.ticketId).toEqual(1);

    expect(wrapper.vm.ticket_type).toEqual('initiated')
  })

  it("makes `showModal` false when `onClose` method called",()=>{

    wrapper.vm.onClose();

    expect(wrapper.vm.showModal).toEqual(false)
  })
})