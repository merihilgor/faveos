import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ProblemPlanningModal from '../../../../../views/js/components/Problem/View/MiniComponents/ProblemPlanningModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

import * as validation from "../../../../../views/js/validator/problemPlanningValidation";

jest.mock('helpers/responseHandler')

let wrapper;

describe('ProblemPlanningModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(ProblemPlanningModal,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), problemId : 1, identifier : 'root-cause', updateChangeData : jest.fn(), showModal : true},
		
			stubs:['modal','loader','ck-editor','file-upload','text-field'],
		
		});
	}

	it('makes a get api call as soon as `getInitialValues` is called',(done)=>{
		
		populateWrapper()

		dataRequest()
		
		wrapper.vm.getInitialValues()
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_problem/root-cause')

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.description).toEqual("description")

			expect(wrapper.vm.attachment).toEqual({"size": 1024, "type": "jpg", "value": "name"})

      done();
    },1)
	})

	it('sets values as empty when api returns error response',(done)=>{
		
		populateWrapper()

		dataRequest(400)
		
		wrapper.vm.getInitialValues()
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_problem/root-cause')

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.description).toEqual("")

			expect(wrapper.vm.attachment).toEqual("")

      done();
    },1)
	})

	it('updates `subject` of the change when onChange method is called with suitable parameters for change name',()=>{
	
		wrapper.vm.onChange({ type :'jpg', size : 1024, value :'name'}, 'attachment');
	
		expect(wrapper.vm.attachment).toEqual({ type :'jpg', size : 1024, value :'name'});
	})

	it('isValid - should return false ', done => {
       	
       	validation.validateProblemPlanningSettings = () =>{return {errors : [], isValid : false}}
      
      	expect(wrapper.vm.isValid()).toBe(false)
      
      	done()
    })

    it('isValid - should return true ', done => {
       
       	validation.validateProblemPlanningSettings = () =>{return {errors : [], isValid : true}}
      
      	expect(wrapper.vm.isValid()).toBe(true)
      
      	done()
    })

	it('makes a post api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({description : 'description', attachment : { type :'jpg', size : 1024, value :'name'}})
		
		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		submitRequest()
		
		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_problem')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(false)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();

	  	// expect(wrapper.vm.updateChangeData).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()
	
		wrapper.setData({description : 'description', attachment : { type :'jpg', size : 1024, value :'name'}})
		
		wrapper.vm.isValid = () =>{return true}
		
		wrapper.vm.onSubmit()

		submitRequest(400)
		
		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_problem')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(false)

	  	expect(wrapper.vm.onClose).not.toHaveBeenCalled();
	  
      done();
    },1)
	})

	function dataRequest(status = 200,url = '/service-desk/api/general-popup/' + wrapper.vm.problemId + '/sd_problem/' + wrapper.vm.identifier){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {
	    	data : {
	    		general_info : {

	    			description : 'description',

	    			attachment : { type :'jpg', size : 1024, value :'name'}
	    		}
	    	}
	    }
	  })
	 }

	function submitRequest(status = 200,url = '/service-desk/api/general-popup/'+wrapper.vm.problemId +'/sd_problem'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});