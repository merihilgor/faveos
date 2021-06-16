import AssociatedProblem from './../../../views/js/components/Problem/AssociatedProblem.vue';
import {mount} from '@vue/test-utils';

let wrapper;

let associatedProblemObject = {'name':'test_name', "redirect_to" : 'test_link', 'description':'test_description'}

describe('AssociatedProblem',() => {
  wrapper = mount(AssociatedProblem, {
    mocks : { lang:(string) => string },
    propsData : {
      data : JSON.stringify({"associated_problem": associatedProblemObject})
    }
  })

  it('hides associated problem when `associated_problem` is null',() => {
    wrapper.setProps({data:JSON.stringify({"associated_problem": null})})
    expect(wrapper.find('#associated-problem').exists()).toBe(false);
  })

  it('Shows associated problem when `associated_problem` is not null',() => {
    wrapper.setProps({data:JSON.stringify({"associated_problem": associatedProblemObject})})
    expect(wrapper.find('#associated-problem').exists()).toBe(true);
  })

  it('redirects to `associatedProblem.redirect_to` when clicked on the value',() => {
    wrapper.setProps({data:JSON.stringify({"associated_problem": associatedProblemObject})})
    let associatedProblemLink = wrapper.find('#associated-problem-link');
    expect(associatedProblemLink.exists()).toBe(true);
    window.location.assign = jest.fn()
    expect(associatedProblemLink.attributes().href).toBe('test_link')
  })

  it('displays `associated_problem.name` when `associated_problem` is not null',() => {
    wrapper.setProps({data:JSON.stringify({"associated_problem": associatedProblemObject})})
    expect(wrapper.text()).toContain('associated_problem');
    expect(wrapper.text()).toContain('test_name');
  })

})
