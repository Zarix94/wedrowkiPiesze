function sendRequest(method, data, callback) {
  var requestData = {
    'method' : method,
    'data' : data
  };

  var url = './class/DefaultController.php';

  $.post(url, requestData, callback);
}
