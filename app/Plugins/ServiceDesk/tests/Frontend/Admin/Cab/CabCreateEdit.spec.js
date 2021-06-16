import { shallowMount } from '@vue/test-utils';

import CabCreateEdit from '../../../../views/js/components/Admin/Cab/CabCreateEdit';

import moxios from 'moxios';

import Vue from 'vue'

window.scrollTo = () => { };

window.eventHub = new Vue();

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/cabSettingRules";

jest.mock('helpers/responseHandler')

describe('CabCreateEdit',()=>{


  let wrapper;

  beforeEach(()=>{

      moxios.install();

      moxios.stubRequest('/service-desk/api/cab/1',{
        status: 200,
        response: fakeResponse
      })

      wrapper = shallowMount(CabCreateEdit, {
        mocks:{
          lang: (string) => string
        }
      });

      //mocking scroll functional as it doesn't have any logics
      wrapper.vm.scrollToSubmitButton = jest.fn()
  })

  afterEach(()=>{
    moxios.uninstall();
  })

  it('shows loader in edit mode initially when API call is made',(done)=>{
      wrapper.vm.getInitialValues(1);
      expect(wrapper.find('custom-loader-stub').exists()).toBe(true);
      done();
  })

  it('does not show body if `hasDataPopulated` is false',(done)=>{
      wrapper.setData({hasDataPopulated: false});
      wrapper.vm.getInitialValues(1);
      expect(wrapper.find('.box-container').exists()).toBe(false);
      done();
  })

  it('populates component data as soon as API response is a success in edit mode',(done) => {
      wrapper.vm.getInitialValues(1);
      setTimeout(()=>{
        expect(wrapper.vm.id).toEqual(fakeResponse.data.id)
        expect(wrapper.vm.name).toEqual(fakeResponse.data.name)
        expect(wrapper.vm.levels).toEqual(fakeResponse.data.levels)
        done();
      },1)
  })

  it('updates `name` of the cab when onChange method is called with suitable parameters for cab name',()=>{
    wrapper.vm.onChange('test_name', 'name');
    expect(wrapper.vm.name).toBe('test_name');
  })

  it('updates `name` of the cab-level when onChange method is called with suitable parameters for cab-level`s name',()=>{
    wrapper.vm.onChange('test_level_name', 'level-name-0');
    expect(wrapper.vm.levels[0].name).toBe('test_level_name');
  })

  it('updates `users` of the cab-level when onChange method is called with suitable parameters for cab-level`s users',()=>{
    wrapper.vm.onChange('test_level_agent', 'cab-agent-0');
    expect(wrapper.vm.levels[0].approvers.users).toBe('test_level_agent');
  })

  it('updates `user_types` of the cab-level when onChange method is called with suitable parameters for cab-level`s user_types',()=>{
    wrapper.vm.onChange('test_level_type', 'cab-user-type-0');
    expect(wrapper.vm.levels[0].approvers.user_types).toBe('test_level_type');
  })

  it('updates `match` of the cab-level when onChange method is called with suitable parameters for cab-level`s match',()=>{
    wrapper.vm.onChange('test_match', 'level-match-0');
    expect(wrapper.vm.levels[0].match).toBe('test_match');
  })

  it('pushes another level to `levels` when `add level` button is pressed',() => {
    wrapper.setData({hasDataPopulated: true});
    expect(wrapper.vm.levels.length).toBe(1);
    wrapper.find('#add-level').trigger('click');
    expect(wrapper.vm.levels.length).toBe(2);
    expect(wrapper.vm.levels[1]).toEqual(
      {id:0, name:'', "order": 2, match:'all',approvers:{ users:[], user_types : []}}
    )
  })

  it('makes API request with expected parameters when submit-cab button is pressed', (done) => {
    wrapper.vm.getInitialValues(1);
    setTimeout(()=>{
      wrapper.find('#submit-cab').trigger('click');
      setTimeout(() => {
        let requestData = JSON.parse(moxios.requests.mostRecent().config.data);
        expect(requestData.levels[0].approvers.users).toEqual(["test_user_id"]);
        done();
      }, 1)
    }, 1)
  })

  it('shows loader when submit button is clicked',(done)=>{
      wrapper.vm.getInitialValues(1);
      setTimeout(()=>{
        wrapper.find('#submit-cab').trigger('click');
        setTimeout(() => {
          expect(wrapper.find('custom-loader-stub').exists()).toBe(true);
          done();
        }, 1)
      }, 1)
  })

  it('sends delete API request when deletingLevelId is non_zero', (done) => {
    wrapper.vm.getInitialValues(1);
    setTimeout(()=>{
      wrapper.setData({deletingLevelId: 'test_level_id'});
      wrapper.vm.onDelete();
      setTimeout(()=>{
          let request = moxios.requests.mostRecent().config;
          expect(request.url).toBe("/service-desk/api/cab/test_level_id/level");
          expect(request.method).toBe("delete");
          done();
      }, 1)
    }, 1)
  })

  it('does not send delete API request if deletingLevelId is 0 (which is the case when level is deleted before getting saved)', (done) => {
    wrapper.vm.getInitialValues(1);
    setTimeout(()=>{
      wrapper.setData({deletingLevelId: 0});
      wrapper.vm.onDelete();
      setTimeout(()=>{
          let request = moxios.requests.mostRecent().config;
          expect(request.method).not.toBe("delete");
          done();
      }, 1)
    }, 1)
  })

  it('deletes the correct index when `delete method is called`', () => {
    wrapper.setData({levels : [
      {id:0, name:'index_0', match:'all',approvers:{ users:[],user_types : []}},
      {id:0, name:'index_1', match:'all',approvers:{ users:[],user_types : []}},
      {id:0, name:'index_2', match:'all',approvers:{ users:[],user_types : []}},
      {id:0, name:'index_3', match:'all',approvers:{ users:[],user_types : []}},
    ]})

    wrapper.setData({deletingLevelIndex: 2});

    //deleting the one with index 2
    wrapper.vm.onDelete();

    expect(wrapper.vm.levels[0].name).toBe('index_0');
    expect(wrapper.vm.levels[1].name).toBe('index_1');
    expect(wrapper.vm.levels[2].name).toBe('index_3');
  })

  it('isValid - should return false ', done => {
        
    validation.validateCabSettings = () =>{return {errors : [], isValid : false}}
      
    expect(wrapper.vm.isValid()).toBe(false)
      
    done()
  })

  it('isValid - should return true ', done => {
       
    validation.validateCabSettings = () =>{return {errors : [], isValid : true}}
      
    expect(wrapper.vm.isValid()).toBe(true)
      
    done()
  })

  it('updates values on deleting method',()=>{

    expect(wrapper.vm.deletingLevelIndex).toEqual(null);
    
    expect(wrapper.vm.deletingLevelId).toEqual(null);

    wrapper.vm.deleting(1,10);

    expect(wrapper.vm.deletingLevelIndex).toEqual(1);
    
    expect(wrapper.vm.deletingLevelId).toEqual(10);

    expect(wrapper.vm.showDeletePopUp).toEqual(true);
  })

  let fakeResponse = {
      success: true,
      data: {
          id: 1,
          name: "cab_name",
          user_id: 173,
          action_on_deny:{id:1, name:'test_status'},
          action_on_approve:{id:1, name:'test_status'},
          levels : [{
              id: 'test_level_id',
              name: "Level 1",
              match: "any",
              approvers: {
                  users: [{
                      id: 'test_user_id',
                      name: "Erik Douglas Spencer Cummings IV"
                  }],
                  user_types: [{
                      id: 'test_user_type_id',
                      name: "Rashawn Glover"
                  }]
              }
          }]
       }
    }
})
