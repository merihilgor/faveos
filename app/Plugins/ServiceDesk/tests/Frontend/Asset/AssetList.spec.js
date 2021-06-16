import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import AssetList from '../../../views/js/components/Asset/AssetList.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from "vuex";

let localVue = createLocalVue()
localVue.use(Vuex)

window.axios = require('axios');
window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]');

let wrapper;

describe('AssetList', () => {

	let store

    let getters

	beforeEach(()=>{
		getters = {
			getStoredTicketId: () => { return 1 }
		}

    	store = new Vuex.Store({
    		getters
    	})

		wrapper = mount(AssetList,{
			stubs:['data-table','sdesk-data-table-actions','alert'],
		    mocks:{
		        lang: (string) => string,
		    },
		    propsData : { data : JSON.stringify({ show_detach_asset : true})},
		    store
		})
	})

	it('is a vue instance', () => {
	    expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
        expect(wrapper.find('data-table-stub').exists()).toBe(true)
    });

    it("initial value of `apiUrl` is equall to `/service-desk/api/asset-list?ticket_ids=1`",()=>{
    	expect(wrapper.vm.apiUrl).toEqual('/service-desk/api/asset-list?ticket_ids=1')
    });

    it("updates `apiUrl` value when store value changed",()=>{

		 store.hotUpdate({
            getters: {
              ...getters,
              getStoredTicketId: () => {return 2}
            }
        })

		const wrapper = shallowMount(AssetList, { propsData : { data : JSON.stringify({ show_detach_asset : true})},store, localVue })

	    expect(wrapper.vm.apiUrl).toBe('/service-desk/api/asset-list?ticket_ids=2')

	})
})
