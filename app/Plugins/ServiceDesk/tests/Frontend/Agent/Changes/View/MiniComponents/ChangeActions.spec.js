import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ChangeActions from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ChangeActions.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

window.eventHub = new Vue();

localVue.use(Vuex)

describe('ChangeActions',() => {

	let wrapper;

	let actions
  
  let store

  actions = { unsetValidationError: jest.fn() }

 	store = new Vuex.Store({ actions })

	const updateWrapper = () =>{

		wrapper = mount(ChangeActions,{

			stubs: ['delete-modal','release-add-modal','release-create-modal','change-release-detach','change-status-modal','change-update-modal'],
			
			mocks:{
			
				lang:(string)=>string
			},

			methods : {

				basePath : jest.fn()
			},

			propsData : {

				change : { id : 1}
			},

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
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getActions(1);
		
		stubRequest();

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/change-action/1')
			
			expect(wrapper.vm.actions).not.toEqual('')

			done();
		},50)
	})

	it("makes `actions` as empty if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getActions(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.actions).toEqual('')

			done();
		},50)
	})

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it('updates `showChangeUpdate, identifier` values when `updateMethod` method called',()=>{

		wrapper.vm.updateMethod('reason');

		expect(wrapper.vm.showChangeUpdate).toEqual(true)

		expect(wrapper.vm.identifier).toEqual('reason')
	})

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showDeleteModal).toBe(false);

		expect(wrapper.vm.showReleaseCreate).toBe(false);

		expect(wrapper.vm.showReleaseExists).toBe(false);

		expect(wrapper.vm.showReleaseDetach).toBe(false);
		
		expect(wrapper.vm.showChangeStatus).toBe(false);
		
		expect(wrapper.vm.showChangeUpdate).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})

	function stubRequest(status = 200,url = '/service-desk/api/change-action/1'){
	  
	  moxios.uninstall();
	  
	  moxios.install();
	  
	  moxios.stubRequest(url,{
	    
	    status: status,
	    
	    response : {
	    	data : {
	    		actions : {
	    			allowed_cab_action: true,
						allowed_enforce_cab: false,
						change_close: true,
						change_create: true,
						change_delete: true,
						change_edit: true,
						change_release_attach: true,
						change_release_detach: false,
						change_view: true,
						remove_cab: true,
						view_cab_progress: true,
	    		}
	    	}
	    }
	  })
	}
})