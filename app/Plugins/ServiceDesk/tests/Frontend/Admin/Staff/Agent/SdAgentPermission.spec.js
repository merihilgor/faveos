import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios';
import Vue from 'vue';
import SdAgentPermission from '../../../../../views/js/components/Admin/Staff/Agent/SdAgentPermission'
jest.mock('helpers/responseHandler')
let wrapper;
window.eventHub = new Vue();

const fakeResponseData = {
  "success": true,
  "permissions": [{id: "perm", nmae: "perm"}]
}

//faking the success response
function getPermissionsMock() {
  moxios.stubRequest('service-desk/get/permissions', {
    status: 200,
    response: fakeResponseData
  });
}

const __data = '[{"id": "create_ticket", "name": "Create ticket"}]';

const mountWrapper = () => {
  wrapper = shallowMount(SdAgentPermission,{
    propsData:{
      data: __data
      },
    mocks: {
      lang: (string) => string
    },
    stubs:["checkbox"],
  });
}

describe('SdAgentPermission',()=> {  
  beforeEach(() => {
    moxios.install()
    mountWrapper();
  });

  afterEach(()=>{
    moxios.uninstall();
  })

  it('default value for `permissionsListSD` should be an empty array', () => {
    expect(wrapper.vm.$data.permissionsListSD.length).toBe(0)
  });

  it(' `permissionsListSD` should be assigned to response data', (done) => {
    wrapper.vm.getpermissonListApi();
    getPermissionsMock()
    setTimeout(() => {
      expect(wrapper.vm.$data.permissionsListSD.length).not.toBe(0)
      done()
    }, 1);
  })

  it(' each `permissionsListSD` element should be contains checked property', (done) => {
    wrapper.vm.getpermissonListApi();
    getPermissionsMock()
    setTimeout(() => {
      expect(wrapper.vm.permissionsListSD[0].checked).toBe(false)
      done();
    }, 1);
  })

  it('assign `value` passed in checkBoxOperationsSD function to checked property',() => {
    wrapper.vm.permissionsListSD = [{id: 'create_ticket', name: 'create ticket', checked: false}]
    let value = true;
    let selectedId = "create_ticket";
    wrapper.vm.checkBoxOperationsSD(value, selectedId)
    expect(wrapper.vm.permissionsListSD[0].checked).toBe(true);
  })

})