import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ReleasesFilter from '../../../../views/js/components/Agent/Release/ReleasesFilter.vue';

import sinon from 'sinon'

import Vue from 'vue'

let wrapper;

describe('ReleasesFilter', () => {

	beforeEach(()=>{

		wrapper = mount(ReleasesFilter,{
			
			stubs:['dynamic-select','loader','date-time-field'],

			propsData : {

				metaData : [{ section : [{value : ''}]},{ section : [{value : ''}]},{ section : [{value : ''}]},{ section : [{value : ''},{value : ''}]}]
			},
		   
		  mocks:{ lang: (string) => string }
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('updates `release_ids` value of the filter when onChange method is called',()=>{

    wrapper.vm.onChange([{id :1, name :'release'}], 'release_ids');

    expect(wrapper.vm.selectedFilters['release_ids']).toEqual([{id :1, name :'release'}]);
  });

  it("emits an `closeEvent` when `onCancel` method called", () => {
	 
	  wrapper.vm.onCancel()

	  expect(wrapper.emitted()).toEqual({"selectedFilters": [["closeEvent"]]})
	});

	it("emits an `resetEvent` when `onReset` method called", (done) => {
	 
	  wrapper.vm.onReset()
	  
	  expect(wrapper.vm.loading).toEqual(true)

	  setTimeout(()=>{

	  	expect(wrapper.vm.loading).toEqual(false)
	  	
	  	done();
	  },1001)

	  expect(wrapper.emitted()).toEqual({"selectedFilters": [["resetEvent"]]})
	});

	it("emits an event with `release_ids`", () => {
	 	
	 	wrapper.vm.onChange([{id :1, name :'release'}], 'release_ids');

	  wrapper.vm.onApply()

	  expect(wrapper.emitted()).toEqual({"selectedFilters": [[{"release_ids": [{"id": 1, "name": "release"}]}]]})
	})
})