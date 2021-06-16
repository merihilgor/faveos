import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateAnnouncementSettings(data) {

  const { to, subject, organization, department } = data

  var validatingData = {

    subject : [ subject, 'isRequired' ], 
  };

  if(to === 'department'){
    
    validatingData['department'] = [ department, 'isRequired' ]
  }

  if(to === 'organization'){
    
    validatingData['organization'] = [ organization, 'isRequired' ]
  }

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };
}
