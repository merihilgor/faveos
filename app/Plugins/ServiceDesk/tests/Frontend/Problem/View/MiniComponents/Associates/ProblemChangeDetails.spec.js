import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import ProblemChangeDetails from '../../../../../../views/js/components/Problem/View/MiniComponents/Associates/ProblemChangeDetails.vue';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ProblemChangeDetails',() => {

	let wrapper;

	let store;

	let getters;

	getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}

	store = new Vuex.Store({ getters })

	const updateWrapper = () =>{

		wrapper = mount(ProblemChangeDetails,{

			mocks:{ lang:(string)=>string },

			methods : { basePath : jest.fn() },

			propsData : { change : [{ 
					id :1, 
					created_at : '2019-07-31',
					subject : 'subject', 
				}]
			},

			attachToDocument: true,

			localVue, store
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it('updtaes `props` when value changed',()=>{

		expect(wrapper.props().change).toEqual([{"created_at": "2019-07-31", "id": 1, subject:'subject'}])

		wrapper.setProps({ change : []})

		expect(wrapper.props().change).toEqual([])

	})
})