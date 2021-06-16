import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ChangeDetails from '../../../../../views/js/components/Agent/Changes/View/ChangeDetails.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ChangeDetails',() => {

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

	const updateWrapper = () =>{

		wrapper = mount(ChangeDetails,{

			stubs: ['change-actions','change-description','change-assets'],
			
			mocks:{
			
				lang:(string)=>string
			},

			methods : {

				basePath : jest.fn()
			},

			propsData : { change : { 
					id :1, 
					subject : 'subject', 
					description : 'description', 
					created_at : '2019-07-31', 
					status : { id : 1, name : 'status'},
					requester : { id : 1, name : 'req'}, 
					priority : { id : 1, name : 'priority'},
					change_type : { id : 1, name : 'type'},
					impact_type : { id : 1, name : 'impact_type'},
					location_relation : { id : 1, name : 'location'},
					department : { id : 1, name : 'department'},
					team : { id : 1, name : 'team'},
					attachment : { type : 'jpeg', value :'fghjkl', size : 1024}
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

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy();
	});

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

	it('formats image size to bytes when `formatBytes` method called',()=>{

		expect(wrapper.vm.formatBytes(1024)).toEqual('1 KB')
	})

	it("updates `icon` value when `attachment` type value changes",()=>{

		expect(wrapper.vm.icon).toEqual('fa fa-file-picture-o');

		wrapper.setProps({ change :{ id :1, 
					subject : 'subject', 
					description : 'description', 
					created_at : '2019-07-31', 
					status : { id : 1, name : 'status'},
					requester : { id : 1, name : 'req'}, 
					priority : { id : 1, name : 'priority'},
					change_type : { id : 1, name : 'type'},
					impact_type : { id : 1, name : 'impact_type'},
					location_relation : { id : 1, name : 'location'},
					department : { id : 1, name : 'department'},
					team : { id : 1, name : 'team'},
					attachment : { type : 'txt', value :'fghjkl', size : 1024}}});

		expect(wrapper.vm.icon).toEqual('fa fa-file-text-o');

	})
})