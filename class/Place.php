<?php

class Place {

  public static function getVisitedPlaces(int $tourId) {
    $db = DBase::getInstance();
    $result = $db->query('SELECT * FROM place WHERE tour_id =' . intval($tourId));
    return count($result) > 0 ? $result : [];
  }
}