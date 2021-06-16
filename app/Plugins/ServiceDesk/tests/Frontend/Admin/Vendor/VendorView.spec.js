import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import VendorView from '../../../../views/js/components/Admin/Vendor/VendorView.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler')

Vue.use(Vuex)

describe('VendorView',() => {

	let wrapper;

	const updateWrapper = () =>{

		let store;

    let getters;

    getters = {
        
      formattedTime: () => () => {return ''},
        
      formattedDate:()=> () => {return ''},
    }

    store = new Vuex.Store({
    
      getters
    })

		extraLogics.getIdFromUrl = () =>{return 1}

		wrapper = mount(VendorView,{

			stubs: ['vendor-modal','alert','custom-loader','vendor-associates-table'],

			mocks:{	lang:(string)=>string },

			methods : { 
				
				redirect : jest.fn(), 
				
				basePath : jest.fn() 
			},

			store
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
		
		wrapper.vm.getInitialValues(1);

		stubRequest();
		
		setTimeout(()=>{
		
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/vendor/1')
				
			expect(wrapper.vm.vendorData).toEqual( {"contract": {"name": "contract"}, "id": 1, "name": "vendor", "product": {"name": "product"}, "status": {"id": 1, "name": "status"}});

			done();
		
		},50)
	})

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		updateWrapper();

		wrapper.vm.getInitialValues(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

	it("retuns subString value when `subString` method called",()=>{

		expect(wrapper.vm.subString('abcdefghijklmnopqrstuvwxyz')).toEqual("abcdefghijklmnopqrst...")
	})

	it("updates `title, showModal` value when `modalMethod` method called",()=>{

		wrapper.vm.modalMethod('delete');

		expect(wrapper.vm.title).toEqual('delete');

		expect(wrapper.vm.showModal).toEqual(true);
	})

	it("updates `showModal` value as `false` when `onClose` method called",()=>{

		wrapper.vm.onClose();

		expect(wrapper.vm.showModal).toEqual(false);
	})

	it("updtaes `category, tabloading` value when `associates` method called",(done)=>{

		wrapper.vm.associates('products');

		expect(wrapper.vm.category).toEqual('products');

		expect(wrapper.vm.tabloading).toEqual(true);

		setTimeout(()=>{

			expect(wrapper.vm.tabloading).toEqual(false);

			done();

		},2100)
	})

	function stubRequest(status = 200,url = '/service-desk/api/vendor/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					vendor : {
						id : 1,
						name : 'vendor',
						status : {id:1,name:'status'},
						contract : {
							name : 'contract'
						},
						product : {
							name : 'product'
						}
					}
				}
			}
		})
	}
})