import { mount ,createLocalVue} from '@vue/test-utils'

import SdeskDataTableActions from '../../../views/js/components/MiniComponents/SdeskDataTableActions'

import Vuex from 'vuex'

import sinon from 'sinon'

let wrapper;

const localVue = createLocalVue()

localVue.use(Vuex)

describe('SdeskDataTableActions',()=>{

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

	const populateWrapper = ()=>{
	   	wrapper = mount(SdeskDataTableActions,{
		propsData : {
			data : {detach:true, is_default : true}
		},
		mocks : { lang:(string)=> string },
		stubs:['detach-modal'],
			store,
	        localVue
		});
	}
	

	it('Makes detach button enabled if `data` has `detach` as `true`',()=>{
		
		populateWrapper()

		expect(wrapper.find('#detach-button').exists()).toBe(true)
		
	});

	it('Does not show `detach modal popup` if `showModal` is false',()=>{
		
		populateWrapper()

		wrapper.vm.$data.showModal = false
		
		expect(wrapper.find('detach-modal-stub').exists()).toBe(false)

	});

	it('Show `detach modal popup` if `showModal` is true',()=>{
		
		populateWrapper()

		wrapper.vm.$data.showModal = true
		
		expect(wrapper.find('detach-modal-stub').exists()).toBe(true)

	});

	it('Initial value of `showModal` should be false',()=>{
		
		populateWrapper()

		expect(wrapper.vm.$data.showModal).toBe(false)

	});

	it('`ShowModal` should be false when `onClose` method is called',()=>{
		

		populateWrapper();

		wrapper.setData({ showModal : true })

		expect(wrapper.vm.$data.showModal).toBe(true)

		wrapper.vm.onClose()
		
		expect(wrapper.vm.$data.showModal).toBe(false)

	});

	it("updates disabled property",()=>{
		
		populateWrapper()
		
		expect(wrapper.vm.disabled).toBe(true)

		wrapper.setProps({data:{is_default : false}})
		
		expect(wrapper.vm.disabled).toBe(false)
	})

});