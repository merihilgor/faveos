import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import VendorAddModal from '../../../../../../views/js/components/Admin/Products/View/MiniComponents/VendorAddModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('VendorAddModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(VendorAddModal,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), productId : 1 },
		
			stubs:['modal','loader','dynamic-select'],
		
		});
	}
	
	it('Is a vue instance',()=>{
		
		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)	
	});

	it('updates `vendorIds` when onChange method is called with suitable parameters',()=>{
	
		wrapper.vm.onChange([{ id : 1, name :'vendor'}], 'vendorIds');
	
		expect(wrapper.vm.vendorIds).toEqual([{ id : 1, name :'vendor'}]);

		expect(wrapper.vm.isDisabled).toEqual(false)
	})

	it('makes an api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({ vendorIds : [{ id : 1, name :'vendor'}] });

		stubRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/product/attach/vendor')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ vendorIds : [{ id : 1, name :'vendor'}] });

		stubRequest(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/product/attach/vendor')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)
	  
      done();
    },1)
	})

	function stubRequest(status = 200,url = '/service-desk/api/product/attach/vendor'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});