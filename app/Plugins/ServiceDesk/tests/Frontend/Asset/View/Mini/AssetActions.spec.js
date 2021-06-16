import {  mount } from '@vue/test-utils'

import Vue from 'vue'

import AssetActions from '../../../../../views/js/components/Asset/View/Mini/AssetActions.vue';

describe('AssetActions',() => {

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(AssetActions,{

			stubs: ['delete-modal','asset-associate-modal'],
			
			mocks:{ trans:(string)=>string },

			propsData : { asset : { id : 1 } },

			methods : { basePath : jest.fn() }
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it('updates `showAssociateModal & associate` value when `associateMethod` method called',() => {		
		
		wrapper.vm.associateMethod('contract');

		expect(wrapper.vm.showAssociateModal).toEqual(true);

		expect(wrapper.vm.associate).toEqual('contract');
	});

	it('updates `showAssociateModal & showDeleteModal` value when `onClose` method called',() => {		
		
		wrapper.vm.onClose();

		expect(wrapper.vm.showAssociateModal).toEqual(false);

		expect(wrapper.vm.showDeleteModal).toEqual(false);
	});
})