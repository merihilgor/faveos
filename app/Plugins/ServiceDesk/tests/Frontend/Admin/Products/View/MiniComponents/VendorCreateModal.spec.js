import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import VendorCreateModal from '../../../../../../views/js/components/Admin/Products/View/MiniComponents/VendorCreateModal.vue';

window.eventHub = new Vue();

describe('VendorCreateModal',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(VendorCreateModal,{
			
			stubs: ['modal','loader','vendor'],

			propsData  : { onClose : jest.fn(), productId : 1, showModal : true },
			
			mocks:{ lang:(string)=>string },
			
		})  
	}
	
	beforeEach(() => {
		 
		updateWrapper();
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('calls `onClose` method when `onCompleted` method calld',()=>{

		updateWrapper()

		wrapper.vm.onCompleted()

		expect(wrapper.vm.onClose).toHaveBeenCalled()

	});
})