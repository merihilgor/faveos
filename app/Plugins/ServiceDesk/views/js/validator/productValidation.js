import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateProductSettings(data) {

  const { name, manufacturer, product_status, product_proc_mode, department, description, assetType } = data

  var validatingData = {

    name: [name, 'isRequired'],

    manufacturer: [manufacturer, 'isRequired'],

    product_proc_mode: [product_proc_mode, 'isRequired'],

    product_status: [product_status, 'isRequired'],

    department: [department, 'isRequired'],
    
    description: [description, 'isRequired']

  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors);

  return { errors, isValid };
}
