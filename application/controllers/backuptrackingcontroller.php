<?php

class BackuptrackingController extends VanillaController {

	function beforeAction() {

	}

	function index() {
		$this->set('field', "Backuptracking");
		$this->set('segment', "index");
		$this->set('uid', $_SESSION['uid']);

		if(isset($_GET["selected"]) && $_GET["selected"] == "amazon"){
			$all_data = $this->Backuptracking->getAllTrackingDataAmazon();
		} elseif(isset($_GET["selected"]) && $_GET["selected"] == "backup_checksum"){
			$all_data = $this->Backuptracking->getAllTrackingDataBackupChecksum();
		} else {
			$all_data = $this->Backuptracking->getAllTrackingDataBoth();
		}

		$this->set('all_tracking_data', json_decode($all_data));
  }

	function afterAction() {

	}

}
