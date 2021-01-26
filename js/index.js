
$(document).ready(function() {
  sendRequest('User/isLoggedUser', null, function(res) {
    if(res == 'false') {
      $('#login-menu').show();
      $('#register-menu').show();
      $('#logout-menu').hide();
    } else {
      $('#login-menu').hide();
      $('#register-menu').hide();
      $('#logout-menu').show();
    }
      
  })
});