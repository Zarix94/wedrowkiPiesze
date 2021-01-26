<?php
require_once 'Place.php';

class WalkingTour {

  public $editable = false;

  public $userId;

  public $name;

  public $description;

  public $dateStart;

  public $dateStop;

  public $km;

  public $places = [];

  public static function getAll(): array {
    $db = DBase::getInstance();
    $result = $db->query('SELECT * FROM walking_tour WHERE deleted IS NULL');

    return count($result) > 0 ? $result : [];
  }

  public static function getTourList(): String {
    $tourList = self::getAll();
    $html = '<table id="trip-table" class="display" style="width: 100%">';
    $html .= '  <thead>';
    $html .= '    <tr>';
    $html .= '      <th>Nazwa</th>';
    $html .= '      <th>Data rozpoczęcia</th>';
    $html .= '      <th>Data zakończenia</th>';
    $html .= '      <th>Ilość km</th>';
    $html .= '    </tr>';
    $html .= '  </thead>';
    $html .= '  <tbody>';
    foreach ($tourList as $tour) {
      $html .= '    <tr data-id="' . $tour['id'] . '">';
      $html .= '      <td>' . $tour['name'] . '</td>';
      $html .= '      <td>' . $tour['date_start'] . '</td>';
      $html .= '      <td>' . $tour['date_stop'] . '</td>';
      $html .= '      <td>' . $tour['km'] . '</td>';
      $html .= '    </tr>';
    }

    $html .= '  </tbody>';
    $html .= 'table';

    return $html;
  }

  public function getFromDb(): array {
    $db = DBase::getInstance();
    $result = $db->query('SELECT * FROM walking_tour WHERE id =' . intval($this->id) . ' AND deleted IS NULL');

    return count($result) > 0 ? $result[0] : [];
  }

  public static function getTourDetails($data) {
    $tour = new WalkingTour();
    $tour->id = $data['id'];
    $tourData = $tour->getFromDb();
    $tour->userId = $tourData['user_id'];
    $tour->name = $tourData['name'];
    $tour->description = $tourData['description'];
    $tour->dateStart = $tourData['date_start'];
    $tour->dateStop = $tourData['date_stop'];
    $tour->km = $tourData['km'];

    $tour->editable = $_SESSION['user']->id == $tour->userId;
    $tour->places = Place::getVisitedPlaces($tour->id);

    return $tour;
  }

  private static function getTime($time) {
    $time = strtotime($time);
    return date('Y-m-d', $time);
  }

  private static function insertTour($data) {
    $db = DBase::getInstance();
    $query = '';
    $query .= 'INSERT INTO walking_tour ( ';
    $query .= '  user_id, ';
    $query .= '  name, ';
    $query .= '  description, ';
    $query .= '  date_start, ';
    $query .= '  date_stop, ';
    $query .= '  km ';
    $query .= ') VALUES ( ';
    $query .= '  ' . intval($_SESSION['user']->id) . ', ';
    $query .= '  ' . DBase::esc($data['name']) . ', ';
    $query .= '  ' . DBase::esc($data['description']) . ', ';
    $query .= '  ' . DBase::esc(self::getTime($data['dateStart'])) . ', ';
    $query .= '  ' . DBase::esc(self::getTime($data['dateStop'])) . ', ';
    $query .= '  ' . intval($data['km']) . ' ';
    $query .= ') RETURNING id ';

    $result = $db->query($query);
    return $result[0]['id'];
  }

  private static function updateTour($data) {
    $db = DBase::getInstance();
    $query = '';
    $query .= 'UPDATE walking_tour SET ';
    $query .= '  user_id = ' . intval($_SESSION['user']->id) . ', ';
    $query .= '  name = ' . DBase::esc($data['name']) . ', ';
    $query .= '  description = ' . DBase::esc($data['description']) . ', ';
    $query .= '  date_start = ' . DBase::esc(self::getTime($data['dateStart'])) . ', ';
    $query .= '  date_stop = ' . DBase::esc(self::getTime($data['dateStop'])) . ', ';
    $query .= '  km = ' . intval($data['km']) . ' ';
    $query .= 'WHERE ';
    $query .= '  id = ' . intval($data['id']);

    $db->query($query);
  }

  public static function saveTour($data) {
    $tourId = 0;
    if (! isset($data['id']))
      $tourId = self::insertTour($data);
    else {
      $tourId = $data['id'];
      self::updateTour($data);
    }

    return self::getTourDetails([
      'id' => $tourId
    ]);
  }

  public static function addPlace($data) {
    $db = DBase::getInstance();
    $query = '';
    $query .= 'INSERT INTO place ( ';
    $query .= '  name,   ';
    $query .= '  visit_date, ';
    $query .= '  tour_id ';
    $query .= ') VALUES ( ';
    $query .= '  ' . DBase::esc($data['name']) . ', ';
    $query .= '  ' . DBase::esc(self::getTime($data['date'])) . ', ';
    $query .= '  ' . intval($data['tourId']) . ' ';
    $query .= ')';

    $db->query($query);

    return [
      'tourId' => $data['tourId']
    ];
  }
}