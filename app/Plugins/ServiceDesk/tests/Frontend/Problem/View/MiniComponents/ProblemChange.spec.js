import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ProblemChange from '../../../../../views/js/components/Problem/View/MiniComponents/ProblemChange.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ProblemChange',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(ProblemChange,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { updateChangeData : jest.fn(), onClose : jest.fn(), problemId : 1,type : 'attach_existing_change'  },
		
			stubs:['modal','loader','dynamic-select'],
		
		});
	}

	it("calls `onClose` method when `onCompleted` method called",()=>{

		populateWrapper();

		wrapper.vm.onCompleted();

		expect(wrapper.vm.onClose).toHaveBeenCalled();

		expect(wrapper.vm.updateChangeData).toHaveBeenCalled();
	});

	it('updates `change` of the release when onChange method is called with suitable parameters',()=>{
		
		populateWrapper();

		wrapper.vm.onChange({ id : 1, name :'change'}, 'change');
	
		expect(wrapper.vm.change).toEqual({ id : 1, name :'change'});

		expect(wrapper.vm.isDisabled).toEqual(false)
	})

	it('makes an api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({ change : { id : 1, name : 'change'} });

		stubRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-attach-change/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.updateChangeData).toHaveBeenCalled();

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ change : { id : 1, name : 'change'} });

		stubRequest(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-attach-change/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).not.toHaveBeenCalled();
	  
      done();
    },1)
	});

	function stubRequest(status = 200,url = '/service-desk/api/problem-attach-change/'+wrapper.vm.problemId+'/'+wrapper.vm.change.id){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});