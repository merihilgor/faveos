import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateReleaseSettings(data) {

  const { subject, releaseType, status, priority, description } = data

  var validatingData = {

    subject: [subject, 'isRequired'],

    releaseType: [releaseType, 'isRequired'],

    status: [status, 'isRequired'],

    priority: [priority, 'isRequired'],
    
    description: [description, 'isRequired'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
