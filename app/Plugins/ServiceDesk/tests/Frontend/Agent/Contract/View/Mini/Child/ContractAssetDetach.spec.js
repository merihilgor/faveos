import { mount } from '@vue/test-utils'

import ContractAssetDetach from '../../../../../../../views/js/components/Agent/Contract/View/Mini/Child/ContractAssetDetach.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ContractAssetDetach',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	   	wrapper = mount(ContractAssetDetach,{
	   	
	   	mocks : { trans:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn(), alertCompName : 'contractAssociates'},
		
			stubs:['modal','loader'],
		
		});
	}

	it('makes a asset detach api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ contractId : 1, assetId : 1});

		stubRequestAsset()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-detach-asset/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)

	  	expect(wrapper.vm.onClose).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('updates `loading, isDisabled` value and not calls `onClose` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setProps({ contractId : 1, assetId : 1});

		stubRequestAsset(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

	  expect(wrapper.vm.isDisabled).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-detach-asset/1/1')

			expect(wrapper.vm.loading).toBe(false)

	  	expect(wrapper.vm.isDisabled).toBe(true)
	  
      done();
    },1)
	})

	function stubRequestAsset(status = 200,url = '/service-desk/api/contract-detach-asset/'+wrapper.vm.contractId+'/'+wrapper.vm.assetId){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	 }
});