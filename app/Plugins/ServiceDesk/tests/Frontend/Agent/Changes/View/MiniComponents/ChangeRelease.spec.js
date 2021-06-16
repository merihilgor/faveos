import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import ChangeRelease from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ChangeRelease.vue';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ChangeRelease',() => {

	let wrapper;

	let store;

	let getters;

	getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}

	store = new Vuex.Store({ getters })

	const updateWrapper = () =>{

		wrapper = mount(ChangeRelease,{

			mocks:{ lang:(string)=>string },

			methods : { basePath : jest.fn() },

			propsData : { release : [{ 
					id :1, 
					planed_start_date : '2019-07-31', 
					planed_end_date :  '2019-07-31', 
					created_at : '2019-07-31', 
					status : { id : 1, name : 'status'},
					subject : 'subject', 
					priority : { id : 1, name : 'priority'},
					location_relation : { id : 1, name : 'location'},
					release_type : { id : 1, name : 'type'},
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

		expect(wrapper.props().release).toEqual([{"created_at": "2019-07-31", "id": 1, "location_relation": {"id": 1, "name": "location"}, "planed_end_date": "2019-07-31", "planed_start_date": "2019-07-31", "priority": {"id": 1, "name": "priority"}, "release_type": {"id": 1, "name": "type"}, "status": {"id": 1, "name": "status"}, "subject": "subject"}])

		wrapper.setProps({ release : []})

		expect(wrapper.props().release).toEqual([])

	})
})