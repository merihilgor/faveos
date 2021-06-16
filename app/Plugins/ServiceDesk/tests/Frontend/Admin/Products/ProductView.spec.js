import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import ProductView from '../../../../views/js/components/Admin/Products/ProductView.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

window.eventHub = new Vue();

describe('ProductView',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}
		
		wrapper = mount(ProductView,{

			stubs: ['product-details','product-associates','alert','loader'],
			
			mocks:{
			
				lang:(string)=>string
			},
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getValues(1);
		
		stubRequest();

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/product/1')
			
			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getValues(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

  function stubRequest(status = 200,url = '/service-desk/api/product/1'){
	  
	  moxios.uninstall();
	  
	  moxios.install();
	  
	  moxios.stubRequest(url,{
	    
	    status: status,
	    
	    response : {
	    	data : {}
	    }
	  })
	}
})