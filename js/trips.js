var tour = null;

function setTourDetails(tour) {
  $('#it-trip-name').val(tour.name);
  $('#it-trip-start').val(tour.dateStart);
  $('#it-trip-stop').val(tour.dateStop);
  $('#it-trip-description').val(tour.description);
  $('#it-trip-length').val(tour.km);

  var html = '';
  $(tour.places).each(function(idx, elm) {
    html += '  <li>';
    html += '  <div style="display: flex">';
    html += '    <div style="width: 50%">' + elm.visit_date + '</div>';
    html += '    <div style="width: 50%">' + elm.name + '</div>';
    html += '  </div>';
    html += '</li>';
  });

  $('#place-list').html(html);
  if (tour.editable == true) {
    $('#add-place').show();
    $('#save-trip').show();
  } else
    $('#save-trip').hide();

}

function getTourDetails(tourId) {
  console.log(tourId);
  var data = {
    id : tourId
  };
  
  console.log(data);
  
  sendRequest('WalkingTour/getTourDetails', data, function(res) {
    var result = $.parseJSON(res);
    tour = result;
    setTourDetails(tour);
  });
}

function getTourList() {
  sendRequest('WalkingTour/getTourList', null, function(res) {
    var result = $.parseJSON(res);
    $('#trip-list').html(result);
    $('#trip-table').DataTable();

    $('#trip-table tr').click(function() {
      getTourDetails($(this).attr('data-id'));
    });
  });
}

function clearFields() {
  $('input').val('');
  $('textarea').val('');
  $('#place-list').html('');
  $('#add-place').hide();
  $('#save-trip').show();
  tour = null;
}

function saveTour() {
  if (tour == null)
    tour = {};
  tour.dateStart = $('#it-trip-start').val();
  tour.dateStop = $('#it-trip-stop').val();
  tour.description = $('#it-trip-description').val();
  tour.km = $('#it-trip-length').val();
  tour.name = $('#it-trip-name').val();

  sendRequest('WalkingTour/saveTour', tour, function(res) {
    var result = $.parseJSON(res);
    tour = result;
    getTourList();
    $('#add-place').show();

  });
}

function savePlace() {
  var obj = {
    tourId : tour.id,
    name : $('#it-place-name').val(),
    date : $('#it-place-date').val()
  };

  sendRequest('WalkingTour/addPlace', obj, function(res) {
    var result = $.parseJSON(res);
    getTourDetails(result.tourId);
    $('#add-place-dialog-holder').hide();
    $('#add-place-dialog-holder input').val('');
  });

}

$(document).ready(function() {

  sendRequest('User/isLoggedUser', null, function(res) {
    if (res == 'false') {
      $('#login-menu').show();
      $('#register-menu').show();
      $('#logout-menu').hide();
      $('#add-trip').hide();
      $('#edit-trip').hide();
    } else {
      $('#login-menu').hide();
      $('#register-menu').hide();
      $('#logout-menu').show();
      $('#add-trip').show();
      $('#edit-trip').show();
    }
  });
  getTourList();

  $('#new-trip').click(function(elm) {
    clearFields();
  });

  $('#save-trip').click(function(elm) {
    saveTour();
  });

  $('#add-place').click(function(elm) {
    $('#add-place-dialog-holder').show();
  });

  $('#close-place').click(function(elm) {
    $('#add-place-dialog-holder').hide();
  });

  $('#save-place').click(function(elm) {
    savePlace();
  });

});