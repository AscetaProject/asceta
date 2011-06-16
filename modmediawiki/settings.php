<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/modmediawiki/lib.php');

    $pixpath = "$CFG->wwwroot/pix";
    //$vicon = "<a title=\"".$vtitle."\" href=\"$CFG->wwwroot/mod/modmediawiki/formats.php?id=$formatid&amp;mode=visible&amp;sesskey=".sesskey()."\"><img class=\"iconsmall\" src=\"$pixpath/t/".$vicon."\" alt=\"$vtitle\" /></a>";
    $str = '<table>';
        $servers = $DB->get_records('modmediawiki_server', array());
        $str .= '<tr>';
        $str .= '<td>'.get_string('tablename', 'modmediawiki').'</td><td>'.get_string('tableurl', 'modmediawiki').'</td><td>'.get_string('tablecourse', 'modmediawiki').'</td><td>'.get_string('tableactions', 'modmediawiki').'</td>';
        $str .= '</tr>';
        foreach($servers as $server){
            $viconedit = "<a title=\"Editar\" href=\"$CFG->wwwroot/mod/modmediawiki/servers.php\?id=$server->id\" /><img class=\"iconsmall\" src=\"$pixpath/t/edit.gif\" alt=\"editar\" /></a>";
            $vicondelete = "<a title=\"Eliminar\" href=\"$CFG->wwwroot/mod/modmediawiki/servers.php\" /><img class=\"iconsmall\" src=\"$pixpath/t/delete.gif\" alt=\"eliminar\" /></a>";
            $course = $DB->get_record('course',array('id'=>$server->course_id));
            $str .= '<tr>';
            $str .= '<td>'.$server->name.'</td><td>'.$server->url.'</td><td>'.$course->fullname.'</td><td>'.$viconedit.' '.$vicondelete.'</td>';
            $str .= '</tr>';
        }
    $str .= '</table>';

    $settings->add(new admin_setting_heading('modmediawiki_formats_header', get_string('modmediawikiserverlist', 'modmediawiki'), $str));
}
