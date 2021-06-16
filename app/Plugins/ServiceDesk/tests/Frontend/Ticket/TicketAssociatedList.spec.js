import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import TicketAssociatedList from '../../../views/js/components/Ticket/TicketAssociatedList.vue';

import Vue from 'vue'

window.eventHub = new Vue();

let wrapper;

describe('TicketAssociatedList', () => {

	wrapper = mount(TicketAssociatedList,{
		
		stubs:['associated-asset-list','associated-contracts','associated-changes','loader'],
		
		mocks:{
			lang: (string) => string,
		},

		propsData:{
			
			data : JSON.stringify({ 'id' : 1 })
		}
	})

	it('is a vue instance', () => {
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("updates `category` when `associates` method called",()=>{

		wrapper.vm.associates('assets');
		
		expect(wrapper.vm.category).toEqual('assets')
	});

	it("updates `category` and `loading` value when `associateMethod` method called",(done)=>{

		wrapper.vm.associateMethod('assets');
		
		expect(wrapper.vm.category).toEqual('assets');

		expect(wrapper.vm.loading).toEqual(true);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toEqual(false);

			done();
		},3000)
	})
})