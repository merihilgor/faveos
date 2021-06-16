import { store } from "store";

import { Validator } from "easy-validator-js";

import { lang } from "helpers/extraLogics";

export function validateAssetsSettings(data) {
  const { name } = data;

  var validatingData = {
    name: [name, "isRequired"],
  };

  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  store.dispatch("setValidationError", errors);

  return { errors, isValid };
}
