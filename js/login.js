function login() {
  var credentials = {
    login : $('#it-login').val(),
    password : $('#it-password').val()

  };

  sendRequest('User/login', credentials, function(res) {
    var result = $.parseJSON(res);

    if (result.result == true)
      window.location.href = "main.html";
    else {
      var msgHtml = '';
      $(result.errors).each(function(id, elm) {
        msgHtml += '<div class="error">' + elm + '</div>';
      });
      
      $('#login-error-holder').html(msgHtml);

    }

    console.log(res);
  });
}

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

    $('#btn-login').click(function(idx, elm) {
      login();
    });
  })
});