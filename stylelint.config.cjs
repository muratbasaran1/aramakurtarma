module.exports = {
  extends: ['stylelint-config-standard', 'stylelint-config-recommended-vue'],
  plugins: ['stylelint-order'],
  ignoreFiles: ['**/vendor/**', '**/storage/**'],
  rules: {
    'color-no-invalid-hex': true,
    'selector-class-pattern': [
      '^[a-z0-9\-_/]+$',
      {
        message: 'Sınıf isimlerinde yalnızca küçük harf, sayı ve tire kullanılabilir.',
      },
    ],
    'order/properties-alphabetical-order': true,
    'no-descending-specificity': null,
    'alpha-value-notation': 'percentage',
    'declaration-no-important': true,
  },
};
