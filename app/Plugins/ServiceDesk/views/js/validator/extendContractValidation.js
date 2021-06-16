import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateExtendContractSettings(data) {

  const { contract_end_date, approver, cost,  } = data

  var validatingData = {

    contract_end_date: [contract_end_date, 'isRequired'],

    approver: [approver, 'isRequired'],

    cost: [cost, 'isRequired']
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
} 