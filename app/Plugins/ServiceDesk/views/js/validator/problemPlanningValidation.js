import {store} from 'store'

import { Validator } from 'easy-validator-js';

import { lang } from 'helpers/extraLogics';

export function validateProblemPlanningSettings(data) {

  const { solution_title, description } = data

  var validatingData = {

    solution_title: [solution_title,{'max(50)' :'The title should be less than 50 characters.'}],
    
    description: [description,'isRequired'],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };
}
