<?php

class Browse extends VanillaModel {

  function getAllTrackingData(){
    $result = $this->query("");

    return $result;
  }

}
