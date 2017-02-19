<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');

if ($visitor) {
    define('PAGE', "about");
    require_once("common/secondary-user-nav.php");
}
?>

<div class="box show-profile">
    <?php
    // If there is nothing to show.
    if ($visitor && !$profile['programmes']
        && !$profile['colleges'] && !$profile['schools'] && !$profile['halls']
        && !$profile['hostels'] && !$profile['country'] && !$profile['district']) {
        print("<div class='alert alert-info'><p>Nothing to show...</p></div>");
    }
    else {
    ?>
        <?php if (!$visitor || $profile['colleges']) { ?>
            <h4>Education</h4>
            <ul class="profile">
            <?php
            if ($profile['colleges']) {
                foreach ($profile['colleges'] as $college) {
                    print("<span><b>{$college['start_year']} - {$college['end_year']}</b></span>");
                    print("<li><b>College: </b>{$college['college_name']}");
                    if (!$visitor) {
                        print(' <a href="' . base_url("user/edit-college/{$college['id']}") . '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>');
                    }
                    print("</li>");

                    if ($profile['schools']) {
                        foreach ($profile['schools'] as $school) {
                            if (($school['date_from'] == $college['date_from']) &&
                                ($school['date_to'] == $college['date_to'])) {
                                print("<li><b>School: </b>{$school['school_name']}</li>");
                            }
                        }
                    }

                    $has_programme = FALSE;
                    if ($profile['programmes']) {
                        foreach ($profile['programmes'] as $programme) {
                            if (($programme['date_from'] == $college['date_from']) &&
                                ($programme['date_to'] == $college['date_to'])) {
                                $has_programme = TRUE;
                                print("<li><b>Programme: </b>{$programme['programme_name']}");
                                if (!$visitor) {
                                    print(' <a href="' . base_url("user/edit-programme/{$programme['id']}") . '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>');
                                }
                                print("</li>");
                            }
                        }
                    }

                    if (!$visitor && !$has_programme) {
                        print('<li><b>Programme: </b><a href="'. base_url("user/add-programme/{$college['id']}") . '"><em>Add programme</em></a></li>');
                    }
                }  // End foreach.

                if (!$visitor) {
                    print('<a href="'. base_url("user/add-college") . '"><em>Add college</em></a>');
                }
            }
            elseif (!$visitor) {
                print('<a href="'. base_url("user/add-college") . '"><em>Add college</em></a>');
            } // ($profile['colleges']).

            print("</ul>");
        } // (!$visitor || $profile['colleges']) ?>

        <?php if (!$visitor || ($profile['hostels'] || $profile['halls'])): ?>
            <h4>Residence</h4>
            <ul class="profile">
                <?php
                if ($profile['halls']) {
                    foreach ($profile['halls'] as $hall) {
                        if ($hall['resident']) {
                            print("<li><b>{$hall['start_year']} - {$hall['end_year']}: </b>Resident of {$hall['hall_name']}");
                            if (!$visitor) {
                                print(' <a href="' . base_url("user/edit-hall/{$hall['id']}") . '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>');
                            }
                            print("</li>");
                        }
                        else {
                            print("<li><b>{$hall['start_year']} - {$hall['end_year']}: </b>Attached to {$hall['hall_name']}");
                            if (!$visitor) {
                                print(' <a href="' . base_url("user/edit-hall/{$hall['id']}") . '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>');
                            }
                            print("</li>");
                        }
                    }

                    if (!$visitor) {
                        print('<a href="'. base_url("user/add-hall") . '"><em>Add hall</em></a>');
                    }
                }
                else {
                    print('<a href="'. base_url("user/add-hall") . '"><em>Add hall</em></a>');
                }

                if ($profile['hostels']) {
                    foreach ($profile['hostels'] as $hostel) {
                        print("<li><b>{$hostel['start_year']} - {$hostel['end_year']}: </b>{$hostel['hostel_name']}");
                        if (!$visitor) {
                            print(' <a href="' . base_url("user/edit-hostel/{$hostel['id']}") . '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>');
                        }
                        print("</li>");
                    }
                }
                if (!$visitor) {
                    if (!$profile['hostels']) {
                        print(' &middot; ');
                    }
                    print('<a href="'. base_url("user/add-hostel") . '"><em>Add hostel</em></a>');
                }
                ?>
            </ul>
        <?php endif; // if ($profile['halls'] || $profile['hostels']) ?>

        <?php if (!$visitor || ($profile['country'] || $profile['district'])): ?>
            <h4>Origin</h4>
            <ul class="profile">
                <?php
                if (!$visitor) {
                    if ($profile['district']) {
                        print("<li><b>District: </b>{$profile['district']}");
                        print(' <a href="' . base_url("user/edit-district") . '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>');
                        print("</li>");
                    }
                    else {
                        print('<li><b>District: </b><a href="' . base_url("user/add-district") . '"><em>Add district</em></a></li>');
                    }

                    if ($profile['country']) {
                        print("<li><b>Country: </b>{$profile['country']}");
                        print(' <a href="' . base_url("user/edit-country") . '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>');
                        print("</li>");
                    }
                    else {
                        print('<li><b>Country: </b><a href="' . base_url("user/add-country") . '"><em>Add country</em></a></li>');
                    }
                } else {
                ?>
                    <li>From
                    <?php
                    if ($profile['district']) {
                        print(" {$profile['district']}");
                    }

                    if ($profile['country'] && $profile['district']) {
                        print(", {$profile['country']}.");
                    }
                    elseif ($profile['country']) {
                        print(" {$profile['country']}.");
                    }
                    elseif ($profile['district']) {
                        print(".");
                    }
                    print("</li>");
                    ?>
                    </li>
                <?php } // (!$visitor) ?>
            </ul>
        <?php endif; // End (!$visitor \\ ($profile['country'] || $profile['district'])) ?>
    <?php } // End nothing to show.?>
</div><!-- box -->
