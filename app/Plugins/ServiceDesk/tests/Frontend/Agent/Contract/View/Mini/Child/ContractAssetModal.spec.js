import { mount, createLocalVue } from '@vue/test-utils';

import ContractAssetModal from '../../../../../../../views/js/components/Agent/Contract/View/Mini/Child/ContractAssetModal.vue';

import Vue from 'vue';

import Vuex from 'vuex';

import moxios from 'moxios';

import sinon from 'sinon'

window.eventHub = new Vue();

window.scrollTo = () => { };

window.alert = jest.fn();

jest.mock('helpers/responseHandler')

jest.mock('helpers/extraLogics')

describe('ContractAssetModal',()=>{

	let wrapper;

	const updateWrapper = ()=>{

		wrapper = mount(ContractAssetModal,{

			 propsData : { contractId : 1 },

			 mocks : { trans:(string)=> string },

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

	it('updates `loading` value correctly when `onSubmit` method is called',(done) => {

		updateWrapper()

		wrapper.setProps( { onClose : jest.fn() } );

		expect(wrapper.vm.loading).toBe(false)

		wrapper.setData({ assetIds : [1,2,3,4]})

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();
		
		setTimeout(()=>{
			
			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.onClose).toHaveBeenCalled();

				done();
		},1);
	});

	it('calls `alert` when `onSubmit` method is called and assetIds is empty',() => {

		updateWrapper()

		wrapper.setProps( { onClose : jest.fn() } );

		expect(wrapper.vm.loading).toBe(false)

		wrapper.setData({ assetIds : []})

		wrapper.vm.onSubmit()
		
	 expect(window.alert).toBeCalled()
	});

	it('updates `asset_type_ids` value when onChange method is called with suitable parameters for asset_type name',()=>{
			
		wrapper.vm.onChange(10,'asset_type_ids');
			
		expect(wrapper.vm.asset_type_ids).toEqual(10);
		
	});

	it("updates `assetIds` value when assetData method called",()=>{

		expect(wrapper.vm.assetIds).toEqual([]);

		wrapper.vm.assetsData([1,2,3]);
		
		expect(wrapper.vm.assetIds).toEqual([1,2,3]);
	})


	it("`loading` value should be false if api returns errorresponse",(done)=>{
		
		updateWrapper();

		wrapper.setProps({ showModal : true })

		wrapper.setData({ assetIds : [1,2,3] })

		mockSubmitFailRequest();

		wrapper.vm.onSubmit()

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-attach-asset?contract_id=1')
				
			expect(wrapper.vm.loading).toEqual(false)

			done();
			
		},1)
	})

	it('updates `apiUrl` value when `onApply` method called',()=>{

		expect(wrapper.vm.$data.apiUrl).toBe('');

		wrapper.setData({ selectedFilters : { 'asset_type_ids' : { id :1, name :'asset'}}})

		wrapper.vm.onApply();

		expect(wrapper.vm.$data.apiUrl).toBe('/service-desk/api/asset-list?contract_id=1&asset_type_ids=1');
	})

	function mockSubmitRequest(){
	 
		moxios.uninstall();
	 
		moxios.install();
	 
		moxios.stubRequest('/service-desk/api/contract-attach-asset?contract_id=1',{
	 
			status: 200,
		 
			response: {'success':true,'message':'successfully saved'}
		 
		})
	}

	function mockSubmitFailRequest(){
	 
		moxios.uninstall();
	 
		moxios.install();
	 
		moxios.stubRequest('/service-desk/api/contract-attach-asset?contract_id=1',{
	 
			status: 400,
	 
			response: {'success':false,'message':'failed'}
	 
		})
	}
})