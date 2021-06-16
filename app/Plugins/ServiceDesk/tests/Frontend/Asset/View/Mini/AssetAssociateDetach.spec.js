import { mount } from '@vue/test-utils'

import AssetAssociateDetach from '../../../../../views/js/components/Asset/View/Mini/AssetAssociateDetach.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('AssetAssociateDetach',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	   	wrapper = mount(AssetAssociateDetach,{
	   	
	   	mocks : { trans:(string)=> string },
	   	
	   	propsData : { showModal : true, assetId : 1, associateId : 1, category  : 'change',  onClose : jest.fn(), compName : 'assetAssociates'},
		
			stubs:['modal','loader']
		});
	}

	it('makes an API call when`onSubmit` is called',(done)=>{
		
		populateWrapper()

		stubRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  	expect(wrapper.vm.isDisabled).toBe(true)
	   
    	setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/detach-asset-services/1')

			expect(wrapper.vm.loading).toBe(false)

		  	expect(wrapper.vm.isDisabled).toBe(true)

		  	expect(wrapper.vm.onClose).toHaveBeenCalled();
		  
	      done();
	    },1)
	})

	it('updates `loading, isDisabled` value if api returns error response',(done)=>{
		
		populateWrapper()

		stubRequest(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  	expect(wrapper.vm.isDisabled).toBe(true)
	   
    	setTimeout(()=>{
    	
    		expect(wrapper.vm.loading).toBe(false)

	  		expect(wrapper.vm.isDisabled).toBe(false)
	  
      	done();
    	},1)
	})

	function stubRequest(status = 200,url = '/service-desk/api/detach-asset-services/1'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	 }
});