function registerUser() {
  var newUser = {};
  newUser.login = $('#it-login').val();
  newUser.password = $('#it-password').val();
  newUser.repeatPassword = $('#it-password-repeat').val();
  newUser.email = $('#it-email').val();

  sendRequest('User/registerUser', newUser, function(res) {
    var result = $.parseJSON(res);
    var msgHtml = '';

    if (result.result == false) {
      $(result.errors).each(function(id, elm) {
        msgHtml += '<div class="error">' + elm + '</div>';
      });
    } else {
      msgHtml += '<div class="success">Zarejestrowano nowego użytkownika</div>';
      $('#register-holder input').val('');
    }
    
    $('#register-error-holder').html(msgHtml);
  })

};

$(document).ready(function() {

  sendRequest('User/isLoggedUser', null, function(res) {
    if (res == 'false') {
      $('#login-menu').show();
      $('#register-menu').show();
      $('#logout-menu').hide();
    } else {
      $('#login-menu').hide();
      $('#register-menu').hide();
      $('#logout-menu').show();
    }

    $('#btn-login').click(function() {
      registerUser();
    });
  })
});