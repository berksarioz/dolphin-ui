<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$query = new dbfuncs();
if (isset($_GET['p'])){$p = $_GET['p'];}

$amazon_str = "AND ngs_fastq_files.dir_id = (SELECT ngs_dirs.id FROM ngs_dirs WHERE ngs_fastq_files.dir_id = ngs_dirs.id AND (ngs_dirs.amazon_bucket LIKE '%s3://%'))";

$sampleBackup = "CASE
					WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE aws_status = 2 AND ngs_samples.id = ngs_fastq_files.sample_id) > 0 THEN '<button class=\"btn btn-warning\" disabled>'
					WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE checksum != original_checksum AND (original_checksum != '' AND original_checksum IS NOT NULL) AND ngs_samples.id = ngs_fastq_files.sample_id) > 0 THEN '<button class=\"btn btn-flickr\" disabled>'
					WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE checksum != backup_checksum AND (backup_checksum != '' AND backup_checksum IS NOT NULL) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<button class=\"btn btn-danger\" disabled>'
					WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE (backup_checksum = '' OR backup_checksum IS NULL) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<button class=\"btn btn-secondary\" disabled>'
					WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE date_modified < DATE_SUB(now(), INTERVAL 2 MONTH) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<button class=\"btn btn-primary\" disabled>'
					WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) = 0 THEN ''
					ELSE '<button class=\"btn btn-success\" disabled>'
				END AS backup";

$selectTracking = "SELECT ngs_samples.id AS sample_id,
    $sampleBackup,
		ngs_samples.name AS sample,
		ngs_experiment_series.experiment_name
		AS experiment, ngs_lanes.name AS lane,
		ngs_fastq_files.file_name, ngs_dirs.backup_dir,
		ngs_dirs.amazon_bucket
	FROM ngs_samples
	LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
	LEFT JOIN ngs_experiment_series
		ON ngs_samples.series_id = ngs_experiment_series.id
	LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
	LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id";

$amazon_not_null = "ngs_dirs.id=ngs_fastq_files.dir_id and ngs_dirs.amazon_bucket!=''
and ngs_dirs.amazon_bucket != 'none'";


if ($p == 'getTrackingData')
{

	$data=$query->queryTable("$selectTracking
		WHERE $amazon_not_null and
		(ngs_fastq_files.backup_checksum='' or isnull(ngs_fastq_files.backup_checksum)
		or DATE(ngs_fastq_files.date_modified) <= DATE(NOW() - INTERVAL 2 MONTH))"
    );
}
else if ($p == 'getTrackingDataAmazon')
{
	$data=$query->queryTable("$selectTracking
		WHERE $amazon_not_null"
    );
}

else if ($p == 'getTrackingDataBackup')
{
	$data=$query->queryTable("$selectTracking
		WHERE
		(ngs_fastq_files.backup_checksum='' or isnull(ngs_fastq_files.backup_checksum)
		or DATE(ngs_fastq_files.date_modified) <= DATE(NOW() - INTERVAL 2 MONTH))"
    );
}

else if ($p == 'getTrackingDataUnfiltered')
{
	$data=$query->queryTable("$selectTracking"
    );
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
