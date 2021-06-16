import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import VendorModal from '../../../../../views/js/components/Admin/Vendor/MiniComponents/VendorModal.vue';

import moxios from 'moxios';

jest.mock('helpers/responseHandler')

describe('VendorModal',() => {

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(VendorModal,{

			stubs: ['modal','alert','loader'],

			mocks:{	lang:(string)=>string },

			propsData : { data : { id : 1} },

			methods : { 
				
				redirect : jest.fn(), 
			}
		})  
	}
	
	beforeEach(() => {
		 
		updateWrapper();

		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('makes an API call', (done) => {
		
		updateWrapper();
		
		wrapper.vm.afterRespond = jest.fn();

		wrapper.vm.onSubmit();

		deleteRequest();
		
		setTimeout(()=>{
		
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/vendor/1')
			
			expect(moxios.requests.mostRecent().config.method).toEqual('delete');

			expect(wrapper.vm.afterRespond).toHaveBeenCalled();
			
			done();
		
		},50)
	})

	it("calls `afterRespond` method if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.afterRespond = jest.fn();

		wrapper.vm.onSubmit();

		deleteRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.afterRespond).toHaveBeenCalled();

			done();

		},50)
	});

	it("calls `onClose` method when `afterRespond` method called",()=>{

		updateWrapper();

		wrapper.setProps({onClose : jest.fn()});

		wrapper.vm.afterRespond();

		expect(wrapper.vm.onClose).toHaveBeenCalled();

		expect(wrapper.vm.loading).toEqual(false);
		
		expect(wrapper.vm.isDisabled).toEqual(true);

	});

	function deleteRequest(status = 200,url = '/service-desk/api/vendor/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {}
		})
	}
})