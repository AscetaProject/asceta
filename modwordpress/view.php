<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Prints a particular instance of modredmine
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_modredmine
 * @copyright 2011 Vicente Manuel García Huete (vmgarcia@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/// (Replace modredmine with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // modredmine instance ID - it should be named as the first character of the module
$option = optional_param('option', '', PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);



$context = get_context_instance(CONTEXT_MODULE, $id);
if ($id) {
  $cm = get_coursemodule_from_id('modredmine', $id, 0, false, MUST_EXIST);
  $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
  $modredmine = $DB->get_record('modredmine', array('id' => $cm->instance), '*', MUST_EXIST);
  // TODO crear un usuario admin por defecto en la base de datos
  if (!has_capability('mod/modredmine:admin', $context)) {
    $modredmine_user = $DB->get_record('modredmine_users', array('moodle_id' => $USER->id), '*', MUST_EXIST);
  }
} elseif ($n) {
  $modredmine = $DB->get_record('modredmine', array('id' => $n), '*', MUST_EXIST);
  $course = $DB->get_record('course', array('id' => $modredmine->course), '*', MUST_EXIST);
  $cm = get_coursemodule_from_instance('modredmine', $modredmine->id, $course->id, false, MUST_EXIST);
  // TODO crear un usuario admin por defecto en la base de datos
  if (!has_capability('mod/modredmine:admin', $context)) {
    $modredmine_user = $DB->get_record('modredmine_users', array('moodle_id' => $USER->id), '*', MUST_EXIST);
  }
} else {
  error('You must specify a course_module ID or an instance ID');
}

if ($modredmine->server_id) {
  $server = $DB->get_record('modredmine_servers', array('id' => $modredmine->server_id), '*', MUST_EXIST);
  //$mdl_users = $DB->get_records_sql('SELECT moodle_id, redmine_id, username, firstname from {modredmine_users} mu, {user} u WHERE u.id = mu.moodle_id and mu.server_id=?', array($modredmine->server_id));
  //$wp_users = $DB->get_records_sql('SELECT redmine_id, moodle_id, username, firstname from {modredmine_users} mu, {user} u WHERE u.id = mu.moodle_id and mu.server_id=?', array($modredmine->server_id));
}


require_login($course, true, $cm);

//add_to_log($course->id, 'modredmine', 'view', "view.php?id=$cm->id", $modredmine->name, $cm->id);
/// Print the page header

$PAGE->set_url('/mod/modredmine/view.php', array('id' => $cm->id));
$PAGE->set_title($modredmine->name);
$PAGE->set_heading($course->shortname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'modredmine')));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');

// Output starts here
echo $OUTPUT->header();

