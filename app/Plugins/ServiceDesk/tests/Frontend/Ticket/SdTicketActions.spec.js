import SdTicketActions from './../../../views/js/components/Ticket/SdTicketActions.vue';
import {shallowMount, createLocalVue} from '@vue/test-utils';
import Vue from "vue";
import Vuex from "vuex";
import moxios from 'moxios';

const localVue = createLocalVue()

localVue.use(Vuex)

window.eventHub = new Vue();

let wrapper;

function mountComponent(showAttachProblem = false, showDetachProblem = false, showAttachAssets = false){
  // mocking a getter
  let getters = {
    getTicketActions : () => {
      return {show_attach_new_problem : showAttachProblem,
              show_detach_problem : showDetachProblem,
              show_attach_assets : showAttachAssets}
    }
  }

  let store = new Vuex.Store({ getters });

  wrapper = shallowMount(SdTicketActions, {
    propsData : { data : JSON.stringify({})},
    stubs: ['attach-problem','detach-modal'],
    mocks : { lang:(string) => string }, localVue, store
  })
}

describe('SdTicketActions', ()=>{

  it('shows `attach-problem` component when `show_attach_problem` is true', () => {
    mountComponent(true);
    expect(wrapper.find('attach-problem-stub').exists()).toBe(true);
  })

  it('hides `attach-problem` component when `show_attach_problem` is false', () => {
    mountComponent(false);
    expect(wrapper.find('attach-problem-stub').exists()).toBe(false);
  })

  it('shows `detach-problem` when `show_detach_problem` is true', () => {
    mountComponent(false, true);
    expect(wrapper.find('#detach-problem').exists()).toBe(true);
  })

  it('hides `detach-problem` when `show_detach_problem` is false', () => {
    mountComponent(false, false);
    expect(wrapper.find('#detach-problem').exists()).toBe(false);
  })

  it('shows `attach-assets` when `show_attach_assets` is true', () => {
    mountComponent(false, false, true);
    expect(wrapper.find('#attach-assets').exists()).toBe(true);
  })

  it('hides `attach-assets` when `show_attach_assets` is false', () => {
    mountComponent(false, false, false);
    expect(wrapper.find('#attach-assets').exists()).toBe(false);
  })
})
