import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ReleaseDetails from '../../../../../views/js/components/Agent/Release/View/ReleaseDetails.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

let checkValue = jest.fn();

Vue.filter('checkValue', checkValue)

describe('ReleaseDetails',() => {

	let wrapper;

	let store;

	let getters;

	let actions;

	getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}

	actions = { unsetValidationError: jest.fn() }

	store = new Vuex.Store({ getters,actions })

	const tooltip = jest.fn();

	const updateWrapper = () =>{

		wrapper = mount(ReleaseDetails,{

			stubs: ['release-actions','release-description'],
			
			mocks:{
			
				lang:(string)=>string
			},

			directives: { tooltip },

			methods : {

				basePath : jest.fn()
			},

			propsData : { release : { 
					id :1, 
					identifier:'007',
					subject : 'subject', 
					description : 'description', 
					created_at : '2019-07-31', 
					updated_at : '2019-07-31', 
					planned_start_date : '2019-07-31', 
					planned_end_date : '2019-07-31', 
					status : { id : 1, name : 'status'},
					priority : { id : 1, name : 'priority'},
					release_type : { id : 1, name : 'type'},
					location : { id : 1, name : 'location'},
					asset_ids: [],
					attach_changes:[],
					attach_assets:[],
					attachment : [{ type : 'jpeg', value :'fghjkl', size : 1024}]
				}
			},

			attachToDocument: true,

			localVue, store
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showDescription).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})
})