// Replace the following lines with you own code
if (!$modredmine->server_id) {
  echo $OUTPUT->heading(get_string("configure_server_url", "modredmine"));
} else {
  global $USER;
  $server_id = $modredmine->server_id;
  $server = $DB->get_record_select("modredmine_servers", "id=$server_id");


  //-----vvv Getting Redmine Server CSS vvv----------------------
  $url = rtrim($server->url, '/');
  $url = rtrim($url, '/api');
  $url = rtrim($url, '/API');
  $url = rtrim($url, '/API');
  $url .= "/";
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  $ch = $curl;
  curl_setopt($ch, CURLOPT_FAILONERROR, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  $html = curl_exec($ch);
  $dom = new DOMDocument();
  @$dom->loadHTML($html);
  $xpath = new DOMXPath($dom);
  $hrefs = $xpath->evaluate("/html/head/child::link[attribute::type='text/css'][attribute::rel='stylesheet']");
  $css = '';
  if ($hrefs->length) {

    foreach ($hrefs as $href) {
      $css = $href->getAttribute('href');
      if ($css != '') {
        echo "<link rel='stylesheet' href='$css' />";
      }
    }
  } else {
    echo "<link rel='stylesheet' href='style.css' />";
  }
  //-----^^^ Getting Redmine Server CSS ^^^----------------------

  // TODO si el usuario es root se usa API KEY, si no lo es se usa username:password
  $api_key = null;
  if ($server->auth && has_capability('mod/modredmine:admin', $context)) {
    $api_key = $server->api_key;
  }
  // Si el servidor requiere autenticacion y no tiene configurada api key
  // se busca en los parametros del usuario (redmine_login y redmine_password)
  // TODO gestionar los parametros redmine_login y redmine_password para cada usuario
  if (strlen($api_key)) {
    $api_key .= ':@';
  } else {
    $api_key = $modredmine_user->redmine_login.":".$modredmine_user->redmine_password."@";
  }
  $server_url = rtrim($server->url, '/');
  $server_url = str_replace('http://', '', $server_url);
  // TODO si el usuario es root se usa API KEY, si no lo es se usa username:password
  $server_url = "http://$api_key$server_url/";


  //var_dump($timees);
  //    var_dump($bugs);
  //    var_dump($bugs[0]->project);
  //    var_dump($bugs[0]->status);
  //    var_dump($bugs[0]->tracker);
  //    var_dump($bugs[0]->priority);
  //    var_dump($bugs[0]->author);
  //    var_dump($bugs[0]->due_date);
  //    var_dump($bugs[0]->estimated_hours);

  //    var_dump($news);



  // ----------- vvv RESUMEN vvv ------------------------------------------------------
  echo "<div id='wrapper'>";
  echo "	<div id='wrapper2'>";
  if (!isset($option) || $option == '') {
    $issues = new RedmineIssue();
    $issues->setSite($server_url);
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".BUG;
    $bugs = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".BUG."&status_id=".OPEN;
    $open_bugs = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".FEATURE;
    $features = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".FEATURE."&status_id=".OPEN;
    $open_features = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".SUPPORT;
    $supports = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".SUPPORT."&status_id=".OPEN;
    $open_supports = $issues->find('all');
    $issues->extra_params = "";
    $time_entries = new RedmineTimeEntry();
    $time_entries->setSite($server_url);
    $time_entries->extra_params = "?project_id=".$modredmine->project_identifier.
    $timees = $time_entries->find('all');
    $hours = 0;
    foreach ($timees as $time) {
      if (isset($time->hours))
        $hours += $time->hours;
    }
    $project_news = new RedmineNews();
    $project_news->setSite($server_url);
    $project_news->extra_params = "?project_id=".$modredmine->project_identifier.
    $news = $project_news->find('all');

    echo $OUTPUT->heading($server->name);
    echo "<div id='header'>";
    echo "  <h1>$modredmine->project_name</h1>";
    echo "  <div id='main-menu' style='margin-top: 12px; position: relative;'>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;sesskey=".sesskey()."' class='overview selected'>Resumen</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Peticiones</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=".sesskey()."' class='news'>Noticias</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Tiempo Dedicado</a></li>";
    echo "  </div>";
    echo "</div>";
    echo "<div class='main'>";
    echo "	<div id='sidebar'>";
    echo "	    <h3>Tiempo dedicado</h3>";
    echo "	    <p><span class='icon icon-time'>$hours horas</span></p>";
    echo "	    <p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Detalles</a></p>";
    echo "	</div>";
    echo "	<div id='content'>";
    if ($issues->errno == NULL) {
      echo "	    <div id='splitcontentleft'>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Peticiones</h3>";
      echo "		<ul>";
      echo "		    <li>Errores: ".count($open_bugs)."     abiertos / ".count($bugs)."    </li>";
      echo "		    <li>Tareas:  ".count($open_features)." abiertas / ".count($features)."</li>";
      echo "		    <li>Soporte: ".count($open_supports)."  abiertas / ".count($supports)." </li>";
      echo "		</ul>";
      echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=" . sesskey() . "'>Ver todas las peticiones</a></p>";
      echo "	        </div>";
      echo "	        <div class='news box'>";
      echo "		<h3>Últimas Noticias</h3>";
      echo "		<ul>";
      for ($x=0; ($x<count($news) && $x<MAX_SUMMARY_NEWS); $x++) {
        if (count($news)) {
          echo "		    <li> <h5>".$news[$x]->title."</h5> - ".$news[$x]->summary." </li>";
        }
      }
      echo "		</ul>";
      echo "		<p><a href=''>Ver todas las noticias</a></p>";
      echo "	        </div>";
      echo "	    </div>";
      echo "	    <div id='splitcontentright'>";
      echo "	    </div>";
      echo "	    <div style='clear:both;'></div>";
    } else {
      echo $issues->error;
    }
    echo "	</div>";
    echo "</div>";
    // ----------- ^^^ RESUMEN ^^^ ------------------------------------------------------





    // ----------- vvv ISSUES vvv ------------------------------------------------------
  } elseif ($option == 'issues') {
    $issues = new RedmineIssue();
    $issues->setSite($server_url);
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".BUG;
    $bugs = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".BUG."&status_id=".OPEN;
    $open_bugs = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".FEATURE;
    $features = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".FEATURE."&status_id=".OPEN;
    $open_features = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".SUPPORT;
    $supports = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".SUPPORT."&status_id=".OPEN;
    $open_supports = $issues->find('all');
    $issues->extra_params = "";
    $time_entries = new RedmineTimeEntry();
    $time_entries->setSite($server_url);
    $time_entries->extra_params = "?project_id=".$modredmine->project_identifier.
    $timees = $time_entries->find('all');
    $hours = 0;
    foreach ($timees as $time) {
      $hours += $time->hours;
    }

    echo $OUTPUT->heading($server->name);
    echo "<div id='header'>";
    echo "  <h1>$modredmine->project_name</h1>";
    echo "  <div id='main-menu' style='margin-top: 12px; position: relative;'>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;sesskey=".sesskey()."' >Resumen</a></li>";
    echo "      <ul><li style='display:inline;'><a class='overview selected' href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Peticiones</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=".sesskey()."' class='news'>Noticias</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Tiempo Dedicado</a></li>";
    echo "  </div>";
    echo "</div>";
    echo "<div class='main'>";
    echo "	<div id='sidebar'>";
    echo "	    <h3>Tiempo dedicado</h3>";
    echo "	    <p><span class='icon icon-time'>$hours horas</span></p>";
    echo "	    <p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Detalles</a></p>";
    echo "	</div>";
    echo "	<div id='content'>";

    if (!$issues->errno) {
      echo "	    <div id='splitcontentleft'>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Errores Abiertos</h3>";
      echo "		<ul>";
      for ($x=0; ($x<count($open_bugs) && ($x<MAX_SUMMARY_ISSUES)); $x++) {
        echo "		    <li>".$open_bugs[$x]->subject." [".$open_bugs[$x]->priority['name']."] - Starts on ".$open_bugs[$x]->start_date." - Created on ". $open_bugs[$x]->created_on . " by ".$open_bugs[$x]->author['name']."</li>";
      }
      echo "		</ul>";
      echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=bugs&amp;sesskey=" . sesskey() . "'>Ver todos los errores</a></p>";
      echo "	        </div>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Tareas Abiertas</h3>";
      echo "		<ul>";
      for ($x=0; ($x<count($open_features) && ($x<MAX_SUMMARY_ISSUES)); $x++) {
        echo "		    <li>".$open_features[$x]->subject." [".$open_features[$x]->priority['name']."] - Starts on ".$open_features[$x]->start_date." - Created on ". $open_features[$x]->created_on . " by ".$open_features[$x]->author['name']."</li>";
      }
      echo "		</ul>";
      echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=features&amp;sesskey=" . sesskey() . "'>Ver todas las tareas</a></p>";
      echo "	        </div>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Tareas de Soporte Abiertas</h3>";
      echo "		<ul>";
      for ($x=0; ($x<count($open_supports) && ($x<MAX_SUMMARY_ISSUES)); $x++) {
        echo "		    <li>".$open_supports[$x]->subject." [".$open_supports[$x]->priority['name']."] - Starts on ".$open_supports[$x]->start_date." - Created on ". $open_supports[$x]->created_on . " by ".$open_supports[$x]->author['name']."</li>";
      }
      echo "		</ul>";
      echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=supports&amp;sesskey=" . sesskey() . "'>Ver todas las tareas de soporte</a></p>";
      echo "	        </div>";
      echo "	    </div>";
      echo "	    <div id='splitcontentright'>";
      echo "	    </div>";
      echo "	    <div style='clear:both;'></div>";
    } else {
      echo $issues->error;
    }
    echo "	</div>";
    echo "</div>";
    // ----------- ^^^ ISSUES ^^^ ------------------------------------------------------




    // ----------- vvv NEWS vvv ------------------------------------------------------
  } elseif ($option == 'news') {
    $time_entries = new RedmineTimeEntry();
    $time_entries->setSite($server_url);
    $time_entries->extra_params = "?project_id=".$modredmine->project_identifier."&limit=".MAX_NEWS."&offset=".$page;
    $timees = $time_entries->find('all');
    $hours = 0;
    foreach ($timees as $time) {
      $hours += $time->hours;
    }
    $project_news = new RedmineNews();
    $project_news->setSite($server_url);
    $project_news->extra_params = "?project_id=".$modredmine->project_identifier.
    $news = $project_news->find('all');

    echo $OUTPUT->heading($server->name);
    echo "<div id='header'>";
    echo "  <h1>$modredmine->project_name</h1>";
    echo "  <div id='main-menu' style='margin-top: 12px; position: relative;'>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;sesskey=".sesskey()."' >Resumen</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Peticiones</a></li>";
    echo "      <ul><li style='display:inline;'><a class='overview selected' href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=".sesskey()."' class='news'>Noticias</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Tiempo Dedicado</a></li>";
    echo "  </div>";
    echo "</div>";
    echo "<div class='main'>";
    echo "	<div id='sidebar'>";
    echo "	    <h3>Tiempo dedicado</h3>";
    echo "	    <p><span class='icon icon-time'>$hours horas</span></p>";
    echo "	    <p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Detalles</a></p>";
    echo "	</div>";
    echo "	<div id='content'>";

    if (!$project_news->errno) {
      echo "	    <div id='splitcontentleft'>";
      echo "	        <div class='news box'>";
      echo "		<h3>Todas las noticias</h3>";
      echo "		<ul>";
      foreach ($news as $new) {
        echo "		    <li><b>$new->title:</b> $new->summary ( by ".$new->author['name']." )</li>";
      }
      echo "		</ul>";
      echo "	        </div>";
      echo "	    </div>";
      echo "	    <div id='splitcontentright'>";
      echo "	    </div>";
      echo "	    <div style='clear:both;'>";
      if ($page > 0) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=" . sesskey() . "&amp;page=".($page-1)."'>Anteriores | </a>";
      }
      if (count($news)) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=" . sesskey() . "&amp;page=".($page+1)."'> Siguientes</a>";
      }
      echo "	    </div>";
    } else {
      echo $issues->error;
    }
    echo "	</div>";
    echo "</div>";
    // ----------- ^^^ NEWS ^^^ ------------------------------------------------------




    // ----------- vvv BUGS vvv ------------------------------------------------------
  } elseif ($option == 'bugs') {
    $issues = new RedmineIssue();
    $issues->setSite($server_url);
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".BUG."&status_id=".OPEN."&limit=".MAX_ISSUES."&offset=".$page;
    $open_bugs = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".BUG."&status_id=".CLOSED."&limit=".MAX_ISSUES."&offset=".$page;
    $closed_bugs = $issues->find('all');
    $time_entries = new RedmineTimeEntry();
    $time_entries->setSite($server_url);
    $time_entries->extra_params = "?project_id=".$modredmine->project_identifier.
    $timees = $time_entries->find('all');
    $hours = 0;
    foreach ($timees as $time) {
      $hours += $time->hours;
    }

    echo $OUTPUT->heading($server->name);
    echo "<div id='header'>";
    echo "  <h1>$modredmine->project_name</h1>";
    echo "  <div id='main-menu' style='margin-top: 12px; position: relative;'>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;sesskey=".sesskey()."' >Resumen</a></li>";
    echo "      <ul><li style='display:inline;'><a class='overview selected' href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Peticiones</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=".sesskey()."' class='news'>Noticias</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Tiempo Dedicado</a></li>";
    echo "  </div>";
    echo "</div>";
    echo "<div class='main'>";
    echo "	<div id='sidebar'>";
    echo "	    <h3>Tiempo dedicado</h3>";
    echo "	    <p><span class='icon icon-time'>$hours horas</span></p>";
    echo "	    <p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Detalles</a></p>";
    echo "	</div>";
    echo "	<div id='content'>";

    if (!$issues->errno) {
      echo "	    <div id='splitcontentleft'>";
      echo "      <a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Volver a Peticiones</a>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Errores Abiertos</h3>";
      echo "		<ul>";
      foreach ($open_bugs as $bug) {
        echo "		    <li>".$bug->subject." [".$bug->priority['name']."] - Starts on ".$bug->start_date." - Created on ". $bug->created_on . " by ".$bug->author['name']."</li>";
      }
      echo "		</ul>";
      //echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=bugs&amp;sesskey=" . sesskey() . "'>Ver todos los errores</a></p>";
      echo "	        </div>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Errores Cerrados</h3>";
      echo "		<ul>";
      foreach ($closed_bugs as $bug) {
        echo "		    <li>".$bug->subject." [".$bug->priority['name']."] - Starts on ".$bug->start_date." - Created on ". $bug->created_on . " by ".$bug->author['name']."</li>";
      }
      echo "		</ul>";
      //echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=features&amp;sesskey=" . sesskey() . "'>Ver todas las tareas</a></p>";
      echo "	        </div>";
      echo "	    </div>";
      echo "	    <div id='splitcontentright'>";
      echo "	    </div>";
      echo "	    <div style='clear:both;'>";
      if ($page > 0) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=bugs&amp;sesskey=" . sesskey() . "&amp;page=".($page-1)."'>Anteriores | </a>";
      }
      if (count($open_bugs) || count($closed_bugs)) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=bugs&amp;sesskey=" . sesskey() . "&amp;page=".($page+1)."'> Siguientes</a>";
      }
      echo "</div>";
    } else {
      echo $issues->error;
    }
    echo "	</div>";
    echo "</div>";
    // ----------- ^^^ BUGS ^^^ ------------------------------------------------------





    // ----------- vvv FEATURES vvv ------------------------------------------------------
  } elseif ($option == 'features') {
    $issues = new RedmineIssue();
    $issues->setSite($server_url);
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".FEATURE."&status_id=".OPEN."&limit=".MAX_ISSUES."&offset=".$page;
    $open_features = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".FEATURE."&status_id=".CLOSED."&limit=".MAX_ISSUES."&offset=".$page;
    $closed_features = $issues->find('all');
    $time_entries = new RedmineTimeEntry();
    $time_entries->setSite($server_url);
    $time_entries->extra_params = "?project_id=".$modredmine->project_identifier.
    $timees = $time_entries->find('all');
    $hours = 0;
    foreach ($timees as $time) {
      $hours += $time->hours;
    }

    echo $OUTPUT->heading($server->name);
    echo "<div id='header'>";
    echo "  <h1>$modredmine->project_name</h1>";
    echo "  <div id='main-menu' style='margin-top: 12px; position: relative;'>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;sesskey=".sesskey()."' >Resumen</a></li>";
    echo "      <ul><li style='display:inline;'><a class='overview selected' href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Peticiones</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=".sesskey()."' class='news'>Noticias</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Tiempo Dedicado</a></li>";
    echo "  </div>";
    echo "</div>";
    echo "<div class='main'>";
    echo "	<div id='sidebar'>";
    echo "	    <h3>Tiempo dedicado</h3>";
    echo "	    <p><span class='icon icon-time'>$hours horas</span></p>";
    echo "	    <p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Detalles</a></p>";
    echo "	</div>";
    echo "	<div id='content'>";

    if (!$issues->errno) {
      echo "	    <div id='splitcontentleft'>";
      echo "      <a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Volver a Peticiones</a>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Tareas Abiertas</h3>";
      echo "		<ul>";
      foreach ($open_features as $feature) {
        echo "		    <li>".$feature->subject." [".$feature->priority['name']."] - Starts on ".$feature->start_date." - Created on ". $feature->created_on . " by ".$feature->author['name']."</li>";
      }
      echo "		</ul>";
      //echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=bugs&amp;sesskey=" . sesskey() . "'>Ver todos los errores</a></p>";
      echo "	        </div>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Tareas Cerradas</h3>";
      echo "		<ul>";
      foreach ($closed_features as $feature) {
        echo "		    <li>".$feature->subject." [".$feature->priority['name']."] - Starts on ".$feature->start_date." - Created on ". $feature->created_on . " by ".$feature->author['name']."</li>";
      }
      echo "		</ul>";
      //echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=features&amp;sesskey=" . sesskey() . "'>Ver todas las tareas</a></p>";
      echo "	        </div>";
      echo "	    </div>";
      echo "	    <div id='splitcontentright'>";
      echo "	    </div>";
      echo "	    <div style='clear:both;'>";
      if ($page > 0) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=features&amp;sesskey=" . sesskey() . "&amp;page=".($page-1)."'>Anteriores | </a>";
      }
      if (count($open_features) || count($closed_features)) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=features&amp;sesskey=" . sesskey() . "&amp;page=".($page+1)."'> Siguientes</a>";
      }
      echo "</div>";
    } else {
      echo $issues->error;
    }
    echo "	</div>";
    echo "</div>";
    // ----------- ^^^ FEATURES ^^^ ------------------------------------------------------






    // ----------- vvv SUPPORT vvv ------------------------------------------------------
  } elseif ($option == 'supports') {
    $issues = new RedmineIssue();
    $issues->setSite($server_url);
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".SUPPORT."&status_id=".OPEN."&limit=".MAX_ISSUES."&offset=".$page;
    $open_supports = $issues->find('all');
    $issues->extra_params = "?project_id=".$modredmine->project_identifier."&tracker_id=".SUPPORT."&status_id=".CLOSED."&limit=".MAX_ISSUES."&offset=".$page;
    $closed_supports = $issues->find('all');
    $time_entries = new RedmineTimeEntry();
    $time_entries->setSite($server_url);
    $time_entries->extra_params = "?project_id=".$modredmine->project_identifier.
    $timees = $time_entries->find('all');
    $hours = 0;
    foreach ($timees as $time) {
      $hours += $time->hours;
    }

    echo $OUTPUT->heading($server->name);
    echo "<div id='header'>";
    echo "  <h1>$modredmine->project_name</h1>";
    echo "  <div id='main-menu' style='margin-top: 12px; position: relative;'>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;sesskey=".sesskey()."' >Resumen</a></li>";
    echo "      <ul><li style='display:inline;'><a class='overview selected' href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Peticiones</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=".sesskey()."' class='news'>Noticias</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Tiempo Dedicado</a></li>";
    echo "  </div>";
    echo "</div>";
    echo "<div class='main'>";
    echo "	<div id='sidebar'>";
    echo "	    <h3>Tiempo dedicado</h3>";
    echo "	    <p><span class='icon icon-time'>$hours horas</span></p>";
    echo "	    <p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Detalles</a></p>";
    echo "	</div>";
    echo "	<div id='content'>";

    if (!$issues->errno) {
      echo "	    <div id='splitcontentleft'>";
      echo "      <a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Volver a Peticiones</a>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Tareas de Soporte Abiertas</h3>";
      echo "		<ul>";
      foreach ($open_supports as $support) {
        echo "		    <li>".$support->subject." [".$support->priority['name']."] - Starts on ".$support->start_date." - Created on ". $support->created_on . " by ".$support->author['name']."</li>";
      }
      echo "		</ul>";
      //echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=bugs&amp;sesskey=" . sesskey() . "'>Ver todos los errores</a></p>";
      echo "	        </div>";
      echo "	        <div class='issues box'>";
      echo "		<h3>Tareas de Soporte Cerradas</h3>";
      echo "		<ul>";
      foreach ($closed_supports as $support) {
        echo "		    <li>".$support->subject." [".$support->priority['name']."] - Starts on ".$support->start_date." - Created on ". $support->created_on . " by ".$support->author['name']."</li>";
      }
      echo "		</ul>";
      //echo "		<p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=features&amp;sesskey=" . sesskey() . "'>Ver todas las tareas</a></p>";
      echo "	        </div>";
      echo "	    </div>";
      echo "	    <div id='splitcontentright'>";
      echo "	    </div>";
      echo "	    <div style='clear:both;'>";
      if ($page > 0) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=supports&amp;sesskey=" . sesskey() . "&amp;page=".($page-1)."'>Anteriores | </a>";
      }
      if (count($open_supports) || count($closed_supports)) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=supports&amp;sesskey=" . sesskey() . "&amp;page=".($page+1)."'> Siguientes</a>";
      }
      echo "</div>";
    } else {
      echo $issues->error;
    }
    echo "	</div>";
    echo "</div>";
    // ----------- ^^^ SUPPORT ^^^ ------------------------------------------------------






    // ----------- vvv TIME ENTRIES vvv ------------------------------------------------------
  } elseif ($option == 'time_entries') {
    $time_entries = new RedmineTimeEntry();
    $time_entries->setSite($server_url);
    $time_entries->extra_params = "?project_id=".$modredmine->project_identifier."&limit=".MAX_TIME_ENTRIES."&offset=".$page;
    $timees = $time_entries->find('all');
    $hours = 0;
    foreach ($timees as $time) {
      $hours += $time->hours;
    }

    echo $OUTPUT->heading($server->name);
    echo "<div id='header'>";
    echo "  <h1>$modredmine->project_name</h1>";
    echo "  <div id='main-menu' style='margin-top: 12px; position: relative;'>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;sesskey=".sesskey()."' >Resumen</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=issues&amp;sesskey=".sesskey()."' class='issues'>Peticiones</a></li>";
    echo "      <ul><li style='display:inline;'><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=news&amp;sesskey=".sesskey()."' class='news'>Noticias</a></li>";
    echo "      <ul><li style='display:inline;'><a class='overview selected' href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Tiempo Dedicado</a></li>";
    echo "  </div>";
    echo "</div>";
    echo "<div class='main'>";
    echo "	<div id='sidebar'>";
    echo "	    <h3>Tiempo dedicado</h3>";
    echo "	    <p><span class='icon icon-time'>$hours horas</span></p>";
    echo "	    <p><a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=".sesskey()."'>Detalles</a></p>";
    echo "	</div>";
    echo "	<div id='content'>";

    if (!$time_entries->errno) {
      echo "	    <div id='splitcontentleft'>";
      echo "	        <h2>Tiempo dedicado</h2>";
      echo "	        <table class='list time-entries'>";
      echo "		<thead>";
      echo "		    <tr>";
      echo "		        <th>Fecha</th>";
      echo "		        <th>Miembro</th>";
      echo "		        <th>Actividad</th>";
      echo "		        <th>Proyecto</th>";
      echo "		        <th>Petición</th>";
      echo "		        <th>Horas</th>";
      echo "		    </tr>";
      echo "		</thead>";
      echo "		<tbody>";
      $odd = 1;
      foreach ($timees as $te) {
        echo "	    <tr class='time-entry ";
        if ($odd) { echo 'odd'; } else { echo 'even'; }
        echo "'>";
        echo "<td>$te->created_on</td><td>".$te->user."</td><td>".$te->activity['name']."</td><td>".$te->project['name']."</td><td>".$te->issue['name']."</td><td>".$te->hours."</td>";
        echo "	    </tr>";
        $odd ? $odd = 0 : $odd = 1;
      }
      echo "		</tbody>";
      echo "	        </table>";
      echo "	    </div>";
      echo "	    <div style='clear:both;'>";
      if ($page > 0) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=" . sesskey() . "&amp;page=".($page-1)."'>Anteriores | </a>";
      }
      if (count($timees)) {
        echo "<a href='$CFG->wwwroot/mod/modredmine/view.php?id=$id&amp;option=time_entries&amp;sesskey=" . sesskey() . "&amp;page=".($page+1)."'> Siguientes</a>";
      }

      echo "</div>";
    } else {
      echo $issues->error;
    }
    echo "	</div>";
    echo "</div>";
    // ----------- ^^^ TIME ENTRIES ^^^ ------------------------------------------------------








  } else {
    echo "Aún no implementado";
  }
  echo "	</div>";
  echo "</div>";

}

// Finish the page
echo $OUTPUT->footer();

