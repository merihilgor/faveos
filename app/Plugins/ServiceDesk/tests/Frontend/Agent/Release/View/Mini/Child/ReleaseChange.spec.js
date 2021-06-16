import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ReleaseChange from '../../../../../../../views/js/components/Agent/Release/View/Mini/Child/ReleaseChange.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ReleaseChange',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(ReleaseChange,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { updateReleaseData : jest.fn(), onClose : jest.fn(), releaseId : 1  },
		
			stubs:['modal','loader','dynamic-select'],
		
		});
	}

	it('updates `change` of the release when onChange method is called with suitable parameters',()=>{
		
		populateWrapper();

		wrapper.vm.onChange([{ id : 1, name :'change'}], 'change');
	
		expect(wrapper.vm.change).toEqual([{ id : 1, name :'change'}]);
	})

	it('makes an api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({ change : [{ id : 1, name : 'change'}] });

		stubRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/attach-change/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.updateReleaseData).toHaveBeenCalled();

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ change : [{ id : 1, name : 'change'}] });

		stubRequest(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/attach-change/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	});

	function stubRequest(status = 200,url = '/service-desk/api/release/attach-change/'+wrapper.vm.releaseId){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});