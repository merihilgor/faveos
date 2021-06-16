import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ProblemDetach from '../../../../../views/js/components/Problem/View/MiniComponents/ProblemDetach.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ProblemDetach',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	   	wrapper = mount(ProblemDetach,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(),updateChangeData:jest.fn(), alertCompName : 'problemAssociates'},
		
			stubs:['modal','loader'],
		
		});
	}

	it('makes a asset detach api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ problemId : 1, assetId : 1});

		stubRequestAsset()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-detach-asset/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('makes a change detach api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ changeId : 1, problemId : 1});

		stubRequestChange()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-detach-change/1/1')

    	expect(wrapper.vm.updateChangeData).toHaveBeenCalled();

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ changeId : 1, problemId : 1});

		stubRequestChange(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-detach-change/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).not.toHaveBeenCalled();
	  
      done();
    },1)
	})

	function stubRequestAsset(status = 200,url = '/service-desk/api/problem-detach-asset/'+wrapper.vm.problemId+'/'+wrapper.vm.assetId){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	 }

	function stubRequestChange(status = 200,url = '/service-desk/api/problem-detach-change/'+wrapper.vm.problemId+'/'+wrapper.vm.changeId){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});