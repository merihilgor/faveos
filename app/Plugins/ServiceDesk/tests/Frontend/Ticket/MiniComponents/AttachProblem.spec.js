import { mount, createLocalVue } from '@vue/test-utils';

import AttachProblem from '../../../../views/js/components/Ticket/MiniComponents/AttachProblem.vue';

import Vue from 'vue';

import Vuex from 'vuex';

const localVue = createLocalVue();

localVue.use(Vuex);

window.eventHub = new Vue();

describe('AttachProblem',()=>{

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

		wrapper = mount(AttachProblem,{

			propsData : { data: {},actions : { show_attach_new_problem : true, show_attach_existing_problem : true}},

			stubs : ['problem-modal'],

			mocks : {
				lang:(string)=> string
			},

			store
		}) 
	};

	beforeEach(()=>{

		updateWrapper()

	});

	it('Is vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	
	});

	it('Calls `showModalMethod` when click on `attach-problem` button',()=>{

		updateWrapper();

		wrapper.vm.showModalMethod = jest.fn();

		wrapper.setData({ lists : [{id:1,name:'attach-new'},{id:2,name:'attach-exists'}]});

		wrapper.find('.cursor').trigger('click');

		expect(wrapper.vm.showModalMethod).toHaveBeenCalled();

	});

	it('Change `showModal` false to true when `showModalMethod` called',()=>{

		updateWrapper();

		expect(wrapper.vm.showModal).toBe(false);

		wrapper.vm.showModalMethod();

		expect(wrapper.vm.showModal).toBe(true);

	});

	it('Change `showModal` value as false when `onClose` method is called',()=>{

		updateWrapper();

		expect(wrapper.vm.showModal).toBe(false);

		wrapper.vm.showModalMethod();

		expect(wrapper.vm.showModal).toBe(true);

		wrapper.vm.onClose();

		expect(wrapper.vm.showModal).toBe(false);

	})

});