import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import Chart from '../../../../views/js/components/Report/Charts/Chart.vue';

import sinon from 'sinon'

import Vue from 'vue'

let wrapper;

describe('Chart', () => {

	beforeEach(()=>{

		wrapper = mount(Chart,{
			
			stubs:['bar-chart','pie-chart','horizontal-chart','doughnut-chart'],
		   
		  mocks:{ lang: (string) => string },
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("updates `colors` array",()=>{

		wrapper.vm.getRandomColor();

		expect(wrapper.vm.colors).not.toEqual([]);

	})
})