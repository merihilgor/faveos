import { mount, createLocalVue } from '@vue/test-utils';

import AttachAsset from '../../../views/js/components/Asset/AttachAsset.vue';

import Vue from 'vue';

import Vuex from 'vuex';

import moxios from 'moxios';

import sinon from 'sinon'

window.eventHub = new Vue();

window.scrollTo = () => { };

window.alert = () => { };

jest.mock('helpers/responseHandler')

jest.mock('helpers/extraLogics')

describe('AttachAsset',()=>{

	let wrapper;
	const updateWrapper = ()=>{

		wrapper = mount(AttachAsset,{

			 propsData : { data : JSON.stringify({ 'title' : 'title', 'id' : 1 })},

			 mocks : {
			 	lang:(string)=> string
			 },

			 stubs : ['modal','alert','custom-loader','asset-list-with-checkbox','dynamic-select']
		})
	};

	beforeEach(()=>{

		moxios.install();
		
		updateWrapper();
	
	});

	afterEach(()=>{

		moxios.uninstall()

	});

	it('Is a vue instance',()=>{
		
		expect(wrapper.isVueInstance()).toBeTruthy();
	
	});

	it('Shows Modal popup if `showModal` is true',()=>{

		wrapper.setProps({ showModal : true });

		expect(wrapper.find('modal-stub').exists()).toBe(true);

	});

	it('Does not show Modal popup if `showModal` is false',()=>{

		wrapper.setProps({ showModal : false });

		expect(wrapper.find('modal-stub').exists()).toBe(false);
		
	});

	it('Shows loader if `loading` is true',(done)=>{

		wrapper.setProps({ showModal : true });

		wrapper.setData({ loading : true });

		setTimeout(()=>{
			
			expect(wrapper.find('custom-loader-stub').exists()).toBe(true);
			
			done();
		},50)

	});

	it('Does not show loader if `loading` is false',()=>{

		wrapper.setProps({ showModal : true });

		wrapper.setData({ loading : false });

		expect(wrapper.find('custom-loader-stub').exists()).toBe(false);
		
	});

	it('updates `loading` value correctly when `onSubmit` method is called',(done) => {

    updateWrapper()

    expect(wrapper.vm.loading).toBe(false)

    wrapper.setData({ assetIds : [1,2,3,4]})

    wrapper.vm.onSubmit()

    expect(wrapper.vm.loading).toBe(true)

    mockSubmitRequest();
    
    setTimeout(()=>{
    	
    	expect(wrapper.vm.loading).toBe(false)
      	done();
    },1);
  });

	it('updates `asset_type` value when onChange method is called with suitable parameters for asset_type name',()=>{
    	
    	wrapper.vm.onChange(10,'asset_type');
    	
    	expect(wrapper.vm.asset_type).toEqual(10);
  	
  	});

	it("updates `assetIds` value when assetData method called",()=>{

		expect(wrapper.vm.assetIds).toEqual([]);

		wrapper.vm.assetsData([1,2,3]);
		
		expect(wrapper.vm.assetIds).toEqual([1,2,3]);
	})


	it('does not data table if `asset_type` is equal to empty',(done)=>{

		wrapper.setProps({ showModal : true });

		wrapper.vm.$data.asset_type = ''

		setTimeout(()=>{
		
			expect(wrapper.find('asset-list-with-checkbox-stub').exists()).toBe(false);

			done()
		
		},1)

	});

	it('`alert-stub` should exists when page created', () => {

		wrapper.setProps({ showModal : true });

        expect(wrapper.find('alert-stub').exists()).toBe(true)
    });


  	it('calls `onSubmit` when clicks on Save button',()=>{
    	
    	updateWrapper()

    	wrapper.vm.onSubmit =jest.fn()

    	wrapper.setProps({ showModal : true })

    	wrapper.find('#submit_btn').trigger('click')

    	expect(wrapper.vm.onSubmit).toHaveBeenCalled()

  	});

  	it('makes an AJAX call when onSubmit method is called',(done)=>{


	    updateWrapper()

	    wrapper.setProps({ showModal : true,data: JSON.stringify({ id : 1}) })

	    wrapper.setData({ assetIds : [1,2,3] })

	    wrapper.vm.onSubmit()

	    mockSubmitRequest();

	    setTimeout(()=>{
	     
	      expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/ticket-attach-assets')
	     
	      done();
	    
	    },1);

  });

  	it('calls `onClose` after 3 seconds after API is a success in `onSubmit`',(done)=>{
	   
	    wrapper.setProps({onClose: jest.fn()});

	    wrapper.setProps({ showModal : true,data: JSON.stringify({ id : 1}) })

	    wrapper.setData({ assetIds : [1,2,3] })

		mockSubmitRequest();

	    wrapper.vm.onSubmit();

	    setTimeout(()=>{
	      expect(wrapper.vm.onClose).toHaveBeenCalled()
	      done();
	    }, 3100);
	  })

    it('ticketId', done => { 
      
      expect(wrapper.vm.ticketId).toEqual(1)

      wrapper.setProps({
        data: JSON.stringify({ id : 2})
      })
      
       expect(wrapper.vm.ticketId).toEqual(2)
      
      done()
    })

  	it("`loading` value should be false if api returns errorresponse",(done)=>{
		
		updateWrapper();

		wrapper.setProps({ showModal : true,data: JSON.stringify({ id : 1}) })

	    wrapper.setData({ assetIds : [1,2,3] })

		mockSubmitFailRequest();

		wrapper.vm.onSubmit()

		setTimeout(()=>{
	    
	     	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/ticket-attach-assets')
	    	
	    	expect(wrapper.vm.loading).toEqual(false)

	      	done();
	    
	    },1)
	})

	it("returns the number", () => {
	    expect(wrapper.vm.ticketId).toBe(1);
	});

	it('updates `apiUrl` value when `onApply` method called',()=>{

		expect(wrapper.vm.$data.apiUrl).toBe('');

		wrapper.vm.onApply();

		expect(wrapper.vm.$data.apiUrl).toBe('/service-desk/api/asset-list?');
	})

  	function mockSubmitRequest(){
	 
	  moxios.uninstall();
	 
	  moxios.install();
	 
	  moxios.stubRequest('/service-desk/api/ticket-attach-assets',{
	 
	        status: 200,
	 
	        response: {'success':true,'message':'successfully saved'}
	 
	      })
	
	}

	function mockSubmitFailRequest(){
	 
	  moxios.uninstall();
	 
	  moxios.install();
	 
	  moxios.stubRequest('/service-desk/api/ticket-attach-assets',{
	 
	        status: 400,
	 
	        response: {'success':false,'message':'failed'}
	 
	      })
	
	}

	function stubRequest(status = 200,url = '/service-desk/api/dependency/asset_types?meta=true'){
	   
	    moxios.uninstall();
	   
	    moxios.install();
	   
	    moxios.stubRequest(url,{
	   
	      status: status,
	   
	      response : {
	      	data : {
	      		asset_types : [{id:1,name:'test'}]
	      	}
	      }
	   
	    })
	  }
})