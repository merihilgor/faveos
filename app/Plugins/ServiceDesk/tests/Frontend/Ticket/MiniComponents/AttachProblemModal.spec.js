import { mount } from '@vue/test-utils';

import AttachProblemModal from '../../../../views/js/components/Ticket/MiniComponents/AttachProblemModal.vue';

import Vue from 'vue';

import Vuex from 'vuex';

import moxios from 'moxios';

window.eventHub = new Vue();

describe('AttachProblemModal',()=>{

	let wrapper;

	const updateWrapper = ()=>{

		wrapper = mount(AttachProblemModal,{
			 propsData : { data : { title : 'title'}},

			 mocks : {
			 	lang:(string)=> string
			 },

			 stubs : ['modal','alert','loader','problem','dynamic-select']
		})
	};

	beforeEach(()=>{
		updateWrapper();
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

	it('updates `problem` value when onChange method is called with suitable parameters for problem name',()=>{
    	
    	wrapper.vm.onChange({"id":1,"name":"problem"}, "problem");
    	
    	expect(wrapper.vm.problem).toEqual({"id":1,"name":'problem'});
  	
  	});

  	it('calls `onSubmit` when clicks on Save button',()=>{
    
    	updateWrapper()

    	wrapper.vm.onSubmit =jest.fn()

    	wrapper.setProps({ showModal : true })

    	wrapper.find('#submit_btn').trigger('click')

    	expect(wrapper.vm.onSubmit).toHaveBeenCalled()

  	});

})