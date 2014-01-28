<?php
	error_log('DENTRO');
	require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
	require_once(dirname(__FILE__).'/lib.php');

	$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
	$action = htmlspecialchars($_GET["action"]);
	error_log('id '.$id);
	error_log('action '.$action);
	// $n  = optional_param('n', 0, PARAM_INT);  // hydra instance ID - it should be named as the first character of the module

	if ($id) {
	    $cm         = get_coursemodule_from_id('hydra', $id, 0, false, MUST_EXIST);
	    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	    $hydra  = $DB->get_record('hydra', array('id' => $cm->instance), '*', MUST_EXIST);
	} elseif ($n) {
	    $hydra  = $DB->get_record('hydra', array('id' => $n), '*', MUST_EXIST);
	    $course     = $DB->get_record('course', array('id' => $hydra->course), '*', MUST_EXIST);
	    $cm         = get_coursemodule_from_instance('hydra', $hydra->id, $course->id, false, MUST_EXIST);
	} else {
	    error('You must specify a course_module ID or an instance ID');
	}

	if ($action) {
		error_log('Action');
		add_to_log($course->id, 'hydra', $action, "view.php?id=$cm->id", $hydra->name, $cm->id);
	}


?>