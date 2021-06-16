import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ReleaseAddModal from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ReleaseAddModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ReleaseAddModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(ReleaseAddModal,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), changeId : 1 },
		
			stubs:['modal','loader','dynamic-select'],
		
		});
	}
	
	it('Is a vue instance',()=>{
		
		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)	
	});

	it('updates `release_id` of the release when onChange method is called with suitable parameters',()=>{
	
		wrapper.vm.onChange({ id : 1, name :'release'}, 'release_id');
	
		expect(wrapper.vm.release_id).toEqual({ id : 1, name :'release'});

		expect(wrapper.vm.isDisabled).toEqual(false)
	})

	it('makes an api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({ release_id : { id : 1, name : 'release'} });

		stubRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/change/attach-release/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ release_id : { id : 1, name : 'release'} });

		stubRequest(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/change/attach-release/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).not.toHaveBeenCalled();
	  
      done();
    },1)
	})

	function stubRequest(status = 200,url = '/service-desk/api/change/attach-release/'+wrapper.vm.changeId+'/'+wrapper.vm.release_id.id){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});