import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import ReleaseCreateModal from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ReleaseCreateModal.vue';

window.eventHub = new Vue();

describe('ReleaseCreateModal',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(ReleaseCreateModal,{
			
			stubs: ['modal','loader','release'],

			propsData  : { onClose : jest.fn(), changeId : 1, showModal : true },
			
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

	// it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{

	// 	updateWrapper()

	// 	wrapper.setData({ loading : false, subject : 'release', asset_ids : [ { id : 1, name : 'ww'}]})

	// 	wrapper.vm.isValid = () =>{return true}

	// 	wrapper.vm.onSubmit()

	// 	expect(wrapper.vm.loading).toBe(true)

	// 	mockSubmitRequest(400);

	// 	setTimeout(()=>{

	// 		expect(wrapper.vm.loading).toBe(false)

	// 		expect(wrapper.vm.onClose).toHaveBeenCalled()

	// 		expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release')
			
	// 			done();
	// 	},1);
	// });

	// function mockSubmitRequest(status = 200,url = '/service-desk/api/release'){

	// 	moxios.uninstall();
		
	// 	moxios.install();
		
	// 	moxios.stubRequest(url,{
			
	// 		status: status,
			
	// 		response: {'success':true,'message':'successfully saved'}
	// 	})
	// }
})