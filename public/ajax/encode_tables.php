<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$query = new dbfuncs();

if (isset($_GET['p'])){$p = $_GET['p'];}
if (isset($_POST['p'])){$p = $_POST['p'];}

$data = '';

if ($p == 'getConditionData')
{
	$data=$query->queryTable("
	  SELECT * FROM `ngs_conds` ORDER BY id DESC
	");
}

if ($p == 'getSubmissions')
{
	$data=$query->queryTable("
		SELECT ngs_samples.id, sub_status, output_file, ngs_samples.samplename
		FROM encode_submissions
		LEFT JOIN ngs_samples
		ON encode_submissions.sample_id = ngs_samples.id
	");
}
else if ($p == 'getConditionsForSample')
{
	if (isset($_GET['sample_id'])){$sample_id = $_GET['sample_id'];}
	$data=$query->queryTable("
		SELECT sample_id, ngs_conds.condition, ngs_samples.samplename, cond_symbol,
		ngs_sample_conds.concentration, ngs_sample_conds.duration, ngs_conds.id
		AS cond_id, ngs_sample_conds.concentration_unit,
		ngs_sample_conds.duration_unit
		FROM ngs_sample_conds
		LEFT JOIN ngs_conds
		ON ngs_conds.id = cond_id
		LEFT JOIN ngs_samples
		ON ngs_samples.id = ngs_sample_conds.sample_id
		WHERE sample_id=$sample_id
	");
}
else if ($p == 'getConditionsDetailsWithID')
{
	if (isset($_GET['new_cond_id'])){$new_cond_id = $_GET['new_cond_id'];}
	$data=$query->queryTable("
    SELECT * FROM ngs_conds WHERE id=$new_cond_id
	");
}
else if ($p == 'addOrUpdateCondSample')
{
	if (isset($_POST['sample_id'])){$sample_id = $_POST['sample_id'];}
	if (isset($_POST['new_cond_id'])){$new_cond_id = $_POST['new_cond_id'];}
	if (isset($_POST['concentration'])){$concentration = $_POST['concentration'];}
	if (isset($_POST['duration'])){$duration = $_POST['duration'];}
	if (isset($_POST['concentration_unit'])){$concentration_unit = $_POST['concentration_unit'];}
	if (isset($_POST['duration_unit'])){$duration_unit = $_POST['duration_unit'];}

  $query_str = "
		INSERT INTO ngs_sample_conds (sample_id, cond_id, concentration, duration, concentration_unit, duration_unit)
		VALUES ($sample_id, $new_cond_id, $concentration, $duration, '" . $concentration_unit ."', '" . $duration_unit ."')
		ON DUPLICATE KEY
		    UPDATE concentration=\"$concentration\", duration=\"$duration\", concentration_unit='" . $concentration_unit ."', duration_unit='" . $duration_unit ."';
	";


	$data=$query->queryTable($query_str);
}

else if ($p == 'removeCondSample')
{
	if (isset($_POST['sample_id'])){$sample_id = $_POST['sample_id'];}
	if (isset($_POST['cond_id'])){$cond_id = $_POST['cond_id'];}

  $query_str = "
	DELETE FROM ngs_sample_conds WHERE sample_id=$sample_id AND cond_id=$cond_id
	";

	$data=$query->queryTable($query_str);
}

else if ($p == 'createTreatmentWithSelection')
{
	if (isset($_POST['new_treatment_name'])){$new_treatment_name = $_POST['new_treatment_name'];}
	if (isset($_POST['new_treatment_symbol'])){$new_treatment_symbol = $_POST['new_treatment_symbol'];}

  $query_str = "
    INSERT INTO `ngs_conds`(`cond_symbol`, `condition`) VALUES ('$new_treatment_symbol', '$new_treatment_name')
    ";

	$data=$query->queryTable($query_str);
}


else if ($p == 'getBatchSubmissions')
{
	$data=$query->queryTable("
		SELECT encode_batch_submissions.id, encode_batch_submissions.samples, encode_batch_submissions.output_file,
		CASE
			WHEN (SELECT COUNT(encode_submissions.id) FROM encode_submissions LEFT JOIN encode_batch_submissions ON encode_batch_submissions.id = encode_submissions.batch_submission
				  WHERE encode_submissions.batch_submission = encode_batch_submissions.id AND encode_submissions.sub_status = 2) > 0 THEN 2
			ELSE 1
		END AS sub_status
		FROM encode_batch_submissions
	");
}
else if ($p == 'getSamples')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT ngs_samples.id AS sample_id, ngs_samples.samplename, ngs_samples.source_id,
		ngs_samples.organism_id, ngs_samples.molecule_id, source, organism, molecule,
		ngs_samples.donor_id, ngs_donor.donor, ngs_experiment_series.id as e_id,
		ngs_experiment_series.`grant`, ngs_experiment_series.lab_id, ngs_lab.lab,
		ngs_biosample_acc.biosample_acc, ngs_experiment_acc.experiment_acc
		FROM ngs_samples
		LEFT JOIN ngs_donor
		ON ngs_samples.donor_id = ngs_donor.id
		LEFT JOIN ngs_experiment_series
		ON ngs_samples.series_id = ngs_experiment_series.id
		LEFT JOIN ngs_lab
		ON ngs_experiment_series.lab_id = ngs_lab.id
		LEFT JOIN ngs_source
		ON ngs_samples.source_id = ngs_source.id
		LEFT JOIN ngs_organism
		ON ngs_samples.organism_id = ngs_organism.id
		LEFT JOIN ngs_molecule
		ON ngs_samples.molecule_id = ngs_molecule.id
		LEFT JOIN ngs_biosample_acc
		ON ngs_samples.biosample_acc = ngs_biosample_acc.id
		LEFT JOIN ngs_experiment_acc
		ON ngs_samples.experiment_acc = ngs_experiment_acc.id
		WHERE ngs_samples.id IN ($samples);
	");
}
else if($p == 'getDonors')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT ngs_donor.id as donor_id, ngs_donor.donor,
		ngs_donor.life_stage, ngs_donor.age,
		ngs_donor.sex, ngs_donor.donor_acc, ngs_donor.donor_uuid
		FROM ngs_donor
		WHERE ngs_donor.id
		IN (
			SELECT donor_id
			FROM ngs_samples
			WHERE ngs_samples.id IN ($samples)
		)
		");
}
else if($p == 'getExperiments')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT DISTINCT ngs_samples.id as sample_id, ngs_samples.samplename, ngs_protocols.id as protocol_id,
		ngs_samples.description, ngs_experiment_acc.experiment_acc, ngs_samples.experiment_uuid,
		ngs_library_strategy.id as library_strategy_id, ngs_library_strategy.library_strategy,
		ngs_samples.source_id as source_id, ngs_source.source, ngs_protocols.assay_term_id AS assay_id,
		assay_term_name, ngs_assay_term.assay_term_id
		FROM ngs_samples
		LEFT JOIN ngs_protocols
		ON ngs_protocols.id = ngs_samples.protocol_id
		LEFT JOIN ngs_library_strategy
		ON ngs_library_strategy.id = ngs_protocols.library_strategy_id
		LEFT JOIN ngs_source
		ON ngs_source.id = ngs_samples.source_id
		LEFT JOIN ngs_assay_term
		ON ngs_assay_term.id = ngs_protocols.assay_term_id
		LEFT JOIN ngs_experiment_acc
		ON ngs_samples.experiment_acc = ngs_experiment_acc.id
		WHERE ngs_samples.id in ($samples)
		");
}
else if($p == 'getTreatments')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT *
		FROM ngs_treatment
		WHERE id in (
			SELECT treatment_id
			FROM ngs_samples
			WHERE id in ($samples)
		)
		");
}
else if($p == 'getBiosamples')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT ngs_samples.id as sample_id, ngs_samples.samplename, ngs_biosample_term.biosample_term_name, ngs_biosample_term.biosample_term_id,
		ngs_biosample_term.id as biosample_id, ngs_lanes.id as lane_id, ngs_biosample_term.biosample_type, ngs_lanes.date_received,
		ngs_treatment.id as treatment_id, ngs_lanes.date_submitted, ngs_biosample_acc.biosample_acc, ngs_samples.biosample_uuid, ngs_treatment.name,
		biosample_derived_from, starting_amount, starting_amount_units, ngs_protocols.id as protocol_id, ngs_protocols.starting_amount_id, source,
		GROUP_CONCAT(ngs_conds.cond_symbol SEPARATOR ', ') as treatment_list,
		GROUP_CONCAT(
		  DISTINCT CONCAT(ngs_sample_conds.concentration,' ',
		  ngs_sample_conds.concentration_unit,' ',
		  ngs_sample_conds.duration,' ',
		  ngs_sample_conds.duration_unit
		  )
		  SEPARATOR ', '
		) as concentration_list
		FROM ngs_samples
		LEFT JOIN ngs_biosample_term
		ON ngs_samples.biosample_id = ngs_biosample_term.id
		LEFT JOIN ngs_treatment
		ON ngs_samples.treatment_id = ngs_treatment.id
		LEFT JOIN ngs_lanes
		ON ngs_samples.lane_id = ngs_lanes.id
		LEFT JOIN ngs_protocols
		ON ngs_samples.protocol_id = ngs_protocols.id
		LEFT JOIN ngs_starting_amount
		ON ngs_protocols.starting_amount_id = ngs_starting_amount.id
		LEFT JOIN ngs_biosample_acc
		ON ngs_samples.biosample_acc = ngs_biosample_acc.id
		LEFT JOIN ngs_source
		ON ngs_source.id = ngs_samples.source_id
		LEFT JOIN ngs_sample_conds
		ON ngs_sample_conds.sample_id = ngs_samples.id
		LEFT JOIN ngs_conds
		ON ngs_sample_conds.cond_id = ngs_conds.id
		WHERE ngs_samples.id in ($samples)
		GROUP BY ngs_samples.id
		");
}
else if($p == 'getLibraries')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT ngs_samples.id as sample_id, ngs_samples.samplename, ngs_molecule.molecule, ngs_protocols.extraction, ngs_samples.avg_insert_size,
		ngs_molecule.id as molecule_id, ngs_protocols.id as protocol_id, ngs_samples.instrument_model_id as imid, instrument_model,
		ngs_samples.spike_ins, ngs_protocols.crosslinking_method, ngs_protocols.fragmentation_method, ngs_samples.library_acc,
		ngs_samples.library_uuid, ngs_samples.flowcell_id, machine_name, flowcell, ngs_flowcell.lane, ngs_samples.read_length,
		nucleic_acid_term_name, ngs_nucleic_acid_term.nucleic_acid_term_id, ngs_protocols.nucleic_acid_term_id as nucleic_acid_id
		FROM ngs_samples
		LEFT JOIN ngs_molecule
		ON ngs_samples.molecule_id = ngs_molecule.id
		LEFT JOIN ngs_instrument_model
		ON ngs_samples.instrument_model_id = ngs_instrument_model.id
		LEFT JOIN ngs_protocols
		ON ngs_samples.protocol_id = ngs_protocols.id
		LEFT JOIN ngs_flowcell
		ON ngs_samples.flowcell_id = ngs_flowcell.id
		LEFT JOIN ngs_nucleic_acid_term
		ON ngs_protocols.nucleic_acid_term_id = ngs_nucleic_acid_term.id
		WHERE ngs_samples.id in ($samples)
		");
}
else if($p == 'getAntibodies')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT *
		FROM ngs_antibody_target
		WHERE id in (
			SELECT antibody_lot_id
			FROM ngs_samples
			WHERE id in ($samples)
		)
		");
}
else if($p == 'getReplicates')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT ngs_samples.id as sample_id, samplename, biological_replica, technical_replica, ngs_antibody_target.target, ngs_antibody_target.id as antibody_id,
		ngs_samples.replicate_uuid
		FROM ngs_samples
		LEFT JOIN ngs_antibody_target
		ON ngs_samples.antibody_lot_id = ngs_antibody_target.id
		where ngs_samples.id in ($samples)
		");
}
else if ($p == 'createEncodeRow')
{
	if (isset($_GET['type'])){$type = $_GET['type'];}
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	if (isset($_GET['name'])){$name = $_GET['name'];}
	if($type == 'Treatment'){
		$table = 'ngs_treatment';
		$update = 'treatment_id';
		$rowname = 'name';
	}else{
		$table = 'ngs_antibody_target';
		$update = 'antibody_lot_id';
		$rowname = 'target';
	}

	$data=$query->runSQL("
		INSERT INTO $table
		($rowname)
		VALUES
		('$name')
		WHERE NOT EXISTS(
			SELECT 1
			FROM $table
			WHERE $rowname = '$name'
		)
	");
	$typeID=json_decode($query->queryTable("
		SELECT id
		FROM $table
		WHERE $rowname = '$name'
		ORDER BY id DESC
	"));
	$data=$query->runSQL("
		UPDATE ngs_samples
		SET $update = " . $typeID[0]->id . "
		WHERE id IN ($samples)
	");
}
else if ($p == 'getFiles')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT ngs_runparams.id, ngs_runparams.outdir, samplename, run_name, json_parameters, ngs_runlist.sample_id, ngs_fastq_files.dir_id
		FROM ngs_runlist
		LEFT JOIN ngs_runparams
		ON ngs_runparams.id = ngs_runlist.run_id
		LEFT JOIN ngs_samples
		ON ngs_samples.id = ngs_runlist.sample_id
		LEFT JOIN ngs_fastq_files
		ON ngs_samples.id = ngs_fastq_files.sample_id
		WHERE ngs_runlist.sample_id IN ($samples)
		AND run_status = 1
		AND run_name NOT LIKE '%Initial Run%'
		");
}
else if ($p == 'getFileSelection')
{
	if (isset($_GET['runs'])){$runs = $_GET['runs'];}
	$data=$query->queryTable("
		SELECT ngs_runparams.id, ngs_runparams.wkey, version, type, file
		FROM ngs_runparams
		LEFT JOIN report_list
		ON ngs_runparams.wkey = report_list.wkey
		WHERE ngs_runparams.id in ($runs)
		");
}
else if ($p == 'getSubmittedFiles')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("
		SELECT ngs_samples.samplename, ngs_runparams.run_name, ngs_runparams.outdir, file_name, file_acc, file_uuid, backup_dir, parent_file
		FROM ngs_file_submissions
		LEFT JOIN ngs_samples
		ON ngs_samples.id = ngs_file_submissions.sample_id
		LEFT JOIN ngs_runparams
		ON ngs_runparams.id = ngs_file_submissions.run_id
		LEFT JOIN ngs_dirs
		ON ngs_file_submissions.dir_id = ngs_dirs.id
		WHERE sample_id in ($samples)
		");
}
else if ($p == 'enterFileSubmission')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	if (isset($_GET['ordertable'])){$ordertable = $_GET['ordertable'];}

	$submissions=json_decode($query->queryTable("
		SELECT ngs_file_submissions.sample_id, ngs_file_submissions.dir_id, ngs_file_submissions.run_id, ngs_file_submissions.parent_file,
		ngs_file_submissions.file_type, ngs_file_submissions.file_name, ngs_samples.samplename
		FROM ngs_file_submissions
		LEFT JOIN ngs_samples
		ON ngs_samples.id = ngs_file_submissions.sample_id
		WHERE sample_id IN (".implode(",",array_keys($samples)).")
		"));
	$samplenames=json_decode($query->queryTable("
		SELECT id, samplename
		FROM ngs_samples
		WHERE id IN (".implode(",",array_keys($samples)).")
		"));
	$fastq_files=json_decode($query->queryTable("
		SELECT *
		FROM ngs_fastq_files
		WHERE sample_id IN (".implode(",",array_keys($samples)).")
	"));

	$insertString = '';
	foreach($ordertable as $step => $subdata){
		foreach($samples as $id => $sample){
			$current_sample_name = "";
			$sampleCheck = true;
			foreach($samplenames as $sn){
				if($sn->id == $id){
					$current_sample_name = $sn->samplename;
				}
			}
			$filename = $subdata['l'];
			$sub_r = "";
			$sub_d = "";
			if($subdata['r'] == "" || $subdata['r'] == "NULL"){
				$sub_r = "NULL";
			}else{
				$sub_r = "'".$subdata['r']."'";
			}
			if($subdata['d'] == "" || $subdata['d'] == "NULL"){
				$sub_d = "NULL";
			}else{
				$sub_d = "'".$subdata['d']."'";
			}

			if($subdata['t'] == 'fastq' && $subdata['p'] == 0){
				$filename = "";
				foreach($fastq_files as $fqf){
					if($fqf->sample_id == $id){
						$filename = $fqf->file_name;
					}
				}
			}else if($subdata['t'] == 'fastq' && $subdata['p'] != 0){
				$file_names = "";
				foreach($fastq_files as $fqf){
					if($fqf->sample_id == $id){
						$file_names = $fqf->file_name;
					}
				}
				if(count(explode(",",$file_names)) == 2){
					$filename = $subdata['l'] . "$current_sample_name.1.fastq," . $subdata['l'] . "$current_sample_name.2.fastq";
				}else{
					$filename = $subdata['l'] . "$current_sample_name.fastq";
				}
			}else if($subdata['t'] == "tdf"){
				if(preg_match("/rsem/", $subdata['l'])){
					if($subdata['l'] == "/rsem/genes"){
						$filename = "/rsem/pipe.rsem.$current_sample_name/rsem.out.$current_sample_name.genes.results";
					}else if($subdata['l'] == "/rsem/isoforms"){
						$filename = "/rsem/pipe.rsem.$current_sample_name/rsem.out.$current_sample_name.isoforms.results";
					}else{
						$filename = $subdata['l'];
					}
				}else{
					$filename = $subdata['l'];
				}
			}else if($subdata['t'] == 'bigWig'){
				if(preg_match("/rsem/", $subdata['l']) && !preg_match("/dedup/", $subdata['l'])){
					$filename = $subdata['l'] . "rsem.out.$current_sample_name.bw";
				}else{
					$filename = $subdata['l'] . "$current_sample_name.sorted.bw";
				}
			}else if($subdata['t'] == 'bam'){
				$dedup = false;
				$merged = false;
				$sorted;
				if(preg_match("/dedup/", $subdata['l'])){
					$dedup = true;
				}
				if(preg_match("/merge/", $subdata['l'])){
					$merge = true;
				}
				if($dedup){
					$filename = $subdata['l'] . "$current_sample_name.bam";
				}else if(preg_match("/rsem/", $subdata['l'])){
					$filename = $subdata['l'] . "pipe.rsem.$current_sample_name/rsem.out.$current_sample_name.transcript.bam";
				}else if(preg_match("/tophat/", $subdata['l'])){
					if(!$dedup && !$merge){
						$filename = $subdata['l'] . "pipe.tophat.$current_sample_name/$current_sample_name.bam";
					}
				}else if(preg_match("/chip/", $subdata['l']) || preg_match("/atac/", $subdata['l'])){
					if(!$dedup && !$merge){
						$filename = $subdata['l'] . "$current_sample_name.sorted.bam";
					}
				}else if(preg_match("/hisat2/", $subdata['l'])){
					if(!$dedup && !$merge){
						$filename = $subdata['l'] . "pipe.hisat2.$current_sample_name/$current_sample_name.bam";
					}
				}else if(preg_match("/star/", $subdata['l'])){
					if(!$dedup && !$merge){
						$filename = $subdata['l'] . "pipe.star.$current_sample_name/$current_sample_name.bam";
					}
				}
			}else if($subdata['t'] == 'peaks-bed'){
				$filename = $subdata['l'] . $current_sample_name . "_peaks.narrowPeak";
			}
			foreach($submissions as $nfs){
				if($nfs->dir_id == $sample['did'] && $nfs->run_id == $sample['rid'] && $nfs->sample_id == $id &&
				   $nfs->parent_file == $subdata['p'] && $nfs->file_type == $subdata['t'] && $nfs->file_name == $filename){
					$sampleCheck = false;
				}
			}
			if($sampleCheck){
				$insertString.="(".$sample['did'].", ".$sample['rid'].", ".$id.", '".$filename."', '".$subdata['t']."', '".$subdata['p']."', ".$sub_r.", ".$sub_d."),";
			}
		}
	}
	$data = json_encode("no insertion");
	if($insertString != ''){
		$insertString=substr($insertString, 0, -1);
		$insert=$query->queryTable("
			INSERT INTO ngs_file_submissions
			(dir_id, run_id, sample_id, file_name, file_type, parent_file, step_run, additional_derived_from)
			VALUES
			$insertString;
			");
		$data = json_encode("insert occured");
	}
}
else if ($p == 'linkSamples')
{
	if (isset($_GET['type'])){$type = $_GET['type'];}
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	if (isset($_GET['acc'])){$acc = $_GET['acc'];}
	if($acc == 'none' || $acc == ''){
		$data=json_decode($query->runSQL("
		INSERT INTO ngs_" . strtolower($type) . "_acc
		(" . strtolower($type) . "_acc) VALUES ('insert')
		"));
		$accid=json_decode($query->queryTable("
		SELECT *
		FROM ngs_" . strtolower($type) . "_acc
		WHERE " . strtolower($type) . "_acc = 'insert'
		"));
		$data=json_decode($query->runSQL("
		UPDATE ngs_samples
		SET ". strtolower($type) . "_acc = ".$accid[0]->id."
		WHERE id in ($samples)
		"));
		$data=json_decode($query->runSQL("
		UPDATE ngs_" . strtolower($type) . "_acc
		SET ". strtolower($type) . "_acc = NULL
		WHERE id = ".$accid[0]->id
		));
	}else{
		$accid=json_decode($query->queryTable("
		SELECT *
		FROM ngs_".strtolower($type)."_acc
		WHERE ".strtolower($type)."_acc = '$acc'
		"));
		$data=json_decode($query->runSQL("
		UPDATE ngs_samples
		SET ". strtolower($type) . "_acc = ".$accid[0]->id."
		WHERE id in ($samples)
		"));
	}
}
else if ($p == 'viewLog')
{
	if (isset($_GET['log'])){$log = $_GET['log'];}
	$data=json_encode(file_get_contents("../../tmp/encode/$log"));
}


header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo $data;
exit;
?>
