import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ReleaseDetach from '../../../../../../../views/js/components/Agent/Release/View/Mini/Child/ReleaseDetach.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ReleaseDetach',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	   	wrapper = mount(ReleaseDetach,{
	   	
	   	mocks : { trans:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), alertCompName : 'releaseAssociates'},
		
			stubs:['modal','loader'],
		
		});
	}

	it('makes a asset detach api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ releaseId : 1, category : 'asset', fieldId : 1});

		stubRequestAsset()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/detach-asset/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('makes a change detach api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ category : 'change', fieldId : 1, releaseId : 1});

		stubRequestChange()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/detach-change/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ category : 'change', fieldId : 1, releaseId : 1});

		stubRequestChange(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/detach-change/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(false)

	  	expect(wrapper.vm.onClose).not.toHaveBeenCalled();
	  
      done();
    },1)
	})

	function stubRequestAsset(status = 200,url = '/service-desk/api/release/detach-asset/'+wrapper.vm.releaseId+'/'+wrapper.vm.fieldId){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	 }

	function stubRequestChange(status = 200,url = '/service-desk/api/release/detach-change/'+wrapper.vm.releaseId+'/'+wrapper.vm.fieldId){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});