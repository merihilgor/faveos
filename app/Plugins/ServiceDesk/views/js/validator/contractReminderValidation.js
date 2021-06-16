import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateContractReminderSettings(data) {

  const { notify_to, notifyBefore  } = data

  var validatingData = {

    notify_to: [notify_to, 'isRequired'],

    notifyBefore: [notifyBefore, 'isRequired']
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
} 