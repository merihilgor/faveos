import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import AssetFilter from '../../../views/js/components/Asset/AssetFilter.vue';

import sinon from 'sinon'

import Vue from 'vue'

let wrapper;

describe('AssetFilter', () => {

	beforeEach(()=>{

		wrapper = mount(AssetFilter,{
			
			stubs:['dynamic-select','loader'],

			propsData : {

				metaData : [{ section : [{value : ''}]},{ section : [{value : ''}]},{ section : [{value : ''}]},{ section : [{value : ''},{value : ''}]}]
			},
		   
		  mocks:{ lang: (string) => string }
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('updates `asset_ids` value of the filter when onChange method is called',()=>{

    wrapper.vm.onChange([{id :1, name :'asset'}], 'asset_ids');

    expect(wrapper.vm.selectedFilters['asset_ids']).toEqual([{id :1, name :'asset'}]);
  });

  it("emits an `closeEvent` when `onCancel` method called", () => {
	 
	  wrapper.vm.onCancel()

	  expect(wrapper.emitted()).toEqual({"selectedFilters": [["closeEvent"]]})
	});

	it("emits an `resetEvent` when `onReset` method called", () => {
	 
	  wrapper.vm.onReset()

	  expect(wrapper.emitted()).toEqual({"selectedFilters": [["resetEvent"]]})
	});

	it("emits an event with `asset_ids`", () => {
	 	
	 	wrapper.vm.onChange([{id :1, name :'asset'}], 'asset_ids');

	  wrapper.vm.onApply()

	  expect(wrapper.emitted()).toEqual({"selectedFilters": [[{"asset_ids": [{"id": 1, "name": "asset"}], "assigned_on": ""}]]})
	})
})
