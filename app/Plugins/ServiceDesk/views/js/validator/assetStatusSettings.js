import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateAssetStatusSettings(data) {

  const { name, description } = data

  var validatingData = {

   	name: [name, 'isRequired',{'max(50)' :'The name should be less than 50 characters.'}],

    	description: [description, 'isRequired'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };
}
