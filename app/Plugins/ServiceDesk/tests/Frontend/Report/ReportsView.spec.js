import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ReportView from '../../../views/js/components/Report/ReportView.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import moxios from 'moxios'


describe('ReportView',() => {

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(ReportView,{
			
			stubs: ['static-select','chart','agent-asset-index','custom-loader','loader'],
			
			methods : {

				basePath : jest.fn(),

				redirect : jest.fn()
			},

			mocks:{ lang:(string)=>string },
			
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

	it('static-select should exists when page created', () => {
    
	    expect(wrapper.find('static-select-stub').exists()).toBe(true)
	});

	it('agent-asset-index should exists when page created', () => {
    
	    expect(wrapper.find('agent-asset-index-stub').exists()).toBe(true)
	});


	it('makes an API Call and Chart API chartApiUrl gets updated',() => {

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest('service-desk/api/reports/1',{

			status: 200,

			response : {
					
				success:true,
					
				data : {
					
					report_filter : { id : 1, name : 'test', description : 'desc', filter_meta : [{key:'asset_ids', value_meta:[{id:1,name:'name1'},{id:2,name:'name2'}]}] }
				}	
			}
		})

		moxios.wait(() => {
	       expect(wrapper.vm.chartApiUrl).not.toEqual('/service-desk/api/asset-list?count=true&');
	       done()
	    })


	});


	it('makes an API call and Datatable API apiUrl is updated',() => {

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest('service-desk/api/reports/1',{

			status: 200,

			response : {
					
				success:true,
					
				data : {
					
					report_filter : { id : 1, name : 'test', description : 'desc', filter_meta : [{key:'asset_ids', value_meta:[{id:1,name:'name1'},{id:2,name:'name2'}]}] }
				}	
			}
		})

		moxios.wait(() => {
	       expect(wrapper.vm.apiUrl).not.toEqual('/service-desk/api/asset-list?count=true&');
	       done()
	    })


	});


	
})