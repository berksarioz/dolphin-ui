<?php

class Backuptracking extends VanillaModel {

  function getAllTrackingData(){
    $result = $this->query("SELECT ngs_samples.name AS sample,
        ngs_samples.id AS sample_id,
        ngs_lanes.name AS lane, ngs_experiment_series.experiment_name
        AS experiment, ngs_fastq_files.file_name, ngs_dirs.backup_dir,
        ngs_dirs.amazon_bucket
      FROM ngs_samples
      LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
      LEFT JOIN ngs_experiment_series
        ON ngs_samples.series_id = ngs_experiment_series.id
      LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
      LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id");

    return $result;
  }


  function getAllTrackingDataBoth(){

    $result = $this->query("SELECT ngs_samples.name AS sample,
        ngs_samples.id AS sample_id,
        ngs_lanes.name AS lane, ngs_experiment_series.experiment_name
        AS experiment, ngs_fastq_files.file_name, ngs_dirs.backup_dir,
        ngs_dirs.amazon_bucket
      FROM ngs_samples
      LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
      LEFT JOIN ngs_experiment_series
        ON ngs_samples.series_id = ngs_experiment_series.id
      LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
      LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id
      WHERE ngs_dirs.id=ngs_fastq_files.dir_id and ngs_dirs.amazon_bucket!=''
      and ngs_dirs.amazon_bucket != 'none' and
      (ngs_fastq_files.backup_checksum='' or isnull(ngs_fastq_files.backup_checksum)
      or DATE(ngs_fastq_files.date_modified) <= DATE(NOW() - INTERVAL 2 MONTH))
      ");

    return $result;
  }

  function getAllTrackingDataBackupChecksum(){

    $result = $this->query("SELECT ngs_samples.name AS sample,
        ngs_samples.id AS sample_id,
        ngs_lanes.name AS lane, ngs_experiment_series.experiment_name
        AS experiment, ngs_fastq_files.file_name, ngs_dirs.backup_dir,
        ngs_dirs.amazon_bucket
      FROM ngs_samples
      LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
      LEFT JOIN ngs_experiment_series
        ON ngs_samples.series_id = ngs_experiment_series.id
      LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
      LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id
      WHERE
      (ngs_fastq_files.backup_checksum='' or isnull(ngs_fastq_files.backup_checksum)
      or DATE(ngs_fastq_files.date_modified) <= DATE(NOW() - INTERVAL 2 MONTH))
      ");

    return $result;
  }



      function getAllTrackingDataAmazon(){
        $amazon_str = "AND ngs_fastq_files.dir_id = (SELECT ngs_dirs.id FROM ngs_dirs WHERE ngs_fastq_files.dir_id = ngs_dirs.id AND (ngs_dirs.amazon_bucket LIKE '%s3://%'))";
      	$sampleBackup = "CASE
      						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE aws_status = 2 AND ngs_samples.id = ngs_fastq_files.sample_id) > 0 THEN '<td><button class=\"btn btn-warning\" disabled></td>'
      						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE checksum != original_checksum AND (original_checksum != '' AND original_checksum IS NOT NULL) AND ngs_samples.id = ngs_fastq_files.sample_id) > 0 THEN '<td><button class=\"btn btn-flickr\" disabled></td>'
      						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE checksum != backup_checksum AND (backup_checksum != '' AND backup_checksum IS NOT NULL) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<td><button class=\"btn btn-danger\" disabled></td>'
      						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE (backup_checksum = '' OR backup_checksum IS NULL) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<td><button class=\"btn\" disabled></td>'
      						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE date_modified < DATE_SUB(now(), INTERVAL 2 MONTH) AND ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) > 0 THEN '<td><button class=\"btn btn-primary\" disabled></td>'
      						WHEN (SELECT COUNT(*) FROM ngs_fastq_files WHERE ngs_samples.id = ngs_fastq_files.sample_id $amazon_str) = 0 THEN '<td></td>'
      						ELSE '<td><button class=\"btn btn-success\" disabled></td>'
      					END AS backup";


        $result = $this->query("SELECT ngs_samples.name AS sample,
            ngs_samples.id AS sample_id,
            ngs_lanes.name AS lane, ngs_experiment_series.experiment_name
            AS experiment, ngs_fastq_files.file_name, ngs_dirs.backup_dir,
            ngs_dirs.amazon_bucket
          FROM ngs_samples
          LEFT JOIN ngs_lanes ON ngs_samples.lane_id = ngs_lanes.id
          LEFT JOIN ngs_experiment_series
            ON ngs_samples.series_id = ngs_experiment_series.id
          LEFT JOIN ngs_fastq_files ON ngs_samples.id = ngs_fastq_files.sample_id
          LEFT JOIN ngs_dirs ON ngs_dirs.id = ngs_fastq_files.dir_id
          WHERE ngs_dirs.id=ngs_fastq_files.dir_id and ngs_dirs.amazon_bucket!=''
          and ngs_dirs.amazon_bucket != 'none'
          ");

        return $result;
      }
}
