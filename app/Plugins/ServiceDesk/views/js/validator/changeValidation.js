import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateChangeSettings(data) {
  
  const { subject, requester, status, changeType, description } = data

  var validatingData = {
    
    subject: [subject, 'isRequired'],
    
    requester: [requester, 'isRequired'],
    
    status: [status, 'isRequired'],
    
    changeType: [changeType, 'isRequired'],
    
    description: [description, 'isRequired'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}