/**
 * This Files contain validation specific to LdapSettings.vue only
 */
import {store} from 'store'
import { Validator } from 'easy-validator-js';
import { lang } from 'helpers/extraLogics';

/**
 * @param {object} data      ldapsettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */

export function validateProblemSettings(data) {
  // console.log(data.search_bases.length, "inside ldap rule setting")
  const { subject, identifier, from, department_id, impact_id, status_type_id, priority_id, description } = data



  //rules has to apply only after checking conditions
  var validatingData = {
    subject: [subject, 'isRequired',{'max(50)' :'The subject should be less than 50 characters.'}],
    identifier: [identifier,{'max(45)' :'The identifier should be less than 45 characters.'}],
    from: [from, 'isRequired'],
    department_id: [department_id, 'isRequired'],
    impact_id: [impact_id, 'isRequired'],
    status_type_id: [status_type_id, 'isRequired'],
    priority_id: [priority_id, 'isRequired'],
    description: [description, 'isRequired'],
  };

  //creating a validator instance and pasing lang method to it
  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  // write to vuex if errors
  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };
}
