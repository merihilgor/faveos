import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateVendorSettings(data) {

  const { name, email, description, primary_contact, address } = data

  var validatingData = {

    name: [name, 'isRequired'],

    email: [email, 'isRequired', 'isEmail'],

    description: [description, 'isRequired'],
    
    primary_contact: [primary_contact, 'isRequired'],
    
    address: [address, 'isRequired']
  };
  
  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);
  
  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
