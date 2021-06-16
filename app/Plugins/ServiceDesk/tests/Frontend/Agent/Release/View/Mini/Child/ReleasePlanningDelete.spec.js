import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ReleasePlanningDelete from '../../../../../../../views/js/components/Agent/Release/View/Mini/Child/ReleasePlanningDelete.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ReleasePlanningDelete',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(ReleasePlanningDelete,{
	   	
	   	mocks : { trans:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), showModal : true, releaseId : 1, identifier : 'test-plan'},
		
			stubs:['modal','loader'],
		
		});
	}

	it('makes a delete api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		stubRequestDelete()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/delete/general-popup/1/sd_releases/test-plan')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})


	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()

		stubRequestDelete(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/delete/general-popup/1/sd_releases/test-plan')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	function stubRequestDelete(status = 200,url = '/service-desk/api/delete/general-popup/1/sd_releases/test-plan'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	 }
});