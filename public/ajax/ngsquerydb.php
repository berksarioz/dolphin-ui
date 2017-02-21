<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$query = new dbfuncs();

$data = "";
$uid = "";
$gids = "";
$perms = "";
$andPerms = "";

if (isset($_GET['p'])){$p = $_GET['p'];}

if (isset($_GET['uid'])){$uid = $_GET['uid'];}
if (isset($_GET['gids'])){$gids = $_GET['gids'];}
if($uid != "" && $gids != "" && $_SESSION['uid'] != "1"){
    $perms = "WHERE (((group_id in ($gids)) AND (perms >= 15)) OR (owner_id = $uid) OR (perms >= 32))";
    $andPerms = "AND (((group_id in ($gids)) AND (perms >= 15)) OR (owner_id = $uid) OR (perms >= 32))";
}

$innerJoin = "LEFT JOIN ngs_source
                ON ngs_samples.source_id = ngs_source.id
                LEFT JOIN ngs_organism
                ON ngs_samples.organism_id = ngs_organism.id
                LEFT JOIN ngs_molecule
                ON ngs_samples.molecule_id = ngs_molecule.id
                LEFT JOIN ngs_genotype
                ON ngs_samples.genotype_id = ngs_genotype.id
                LEFT JOIN ngs_library_type
                ON ngs_samples.library_type_id = ngs_library_type.id
                LEFT JOIN ngs_biosample_type
                on ngs_samples.biosample_type_id = ngs_biosample_type.id
                LEFT JOIN ngs_instrument_model
                ON ngs_samples.instrument_model_id = ngs_instrument_model.id
                LEFT JOIN ngs_treatment_manufacturer
                ON ngs_samples.treatment_manufacturer_id = ngs_treatment_manufacturer.id";

$sampleJoin = "LEFT JOIN ngs_fastq_files
                ON ngs_samples.id = ngs_fastq_files.sample_id";

$laneJoin = "LEFT JOIN ngs_facility
                ON ngs_lanes.facility_id = ngs_facility.id";

$experimentSeriesJoin = "LEFT JOIN ngs_lab
                        ON ngs_experiment_series.lab_id = ngs_lab.id
                        LEFT JOIN ngs_organization
                        ON ngs_experiment_series.organization_id = ngs_organization.id";

if (isset($_GET['start'])){$start = $_GET['start'];}
if (isset($_GET['end'])){$end = $_GET['end'];}

