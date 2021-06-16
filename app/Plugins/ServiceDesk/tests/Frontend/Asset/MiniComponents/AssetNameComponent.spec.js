import { mount } from '@vue/test-utils';

import sinon from 'sinon'

import AssetNameComponent from '../../../../views/js/components/Asset/MiniComponents/AssetNameComponent.vue';

import Vue from 'vue'

window.axios = require('axios');

window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]');

let wrapper;

describe('AssetNameComponent', () => {

	beforeEach(()=>{
	
		wrapper = mount(AssetNameComponent,{

			propsData : { data : { name : 'test'}},
		    mocks:{
		        lang: (string) => string,
		    },
		})
	})

	it("is a vue instance", () => {
	    expect(wrapper.isVueInstance()).toBeTruthy()
	});

	 it("`asset-name` should exists when page created", () => {

        expect(wrapper.find('#asset-name').exists()).toBe(true)
    });

	 it("`url` and `href` value should be same", () => {
	
    	expect(wrapper.vm.$data.url).toEqual(wrapper.find('#asset-name').attributes().href)
    });

	 it("updates `href` value when url value change", () => {
		
		wrapper.setData({ url : 'test'})

    	expect(wrapper.find('#asset-name').attributes().href).toEqual('test')
    });

})
