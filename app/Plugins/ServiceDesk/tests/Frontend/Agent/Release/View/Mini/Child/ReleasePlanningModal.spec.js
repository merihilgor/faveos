import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ReleasePlanningModal from '../../../../../../../views/js/components/Agent/Release/View/Mini/Child/ReleasePlanningModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

let wrapper;

describe('ReleasePlanningModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(ReleasePlanningModal,{
	   	
	   	mocks : { trans:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), releaseId : 1, identifier : 'build-plan'},
		
			stubs:['modal','loader','ck-editor','file-upload'],
		
		});
	}

	it('makes a get api call as soon as `getInitialValues` is called',(done)=>{
		
		populateWrapper()

		dataRequest()
		
		wrapper.vm.getInitialValues()
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_releases/build-plan')

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
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_releases/build-plan')

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.description).toEqual("")

			expect(wrapper.vm.attachment).toEqual("")

      done();
    },1)
	})

	it('updates `description` of the change when onChange method is called',()=>{
	
		wrapper.vm.onChange('description', 'description');
	
		expect(wrapper.vm.description).toEqual('description');
	})

	it("updates `selectedFile, attachment_delete` values when `onFileSelected` method called",()=>{

		wrapper.vm.onFileSelected('value','name',true);

		expect(wrapper.vm.selectedFile).toEqual('value');

		expect(wrapper.vm.attachment_delete).toEqual(true);
	})

	it('makes a post api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({description : 'description', attachment : { type :'jpg', size : 1024, value :'name'}})
		
		wrapper.vm.onSubmit()

		submitRequest()
		
		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_releases')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(false)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()
	
		wrapper.setData({description : 'description', attachment : { type :'jpg', size : 1024, value :'name'}})
		
		wrapper.vm.onSubmit()

		submitRequest(400)
		
		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_releases')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(false)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	function dataRequest(status = 200,url = '/service-desk/api/general-popup/' + wrapper.vm.releaseId + '/sd_releases/' + wrapper.vm.identifier){
	   
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

	function submitRequest(status = 200,url = '/service-desk/api/general-popup/'+wrapper.vm.releaseId +'/sd_releases'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});