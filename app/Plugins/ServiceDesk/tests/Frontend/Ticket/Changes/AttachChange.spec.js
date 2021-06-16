import { mount, createLocalVue } from '@vue/test-utils';

import AttachChange from '../../../../views/js/components/Ticket/Changes/AttachChange.vue';

import Vue from 'vue';

import Vuex from 'vuex';

const localVue = createLocalVue();

localVue.use(Vuex);

window.eventHub = new Vue();

describe('AttachChange',()=>{

	let wrapper;

	let actions

	let store

	beforeEach(()=>{
		
		actions = {
			
			unsetValidationError: jest.fn()
		}
		
		store = new Vuex.Store({
			
			actions
		})
	});

	const updateWrapper = ()=> {

		wrapper = mount(AttachChange,{

			propsData : { data: {},actions : { attach_change_initiated : true, attach_change_initiating : true}},

			stubs : ['change-attach-modal'],

			mocks : { lang:(string)=> string },

			store
		}) 
	};

	beforeEach(()=>{

		updateWrapper()
	});

	it('Is vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it('Change `showModal` false to true when `showModalMethod` called',()=>{

		updateWrapper();

		expect(wrapper.vm.showModal).toBe(false);

		wrapper.vm.showModalMethod('attach_initiating');

		expect(wrapper.vm.showModal).toBe(true);

		expect(wrapper.vm.title).toBe('link_to_an_existing_change');

		expect(wrapper.vm.type).toBe('initiating');

	});

	it('Change `showModal` value as false when `onClose` method is called',()=>{

		wrapper.vm.onClose();

		expect(wrapper.vm.showModal).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();

	})
});