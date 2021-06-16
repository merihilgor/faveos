import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ProblemPlanning from '../../../../../../views/js/components/Problem/View/MiniComponents/Associates/ProblemPlanning.vue';

import moxios from 'moxios';

window.eventHub = new Vue();

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ProblemPlanning',() => {

	let wrapper;

	let actions
  
  let store

  let getters;

	getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}

  actions = { unsetValidationError: jest.fn() }

 	store = new Vuex.Store({ actions, getters })

	const updateWrapper = () =>{

		wrapper = mount(ProblemPlanning,{

			stubs: ['problem-update-modal','problem-update-delete-modal','loader'],
			
			mocks:{ lang:(string)=>string },

			methods : {basePath : jest.fn() },

			propsData : { problemId : 1 },

			store,

			attachToDocument: true,
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


	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getDetails();
		
		expect(wrapper.vm.$data.loading).toBe(true)

		mockPlanningRequest(200);

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-planning/1')
			
			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getDetails();

		mockPlanningRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	});

	it("returns correct icon class when `getIcon` method called",()=>{

		updateWrapper();

		expect(wrapper.vm.getIcon({ type : 'jpg'})).toEqual('fa fa-file-picture-o');

		expect(wrapper.vm.getIcon({ type : 'txt'})).toEqual('fa fa-file-text-o');

		expect(wrapper.vm.getIcon({ type : 'xls'})).toEqual('fa fa-file-excel-o');
		
		expect(wrapper.vm.getIcon({ type : 'pdf'})).toEqual('fa fa-file-pdf-o');
		
		expect(wrapper.vm.getIcon({ type : 'vue'})).toEqual('fa fa-file');
	})

	it('formats image size to bytes when `formatBytes` method called',()=>{

		expect(wrapper.vm.formatBytes(1024)).toEqual('1 KB')
	})

	it('updates `showChangeUpdate, identifier` values when `updateMethod` method called',()=>{

		wrapper.vm.updateMethod('root-cause');

		expect(wrapper.vm.showProblemUpdate).toEqual(true)

		expect(wrapper.vm.identifier).toEqual('root-cause')
	})

	it('updates `showUpdateDelete, identifier` values when `deleteMethod` method called',()=>{

		wrapper.vm.deleteMethod('root-cause');

		expect(wrapper.vm.showUpdateDelete).toEqual(true)

		expect(wrapper.vm.identifier).toEqual('root-cause')
	})

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showUpdateDelete).toBe(false);
		
		expect(wrapper.vm.showProblemUpdate).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})
	
	function mockPlanningRequest(status = 200 , url = '/service-desk/api/problem-planning/1'){

		moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {
	    	data : {
	    		planning_popups : [{
	    			attachment: null,
						created_at: "2019-11-23 09:58:29",
						description: "<p>test</p>↵",
						id: 7,
						key: "root-cause",
						owner: "sd_problem:2",
						updated_at: "2019-11-23 09:58:29",
	    		}]
	    	}
	    }
	  })
	}
})