import { mount } from '@vue/test-utils'

import AssetAssociateModal from '../../../../../views/js/components/Asset/View/Mini/AssetAssociateModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('AssetAssociateModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	   	wrapper = mount(AssetAssociateModal,{
	   	
	   	mocks : { trans:(string)=> string },
	   	
	   	propsData : { showModal : true, assetId : 1, associate : 'change',  onClose : jest.fn() },
		
			stubs:['modal','loader','dynamic-select']
		});
	}

	it("updates `associated` value when `onChange` method called",()=>{

		populateWrapper()

		wrapper.vm.onChange([{id:1,name:'name'}],'associated');

		expect(wrapper.vm.associated).toEqual([{"id": 1, "name": "name"}])
	})

	it('makes an API call when`onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData( { associated : [{id:1,name:'name'}]});

		stubRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    	setTimeout(()=>{
    	
    		expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/attach-asset-services/1')

			expect(wrapper.vm.loading).toBe(false)

		  	expect(wrapper.vm.onClose).toHaveBeenCalled();
		  
	      done();
	    },1)
	})

	it('updates `loading` value if api returns error response',(done)=>{
		
		populateWrapper()

		stubRequest(400)
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    	setTimeout(()=>{
    	
    		expect(wrapper.vm.loading).toBe(false)
	  
      	done();
    	},1)
	})

	function stubRequest(status = 200,url = '/service-desk/api/attach-asset-services/1'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	 }
});