if($p == 'getRunSamples')
{
	//Grab Variables
	if (isset($_GET['runID'])){$runID = $_GET['runID'];}

	$data=$query->queryTable("
	SELECT DISTINCT sample_id
	FROM ngs_runlist
	WHERE ngs_runlist.run_id = $runID $andPerms
	");
}
else if ($p == 'grabReload')
{
	//Grab variables
	if (isset($_GET['groupID'])){$groupID = $_GET['groupID'];}

	$data=$query->queryTable("
	SELECT outdir, json_parameters, run_name, run_description, group_id, perms
	FROM ngs_runparams
	WHERE ngs_runparams.id = $groupID $andPerms
	");
}
else if ($p == 'getReportNames')
{
	if (isset($_GET['runid'])){$runid = $_GET['runid'];}
    if (isset($_GET['samp'])){$samp = $_GET['samp'];}
	$sampleQuery = '';
    $samples = explode(",", $samp);

	foreach($samples as $s){
		$sampleQuery.= 'ngs_runlist.sample_id = '+ $s;
		if($s != end($samples)){
			$sampleQuery.= ' OR ';
		}
	}

	$data=$query->queryTable("
		SELECT distinct(ngs_fastq_files.file_name), ngs_runparams.outdir
		FROM ngs_fastq_files, ngs_runparams, ngs_runlist
		WHERE ngs_runlist.sample_id = ngs_fastq_files.sample_id
		AND ngs_runparams.id = ngs_fastq_files.lane_id
			AND ngs_fastq_files.lane_id = $runid
			AND ( $sampleQuery )
            $andPerms;
	");
}
else if ($p == 'getSampleTracking')
{
	if (isset($_GET['runid'])){$runid = $_GET['runid'];}
    if (isset($_GET['samp'])){$samp = $_GET['samp'];}
	$sampleQuery = '';
    $samples = explode(",", $samp);

	foreach($samples as $s){
		$sampleQuery.= 'ngs_runlist.sample_id = '+ $s;
		if($s != end($samples)){
			$sampleQuery.= ' OR ';
		}
	}

	$data=$query->queryTable("SELECT ngs_samples.name AS sample,
      ngs_lanes.name AS lane, ngs_experiment_series.experiment_name
      AS experiment, ngs_fastq_files.file_name, ngs_dirs.backup_dir,
      ngs_dirs.amazon_bucket
    FROM ngs_samples
    LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
    LEFT JOIN ngs_experiment_series
      ON ngs_samples.series_id = ngs_experiment_series.id
    LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
    LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id");
}


else if ($p == 'lanesToSamples')
{
	if (isset($_GET['lane'])){$lane = $_GET['lane'];}
	$data=$query->queryTable("
		SELECT id
		FROM ngs_samples
		WHERE ngs_samples.lane_id = $lane $andPerms
	");
}
else if ($p == 'getAllSampleIds')
{
	$data=$query->queryTable("
		SELECT id
		FROM ngs_samples $perms
	");
}
else if ($p == 'getAllLaneIds')
{
	$data=$query->queryTable("
		SELECT id
		FROM ngs_lanes $perms
	");
}
else if ($p == 'getAllExperimentIds')
{
	$data=$query->queryTable("
		SELECT id
		FROM ngs_experiment_series $perms
	");
}
else if ($p == 'getLaneIdFromSample')
{
	if (isset($_GET['sample'])){$sample = $_GET['sample'];}
	$data=$query->queryTable("
		SELECT id
		FROM ngs_lanes
		where id =
				(select lane_id
				from ngs_samples
				where ngs_samples.id = $sample)
        $andPerms;
	");
}
else if ($p == 'getExperimentIdFromSample')
{
    if (isset($_GET['sample'])){$sample = $_GET['sample'];}
    $data=$query->queryTable("
		SELECT id
		FROM ngs_experiment_series
		where id =
				(select series_id
				from ngs_samples
				where ngs_samples.id = $sample);
	");
}
else if($p == 'getSingleSample')
{
	if (isset($_GET['sample'])){$sample = $_GET['sample'];}
	$data=$query->queryTable("
		SELECT id, name, samplename
		FROM ngs_samples
		where id = $sample $andPerms
	");
}
else if($p == 'getSeriesIdFromLane')
{
	if (isset($_GET['lane'])){$lane = $_GET['lane'];}
	$data=$query->queryTable("
		SELECT series_id
		FROM ngs_lanes
		where id = $lane $andPerms
	");
}
else if ($p == 'checkMatePaired')
{
	if (isset($_GET['runid'])){$runid = $_GET['runid'];}
	$data=$query->queryTable("
		SELECT json_parameters
		FROM ngs_runparams
		where id = $runid $andPerms
	");
}
else if ($p == 'getSampleNames')
{
    if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT name, samplename
		FROM ngs_samples
		where id in ($samples) $andPerms
	");
}
else if ($p == 'getWKey')
{
    if (isset($_GET['run_id'])){$run_id = $_GET['run_id'];}
    $data=$query->queryTable("
    SELECT wkey
    FROM ngs_runparams
    WHERE id = $run_id
    ");
}
else if ($p == 'getFastQCBool')
{
    if (isset($_GET['id'])){$id = $_GET['id'];}
    $data=$query->queryTable("
    SELECT json_parameters
    FROM ngs_runparams
    WHERE id = $id
    ");
}
else if ($p == 'getReportList')
{
    if (isset($_GET['wkey'])){$wkey = $_GET['wkey'];}
    $data=$query->queryTable("
    SELECT version, type, file
    FROM report_list
    WHERE wkey = '$wkey'
    ");
}
else if ($p == 'getTSVFileList')
{
    if (isset($_GET['wkey'])){$wkey = $_GET['wkey'];}
    $data=$query->queryTable("
    SELECT file
    FROM report_list
    WHERE wkey = '$wkey' and file like '%.tsv'
    ");
}
else if ($p == 'profileLoad')
{
    $data=$query->queryTable("
    SELECT photo_loc
    FROM users
    WHERE username = '".$_SESSION['user']."'"
    );
}
else if ($p == 'obtainAmazonKeys')
{
    $data=$query->queryTable("
    SELECT * FROM amazon_credentials WHERE id IN(
        SELECT amazon_id FROM group_amazon WHERE id IN(
            SELECT id FROM groups WHERE id IN(
                SELECT g_id FROM user_group WHERE u_id = ".$_SESSION['uid'].")))
    ");
}
else if ($p == 'checkAmazonPermissions')
{
    if (isset($_GET['a_id'])){$a_id = $_GET['a_id'];}
    $data=$query->queryTable("
    SELECT id FROM groups WHERE owner_id = ".$_SESSION['uid']." AND id IN(
    SELECT group_id FROM group_amazon WHERE amazon_id = (
    SELECT DISTINCT id FROM amazon_credentials where id = $a_id));
    ");
}
else if($p == 'getSamplesFromName')
{
    if (isset($_GET['names'])){$names = $_GET['names'];}
    if (isset($_GET['lane'])){$lane = $_GET['lane'];}
    if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}
    $names = explode(",", $names);
    $sqlnames = "";
    foreach($names as $n){
        if($n != end($names)){
            $sqlnames.= "'".$n."',";
        }else{
            $sqlnames.= "'".$n."'";
        }
    }
    $data=$query->queryTable("
    SELECT DISTINCT ns.id
    FROM ngs_samples ns, ngs_lanes nl, ngs_experiment_series ne
    WHERE ns.name in ($sqlnames)
    AND ns.lane_id IN (SELECT id from ngs_lanes where name in ($lane))
    AND ns.series_id IN (SELECT id from ngs_experiment_series where experiment_name = '$experiment');
    ");
}
else if ($p == 'getLanesWithSamples')
{
    $data=$query->queryTable("
    SELECT ngs_lanes.id, ngs_lanes.owner_id
    FROM ngs_lanes
    WHERE ngs_lanes.id in (
        SELECT ngs_samples.lane_id
        FROM ngs_samples
        WHERE id in (
            SELECT ngs_fastq_files.sample_id
            FROM ngs_fastq_files
            WHERE total_reads > 0
        )
    )
    ");
}
else if ($p == 'getSamplesfromExperimentSeries')
{
    if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}
    $data=$query->queryTable("
    SELECT id
    FROM ngs_samples
    where series_id = $experiment
    ");
}
else if ($p == 'getCustomTSV')
{
	$data=$query->queryTable("
    SELECT name, file
    FROM ngs_createdtables
    where owner_id = ".$_SESSION['uid']
    );
}
else if ($p == 'checkOutputDir')
{
	if (isset($_GET['outdir'])){$outdir = $_GET['outdir'];}
	$data=json_encode($query->queryAVal("
    SELECT outdir
    FROM ngs_runparams
    where outdir = '$outdir'
    "));
}
else if ($p == 'checkRunID')
{
	if (isset($_GET['outdir'])){$outdir = $_GET['outdir'];}
	$data=json_encode($query->queryAVal("
    SELECT run_group_id
    FROM ngs_runparams
    where outdir = '$outdir'
    "));
}
else if ($p == 'changeDataGroupNames')
{
	if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}
	$owner_check=$query->queryAVal("
	SELECT owner_id
	FROM ngs_experiment_series
	WHERE id = $experiment
	");
	if($owner_check == $_SESSION['uid']){
		$data=$query->queryTable("
		SELECT id,name
		FROM groups
		WHERE id IN (
			SELECT g_id
			FROM user_group
			WHERE u_id = " . $_SESSION['uid'] . "
			)
		");
	}else{
		$data=json_encode("");
	}
}
else if ($p == 'changeDataGroup')
{
	if (isset($_GET['group_id'])){$group_id = $_GET['group_id'];}
	if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}

	//	EXPERIMENT SERIES
	$ES_UPDATE=$query->runSQL("
	UPDATE ngs_experiment_series
	SET group_id = $group_id
	WHERE id = $experiment
	");
	//	IMPORTS
	$IMPORTS_UPDATE=$query->runSQL("
	UPDATE ngs_lanes
	SET group_id = $group_id
	WHERE series_id = $experiment
	");
	//	SAMPLES
	$SAMPLE_UPDATE=$query->runSQL("
	UPDATE ngs_samples
	SET group_id = $group_id
	WHERE series_id = $experiment
	");
	$data=json_encode('passed');
}
else if ($p == 'getExperimentSeriesGroup')
{
	if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}
	$data=$query->queryTable("
	SELECT group_id, owner_id
	FROM ngs_experiment_series
	WHERE id = $experiment
	");
}
else if ($p == 'getGroups')
{
	$data=$query->queryTable("
	SELECT id, name
	FROM groups
	WHERE id in (
		SELECT g_id
		FROM user_group
		WHERE u_id = ".$_SESSION['uid']."
	)
	");
}
else if ($p == 'getRunPerms')
{
	if (isset($_GET['run_id'])){$run_id = $_GET['run_id'];}
	$data=$query->queryAVal("
	SELECT perms
	FROM ngs_runparams
	WHERE id = $run_id
	");
}
else if ($p == 'changeRunGroup')
{
	if (isset($_GET['run_id'])){$run_id = $_GET['run_id'];}
	if (isset($_GET['group_id'])){$group_id = $_GET['group_id'];}
	$RUNPARAM_UPDATE=$query->runSQL("
	UPDATE ngs_runparams
	SET group_id = $group_id
	WHERE id = $run_id
	");
	$data=json_encode('pass');
}
else if ($p == 'changeRunPerms')
{
	if (isset($_GET['run_id'])){$run_id = $_GET['run_id'];}
	if (isset($_GET['perms'])){$perms = $_GET['perms'];}
	$RUNPARAM_UPDATE=$query->runSQL("
	UPDATE ngs_runparams
	SET perms = $perms
	WHERE id = $run_id
	");
	$data=json_encode('pass');
}
else if ($p == 'getAllUsers')
{
	if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}
	$owner_check=$query->queryAVal("
	SELECT owner_id
	FROM ngs_experiment_series
	WHERE id = $experiment
	");
	if($owner_check == $_SESSION['uid']){
		$data=$query->queryTable("
		SELECT id, username
		FROM users
		");
	}else{
		$data=json_encode("");
	}
}
else if ($p == "changeOwnerExperiment")
{
	if (isset($_GET['owner_id'])){$owner_id = $_GET['owner_id'];}
	if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}

	//	EXPERIMENT SERIES
	$ES_UPDATE=$query->runSQL("
	UPDATE ngs_experiment_series
	SET owner_id = $owner_id
	WHERE id = $experiment
	");
	//	IMPORTS
	$IMPORTS_UPDATE=$query->runSQL("
	UPDATE ngs_lanes
	SET owner_id = $owner_id
	WHERE series_id = $experiment
	");
	//	SAMPLES
	$SAMPLE_UPDATE=$query->runSQL("
	UPDATE ngs_samples
	SET owner_id = $owner_id
	WHERE series_id = $experiment
	");
	$data=json_encode('passed');
}
else if ($p == 'getPipelineSamples')
{
	if (isset($_GET['id'])){$id = $_GET['id'];}
	//	inner join for samples table
	$innerJoin = "LEFT JOIN ngs_source
					ON ngs_samples.source_id = ngs_source.id
					LEFT JOIN ngs_organism
					ON ngs_samples.organism_id = ngs_organism.id
					LEFT JOIN ngs_molecule
					ON ngs_samples.molecule_id = ngs_molecule.id
					LEFT JOIN ngs_genotype
					ON ngs_samples.genotype_id = ngs_genotype.id
					LEFT JOIN ngs_library_type
					ON ngs_samples.library_type_id = ngs_library_type.id
					LEFT JOIN ngs_biosample_type
					on ngs_samples.biosample_type_id = ngs_biosample_type.id
					LEFT JOIN ngs_instrument_model
					ON ngs_samples.instrument_model_id = ngs_instrument_model.id
					LEFT JOIN ngs_treatment_manufacturer
					ON ngs_samples.treatment_manufacturer_id = ngs_treatment_manufacturer.id";
	$amazon_str = "AND ngs_fastq_files.dir_id = (SELECT ngs_dirs.id FROM ngs_dirs WHERE ngs_fastq_files.dir_id = ngs_dirs.id AND (ngs_dirs.amazon_bucket LIKE '%s3://%'))";
	$sampleJoin = "LEFT JOIN ngs_fastq_files
					ON ngs_samples.id = ngs_fastq_files.sample_id";
	$sampleBackup = "CASE
						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE aws_status = 2 AND ngs_samples.id = ngs_fastq_files.sample_id) > 0 THEN '<td><button class=\"btn btn-warning\" disabled></td>'
						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE checksum != original_checksum AND (original_checksum != '' AND original_checksum IS NOT NULL) AND ngs_samples.id = ngs_fastq_files.sample_id) > 0 THEN '<td><button class=\"btn btn-flickr\" disabled></td>'
						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE checksum != backup_checksum AND (backup_checksum != '' AND backup_checksum IS NOT NULL) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<td><button class=\"btn btn-danger\" disabled></td>'
						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE (backup_checksum = '' OR backup_checksum IS NULL) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<td><button class=\"btn\" disabled></td>'
						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE date_modified < DATE_SUB(now(), INTERVAL 2 MONTH) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<td><button class=\"btn btn-primary\" disabled></td>'
						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) = 0 THEN '<td></td>'
						ELSE '<td><button class=\"btn btn-success\" disabled></td>'
					END AS backup";
	$data=$query->queryTable("
	SELECT ngs_samples.id, ngs_samples.series_id, ngs_samples.lane_id, name, samplename, title, source, organism, molecule, total_reads, barcode, description, avg_insert_size, read_length, concentration, time, biological_replica, technical_replica, spike_ins, adapter,
	notebook_ref, notes, genotype, library_type, biosample_type, instrument_model, treatment_manufacturer, ngs_samples.owner_id,backup_checksum,
	$sampleBackup
	FROM ngs_samples
	$innerJoin
	$sampleJoin
	WHERE ngs_samples.id = $id
	");
}
else if ( $p == "clearPreviousSamples")
{
	if (isset($_GET['run_id'])){$run_id = $_GET['run_id'];}
	$data=$query->runSQL("DELETE FROM ngs_runlist WHERE run_id = $run_id");
}
else if ($p == 'runOwnerCheck')
{
	if (isset($_GET['run_id'])){$run_id = $_GET['run_id'];}
	$idCheck=$query->queryAVal("
		SELECT owner_id
		FROM ngs_runparams
		where id = $run_id
	");
	if($_SESSION['uid'] == '1' || $idCheck == $_SESSION['uid']){
		$data = json_encode("pass");
	}else{
		$data = json_encode('Permission Denied');
	}
}
else if ($p == 'checkFileLocation')
{
	if (isset($_GET['outdir'])){$outdir = $_GET['outdir'];}
	$data=$query->queryTable("
		SELECT backup_dir
		FROM ngs_dirs
		where backup_dir = '$outdir'
	");
}

if (!headers_sent()) {
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo $data;
	exit;
}else{
	echo $data;
}
?>
