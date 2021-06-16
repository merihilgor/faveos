import { mount, createLocalVue, shallowMount } from '@vue/test-utils';
import AssociatedContractList from '../../../views/js/components/Ticket/AssociatedContractList.vue';


import Vue from 'vue'

import Vuex from "vuex";

let localVue = createLocalVue()
localVue.use(Vuex)

let wrapper;

describe('AssociatedContractList', () => {

	let store

    let getters

	beforeEach(()=>{
		getters = {
			getStoredTicketId: () => { return 1 }
		}

    	store = new Vuex.Store({
    		getters
    	})

		wrapper = mount(AssociatedContractList,{
			stubs:['data-table'],
		    mocks:{
		        lang: (string) => string,
		    },

		    propsData:{
		    	 data : JSON.stringify({ 'id' : 1 })
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

    it("initial value of `apiUrl` is equal to `/service-desk/api/contract/ticket/1`",()=>{
    	expect(wrapper.vm.apiUrl).toEqual('/service-desk/api/contract/ticket/1')
    });

    it("updates `apiUrl` value when store value changed",()=>{

    	store.hotUpdate({
            getters: {
              ...getters,
              getStoredTicketId: () => {return 2}
            }
        })
		 const wrapper1 = mount(AssociatedContractList,{
			stubs:['data-table','alert'],
		    mocks:{
		        lang: (string) => string,
		    },
		     propsData:{
		    	 data : JSON.stringify({ 'id' : 1 })
		    },
		    store
		})
	    expect(wrapper1.vm.apiUrl).toBe('/service-desk/api/contract/ticket/2')

	})
})