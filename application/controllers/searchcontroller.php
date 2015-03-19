<?php
 
class SearchController extends VanillaController {

    function beforeAction() {

    }
 
    function index() {
        $jsData['theSegment'] = 'index';
        
        $this->set('field', "Search");
        $this->set('assay', $this->Search->getAccItems("library_type", "ngs_samples"));
        $this->set('organism', $this->Search->getAccItems("organism", "ngs_samples"));
        $this->set('molecule', $this->Search->getAccItems("molecule", "ngs_samples"));
        $this->set('source', $this->Search->getAccItems("source", "ngs_samples"));
        $this->set('genotype', $this->Search->getAccItems("genotype", "ngs_samples"));
        if (!empty($jsData)) {
            echo "<script type='text/javascript'>\n";
            echo "var phpGrab = " . json_encode($jsData) . "\n";
            echo "</script>\n";
        }
    }
    
    function browse($field, $value) {
        $jsData['theSegment'] = 'browse';
        $jsData['theField'] = $field;
        $jsData['theValue'] = $value;
        
        $this->index();
        $this->set('field', $field);
        $this->set('value', $value);
        if (!empty($jsData)) {
            echo "<script type='text/javascript'>\n";
            echo "var phpGrab = " . json_encode($jsData) . "\n";
            echo "</script>\n";
        }
    }
    function details($table, $value) {
        $jsData['theSegment'] = 'details';
        $jsData['theField'] = $table;
        $jsData['theValue'] = $value;
        
        $this->index();
        $this->set('table', $table);
        if ($table=='experiment_series')
        {
           $this->set('experiment_series', $this->Search->getValues($value, 'ngs_experiment_series'));
           $this->set('experiment_series_fields', $this->Search->getFields('ngs_experiment_series'));
        }
        if ($table=='experiments')
        {
           $this->set('experiment_series_fields', $this->Search->getFields('ngs_experiment_series'));
           $this->set('experiment_fields', $this->Search->getFields('ngs_lanes'));
           
           $this->set('experiment_series', $this->Search->getValues($this->Search->getId($value, 'series_id', 'ngs_lanes'), 'ngs_experiment_series'));
           $this->set('experiments', $this->Search->getValues($value, 'ngs_lanes'));
        }
        if ($table=='samples')
        {
           $this->set('experiment_series_fields', $this->Search->getFields('ngs_experiment_series'));
           $this->set('experiment_fields', $this->Search->getFields('ngs_lanes'));
           $this->set('sample_fields', $this->Search->getFields('ngs_samples'));
           
           $this->set('experiment_series', $this->Search->getValues($this->Search->getId($value, 'series_id', 'ngs_samples'), 'ngs_experiment_series'));
           $this->set('experiments', $this->Search->getValues($this->Search->getId($value, 'lane_id', 'ngs_samples'), 'ngs_lanes'));
           $this->set('samples', $this->Search->getValues($value, 'ngs_samples'));
        }
        if (!empty($jsData)) {
            echo "<script type='text/javascript'>\n";
            echo "var phpGrab = " . json_encode($jsData) . "\n";
            echo "</script>\n";
        }
    }


    function afterAction() {

    }

}
