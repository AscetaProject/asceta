<?php
/**
* Display Contact Info view
*
*
 * @package   mod_modmendeley
 * @copyright 2011 María del Mar Jiménez Torres (mjimenez@fidesol.org) - Fundación I+D del Software Libre (www.fidesol.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

$main = $profile_info->main;
$cv = $profile_info->cv;
$contact = $profile_info->contact;
$location = split(',', $main->location);
echo "<div id='content-container'>";
echo "    <div id='main-content'>";
echo "        <div id='profile_tabs'>";
echo "            <div class='tabs-general' id=''>";
echo "                <ul>";
echo "                    <li class='selected'><a href='$main->url' target='_blank'>View Profile</a></li>";
echo "                </ul>";
echo "            </div>";
echo "        </div>";
echo "        <div class='submenu-bottom-spacer'></div>";
echo "        <div class='leftcontent-rightbox left'>";
echo "            <div class='info_section_main'>";
echo "                <div id='main_info_photo'>";
echo "                    <div class='profile_main_info_left'>";
echo "                        <div id='profile_photo'>";
echo "                            <a rel='nofollow' href='$main->url' target='_blank' title='$main->name'>";
echo "                                <img class='profile_image photo-full' src='$main->photo' alt='$main->name profile photo'>";
echo "                            </a>";
echo "                        </div>";
echo "                    </div>";
echo "                </div>";
echo "                <div id='main_info_show' style='display:block'>";
echo "                    <div class='data'>";
echo "                        <div class='profile_main_info_right'>";
echo "                            <h1 class='underlined'>$main->name</h1>";
echo "                            <span class='location_name'>$main->location</span><br>";
echo "                            <br>";
echo "                            <span class='bold'>Research field: <a href='http://www.mendeley.com/$main->discipline_name/'>$main->discipline_name</a></span>";
echo "                            <br>";
echo "                            <a name='research_interests'></a>";
echo "                            <span class='text-minor'>No research interests added yet.</span>";
echo "                        </div>";
echo "                        <div class='clear'></div>";
echo "                    </div>";
echo "                </div>";
echo "                <div class='clear'></div>";
echo "            </div>";
echo "            <div class='info_section'>";
echo "                <h3 class='underlined' id='cv_info'>CV</h3>";
if (true){
    echo "                 <div class='empty text-minor'>No CV information added yet.</div>";
} else {
    echo "                <div id='experiences'>";
    echo "                    <span class='bold' style='float: left;'>Professional Experience</span>";
    echo "                    <div id='experiences_info_show'>";
    echo "                        <br><br>";
    echo "                        <div id='experiences_info_add_edit' style='display:none'></div>";
    echo "                        <div id='experiences_info_container'>";
    $index = 0;
    foreach ($cv->employment as $employment){
        $date_employment = createDate(split('-',$employment->start_date), split('-',$employment->end_date));
        echo "                            <div id='experiences_info_$index_show' class='experiences_info_item'>";
        echo "                                <div class='experience'>";
        echo "                                    <div class='data'>";
        echo "                                        <div class='prof_bio_left text-minor'> $date_employment </div>";
        echo "                                        <div class='prof_bio_right'>$employment->position at <a rel='nofollow' class='red' href='$employment->website'>$employment->institution</a>";
        echo "                                            <br><span class='location_name'>$employment->location</span><br>";
        echo "                                            <span class='bold'>Classes taught:</span><br>";
        foreach($employment->classes_taught as $classes){
            echo "        $classes<br>";
        }
        echo "                                        </div>";
        echo "                                    </div>";
        echo "                                </div>";
        echo "                                <div style='clear: both;'></div>";
        echo "                            </div>";
        $index ++;
    }
    echo "                      </div>";
    echo "                    </div>";
    echo "                </div>";
    echo "                <a name='education_info'></a>";
    echo "                <div id='educations'>";
    echo "                    <span class='bold' style='float: left;'>Education</span>";
    echo "                    <div id='educations_info_show'><br><br>";
    echo "                        <div id='educations_info_add_edit' style='display: none;'></div>";
    echo "                        <div id='educations_info_container'>";
    $index = 0;
    foreach ($cv->education as $education){
        $date_education = createDate(split('-',$education->start_date), split('-',$education->end_date));
        echo "                            <div id='educations_info_$index_show' style='display:block' class='education educations_info_item'>";
        echo "                                <div class='eduction'>";
        echo "                                    <div class='data'>";
        echo "                                        <div class='prof_bio_left text-minor'>$date_education</div>";
        echo "                                        <div class='prof_bio_right'><a rel='nofollow' class='red' href='$education->website'>$education->institution</a>";
        echo "                                            <span class='location_name'>in $education->location</span><br>$education->degree<br>";
        echo "                                        </div>";
        echo "                                    </div>";
        echo "                                </div>";
        echo "                                <div style='clear: both;'></div>";
        echo "                            </div>";
        $index ++;
    }
    echo "                        </div>";
    echo "                    </div>";
    echo "                </div>";
    echo "            </div>";
    echo "            <div class='info_section_bottom'>";
    echo "                <h3 class='underlined' id='contact_details'>Contact Information</h3>";
    echo "                <div id='contact_info_show' style='display:block'>";
    echo "                    <div class='data'>";
    echo "                        <table cellpadding='0' cellspacing='0' class='ci_table'>";
    echo "                            <tbody>";
    echo "                                <tr>";
    echo "                                    <td class='ci_title'>Address:</td>";
    echo "                                    <td class='ci_value'>$location[0], $contact->zipcode, $location[1]</td>";
    echo "                                </tr>";
    echo "                            </tbody>";
    echo "                        </table>";
    echo "                    </div>";
    echo "                </div>";
}
echo "            </div>";
echo "            <script>$(function(){";
echo "                var offsets = $('#highlighted').offset();";
echo "                if(offsets) $('html,body').animate({ scrollTop: offsets.top - 100 }, 1000);";
echo "            });";
echo "            </script>";
echo "        </div>";
echo "        <div class='clear'></div>";
echo "    </div>";
echo "</div>";

?>