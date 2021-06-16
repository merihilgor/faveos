import { mount, createLocalVue } from '@vue/test-utils';

import AssetListWithCheckbox from '../../../views/js/components/Asset/AssetListWithCheckbox.vue';

import Vue from 'vue'

import Vuex from "vuex";

let localVue = createLocalVue()
localVue.use(Vuex)

window.axios = require('axios');
window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]');

let wrapper;

describe('AssetListWithCheckbox', () => {

	let store

    let getters

	beforeEach(()=>{
		getters = {
			getStoredTicketId: () => { return {} }
		}

    	store = new Vuex.Store({
    		getters
    	})

		wrapper = mount(AssetListWithCheckbox,{
			stubs:['data-table','data-table-actions','alert'],
		    mocks:{
		        lang: (string) => string,
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

    it("type check of `tickets` prop",()=>{
		 expect(wrapper.vm.$options.props.tickets.type).toBe(Function)
    })
})
