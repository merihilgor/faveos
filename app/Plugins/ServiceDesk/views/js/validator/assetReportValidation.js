import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateAssetReportSettings(data) {
  
  const { name } = data
  
  var validatingData = {
  
    name: [name, 'isRequired','max(25)'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };
}
