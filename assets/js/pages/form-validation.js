'use strict';
(function () {

  var bouncer = new Bouncer('[data-validate]', {
    disableSubmit: true,
    customValidations: {
      valueMismatch: function (field) {
        var selector = field.getAttribute('data-bouncer-match');
        if (!selector) return false;
        var otherField = field.form.querySelector(selector);
        if (!otherField) return false;
        return otherField.value !== field.value;
      }
    },
    messages: {
      valueMismatch: function (field) {
        var customMessage = field.getAttribute('data-bouncer-mismatch-message');
        return customMessage ? customMessage : 'Please make sure the fields match.';
      }
    }
  });

  document.addEventListener(
    'bouncerFormInvalid',
    function (event) {
      window.scrollTo(0, event.detail.errors[0].offsetTop);
    },
    false
  );

  document.addEventListener(
    'bouncerFormValid',
    function () {
      //alert('Form submitted successfully!');
      console.log('Formulario válido:', event.target);
      //var form = document.getElementById('validate-me');
      var form = event.target;
      form.submit(); // Enviar el formulario
      //window.location.reload();
    },
    false
  );

})();
