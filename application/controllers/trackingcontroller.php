<?php

class TrackingController extends VanillaController {

	function beforeAction() {

	}

	function index() {
		$this->set('field', "Tracking");
		$this->set('segment', "index");

        $this->set('uid', $_SESSION['uid']);
        $gids = $this->Tracking->getGroup($_SESSION['user']);
        $this->set('gids', $gids);
		$this->set('backup_status', 'back up' );
		//
		// $this->set('experiment_series', $this->Tracking->getValues($value, 'ngs_experiment_series'));
		//
		// $this->set('lane', $this->Tracking->getAccItems("id", "ngs_lanes", $_SESSION['uid'], $gids));
		// #$this->set('organism', $this->Tracking->getAccItems("organism", "ngs_organism", $_SESSION['uid'], $gids));
		// #$this->set('molecule', $this->Tracking->getAccItems("molecule", "ngs_molecule", $_SESSION['uid'], $gids));
		// #$this->set('source', $this->Tracking->getAccItems("source", "ngs_source", $_SESSION['uid'], $gids));
		// $this->set('genotype', $this->Tracking->getAccItems("genotype", "ngs_genotype", $_SESSION['uid'], $gids));
	}
	//
	// function browse($field, $value, $search) {
	// 	$this->set('field', "Tracking");
	// 	$this->set('segment', "browse");
	// 	$this->set('table', $field);
	// 	$this->set('value', $value);
	// 	$this->set('search', $search);
	//
  //       $this->set('uid', $_SESSION['uid']);
  //       $gids = $this->Tracking->getGroup($_SESSION['user']);
  //       $this->set('gids', $gids);
	//
	// 	$this->set('assay', $this->Tracking->getAccItemsCont("library_type", "ngs_library_type", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('organism', $this->Tracking->getAccItemsCont("organism", "ngs_organism", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('molecule', $this->Tracking->getAccItemsCont("molecule", "ngs_molecule", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('source', $this->Tracking->getAccItemsCont("source", "ngs_source", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('genotype', $this->Tracking->getAccItemsCont("genotype", "ngs_genotype", $search, $_SESSION['uid'], $gids), $search);
	// }
	// function details($table, $value, $search) {
	// 	$this->set('field', "Tracking");
	// 	$this->set('segment', "details");
	// 	$this->set('value', $value);
	// 	$this->set('table', $table);
	// 	$this->set('search', $search);
	//
  //       $this->set('uid', $_SESSION['uid']);
  //       $gids = $this->Tracking->getGroup($_SESSION['user']);
  //       $this->set('gids', $gids);
	// 	$this->set('assay', $this->Tracking->getAccItemsCont("library_type", "ngs_library_type", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('organism', $this->Tracking->getAccItemsCont("organism", "ngs_organism", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('molecule', $this->Tracking->getAccItemsCont("molecule", "ngs_molecule", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('source', $this->Tracking->getAccItemsCont("source", "ngs_source", $search, $_SESSION['uid'], $gids), $search);
	// 	$this->set('genotype', $this->Tracking->getAccItemsCont("genotype", "ngs_genotype", $search, $_SESSION['uid'], $gids), $search);
	//
	// 	if ($table=='experiment_series')
	// 	{
	// 		$this->set('experiment_series_fields', $this->Tracking->getFields('ngs_experiment_series'));
	// 		$this->set('checkPerms', $this->Tracking->checkViewPerms('ngs_experiment_series', $value, $gids));
	// 		$this->set('experiment_series', $this->Tracking->getValues($value, 'ngs_experiment_series'));
	// 	}
	// 	if ($table=='experiments')
	// 	{
	// 		$this->set('experiment_series_fields', $this->Tracking->getFields('ngs_experiment_series'));
	// 		$this->set('experiment_fields', $this->Tracking->getFields('ngs_lanes'));
	// 		$this->set('checkPerms', $this->Tracking->checkViewPerms('ngs_lanes', $value, $gids));
	// 		$this->set('experiment_series', $this->Tracking->getValues($this->Tracking->getId($value, 'series_id', 'ngs_lanes'), 'ngs_experiment_series'));
	// 		$this->set('experiments', $this->Tracking->getValues($value, 'ngs_lanes'));
  //           $this->set('lane_file', $this->Tracking->getLaneFileLocation($value));
	// 		$this->set('dir_array', $this->Tracking->getInputLaneDirectories($value));
	// 	}
	// 	if ($table=='samples')
	// 	{
	// 		$this->set('experiment_series_fields', $this->Tracking->getFields('ngs_experiment_series'));
	// 		$this->set('experiment_fields', $this->Tracking->getFields('ngs_lanes'));
	// 		$this->set('sample_fields', $this->Tracking->getFields('ngs_samples'));
	// 		$this->set('checkPerms', $this->Tracking->checkViewPerms('ngs_samples', $value, $gids));
	// 		$this->set('experiment_series', $this->Tracking->getValues($this->Tracking->getId($value, 'series_id', 'ngs_samples'), 'ngs_experiment_series'));
	// 		$this->set('experiments', $this->Tracking->getValues($this->Tracking->getId($value, 'lane_id', 'ngs_samples'), 'ngs_lanes'));
  //           $this->set('lane_file', $this->Tracking->getLaneFileLocation($this->Tracking->getId($value, 'lane_id', 'ngs_samples')));
	// 		$this->set('samples', $this->Tracking->getValues($value, 'ngs_samples'));
  //           $this->set('sample_file', $this->Tracking->getSampleFileLocation($value));
  //           $this->set('sample_fastq_file', $this->Tracking->getSampleFastqFileLocation($value));
	// 		$temp_runlist = $this->Tracking->getRuns($value, $gids);
	// 		$this->set('sample_runs', $temp_runlist);
	// 		$this->set('sample_tables', $this->Tracking->getTables($temp_runlist, $gids));
	// 		$this->set('dir_array', $this->Tracking->getInputSampleDirectories($value));
	// 	}
	// }


	function afterAction() {

	}

}
