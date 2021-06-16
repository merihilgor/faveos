import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ChangeUpdateModal from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ChangeUpdateModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

let wrapper;

describe('ChangeUpdateModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(ChangeUpdateModal,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), changeId : 1, identifier : 'reason'},
		
			stubs:['modal','loader','ck-editor','file-upload'],
		
		});
	}
	
	it('Is a vue instance',()=>{
		
		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)	
	});

	it('makes a get api call as soon as `getInitialValues` is called',(done)=>{
		
		populateWrapper()

		dataRequest()
		
		wrapper.vm.getInitialValues()
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_changes/reason')

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
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_changes/reason')

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

	it('makes a post api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({description : 'description', attachment : { type :'jpg', size : 1024, value :'name'}})
		
		wrapper.vm.onSubmit()

		submitRequest()
		
		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_changes')

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
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/general-popup/1/sd_changes')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(false)

	  	expect(wrapper.vm.onClose).not.toHaveBeenCalled();
	  
      done();
    },1)
	})

	function dataRequest(status = 200,url = '/service-desk/api/general-popup/' + wrapper.vm.changeId + '/sd_changes/' + wrapper.vm.identifier){
	   
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

	function submitRequest(status = 200,url = '/service-desk/api/general-popup/'+wrapper.vm.changeId +'/sd_changes'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});