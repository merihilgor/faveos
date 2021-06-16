import AssociatedAssetList from './../../views/js/components/AssociatedAssetList.vue';

import { mount, shallow } from '@vue/test-utils';

import sinon from 'sinon';

import Vue from 'vue';

window.eventHub =new Vue();

let wrapper;

const populateWrapper = () =>{
	wrapper = mount(AssociatedAssetList,{
		mocks : { lang:(string) => string },
		stubs : ['associated-assets','associated-problems','loader','alert']
	})
}

describe('AssociatedAssetList',()=>{

	beforeEach(()=>{
		populateWrapper()
	});

	afterEach(()=>{
	});

	it('Is a vue intance',()=>{
		expect(wrapper.isVueInstance()).toBeTruthy()
	});


	it('Shows `Loader` when loading is `true`',()=>{
		wrapper.setData({ loading : true })
		expect(wrapper.find('loader-stub').exists()).toBe(true)
	});

});