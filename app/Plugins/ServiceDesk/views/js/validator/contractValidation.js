import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateContractSettings(data) {

  const { name, status, contractDescription, contractType, cost, vendor, user, contractDate, licenseCount, notifyBefore } = data

  let validatingData = {

    name: [name, 'isRequired',{'max(50)' :'The name should be less than 50 characters.'}],

    contractType: [contractType, 'isRequired'],

    contractDescription: [contractDescription, 'isRequired'],

    status: [status, 'isRequired'],

    cost: [cost, 'isRequired','minValue(1)'],

    contractDate: [contractDate, 'isRequired'],

    licenseCount: [licenseCount,'minValue(1)'],

    notifyBefore: [notifyBefore,'minValue(1)'],
  };

  let isValid, errors;

  isValid = validateUsersAndVendors(user, vendor)

  const validator = new Validator(lang);

  const validatorData = validator.validate(validatingData);

  errors = validatorData.errors;
  
  isValid = !isValid ? isValid : validatorData.isValid;

  // write to vuex if errors
  store.dispatch('setValidationError', errors);

  return {errors, isValid};
} 

/**
 * Validates user and vendor fields. Either one of the field must be present
 * @return {undefined}
 */
function validateUsersAndVendors(user, vendor){
  
  let missingScenarios = 0;

  //either of the fields has to be selected
  if(!user && !vendor){
      let message = lang('atleast_one_user_or_one_vendor_is_required_to_create_contract');
       missingScenarios++;
      //dispatch an action saying that one of the above field is required
      store.dispatch('setAlert', { type: 'danger', message: message, component_name: 'contract-create-edit' })
  }

  if(missingScenarios > 0){
    return false;
  }

  return true;
}