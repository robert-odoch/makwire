<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'profile');
require_once('common/user-page-start.php');
require_once("common/secondary-user-nav.php");
?>

<div class="box show-profile">
    <?php
    // If there is nothing to show.
    if ($is_visitor && !$profile['colleges'] && !$profile['halls']
        && !$profile['hostels'] && !$profile['country'] && !$profile['district']) {
        print "<div class='alert alert-info'>" .
                "<span class='glyphicon glyphicon-info-sign'></span> Nothing to show.</div>";
    }
    else {
    ?>
        <?php if (!$is_visitor || $profile['colleges']) { ?>
            <h4>Education</h4>
            <ul class="profile">
            <?php
            if ($profile['colleges']) {
                foreach ($profile['colleges'] as $college) {
                    print "<span><b>{$college['start_year']} - {$college['end_year']}</b></span>";
                    print "<li><b>College: </b>{$college['college_name']}";
                    if (!$is_visitor) {
                        print ' <a href="' . base_url("profile/edit-college/{$college['id']}") .
                            '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>';
                    }
                    print "</li>";

                    print "<li><b>School: </b>{$college['school']['school_name']}</li>";

                    if ($college['has_programme']) {
                        print "<li><b>Programme: </b>{$college['programme']['programme_name']}";
                        if (!$is_visitor) {
                            print ' <a href="' . base_url("profile/edit-programme/{$college['programme']['id']}") .
                                    '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>';
                        }
                        print "</li>";
                    }
                    else {
                        if (!$is_visitor) {
                            print '<li><b>Programme: </b><a href="'.
                                    base_url("profile/add-programme/{$college['school']['id']}") .
                                    '"><em>Add programme</em></a></li>';
                        }
                    }
                }  // End foreach.

                if (!$is_visitor) {
                    print '<a href="'. base_url("profile/add-college") . '"><em>Add college</em></a>';
                }
            }
            elseif (!$is_visitor) {
                print '<a href="'. base_url("profile/add-college") . '"><em>Add college</em></a>';
            } // ($profile['colleges']).

            print "</ul>";
        } // (!$is_visitor || $profile['colleges']) ?>

        <?php if (!$is_visitor || ($profile['hostels'] || $profile['halls'])) { ?>
            <h4>Residence</h4>
            <ul class="profile">
                <?php
                if ($profile['halls']) {
                    foreach ($profile['halls'] as $hall) {
                        if ($hall['resident']) {
                            print "<li><b>{$hall['start_year']} - {$hall['end_year']}: </b>" .
                                    "Resident of {$hall['hall_name']}";
                            if (!$is_visitor) {
                                print' <a href="' . base_url("profile/edit-hall/{$hall['id']}") .
                                        '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>';
                            }
                            print "</li>";
                        }
                        else {
                            print "<li><b>{$hall['start_year']} - {$hall['end_year']}: </b>" .
                                    "Attached to {$hall['hall_name']}";
                            if (!$is_visitor) {
                                print ' <a href="' . base_url("profile/edit-hall/{$hall['id']}") .
                                        '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>';
                            }
                            print "</li>";
                        }
                    }

                    if (!$is_visitor) {
                        print '<a href="'. base_url("profile/add-hall") . '"><em>Add hall</em></a>';
                    }
                }
                else {
                    print '<a href="'. base_url("profile/add-hall") . '"><em>Add hall</em></a>';
                }

                if ($profile['hostels']) {
                    foreach ($profile['hostels'] as $hostel) {
                        print "<li><b>{$hostel['start_year']} - {$hostel['end_year']}: </b>" .
                                "{$hostel['hostel_name']}";
                        if (!$is_visitor) {
                            print ' <a href="' . base_url("profile/edit-hostel/{$hostel['id']}") .
                                    '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>';
                        }
                        print "</li>";
                    }
                }
                if (!$is_visitor) {
                    if (!$profile['hostels']) {
                        print ' &middot; ';
                    }
                    print '<a href="'. base_url("profile/add-hostel") . '"><em>Add hostel</em></a>';
                }
                ?>
            </ul>
        <?php } // if ($profile['halls'] || $profile['hostels']) ?>

        <?php if (!$is_visitor || ($profile['country'] || $profile['district'])) { ?>
            <h4>Origin</h4>
            <ul class="profile">
                <?php
                if (!$is_visitor) {
                    if ($profile['district']) {
                        print "<li><b>District: </b>{$profile['district']}";
                        print ' <a href="' . base_url("profile/edit-district") .
                                '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>';
                        print "</li>";
                    }
                    else {
                        print '<li><b>District: </b><a href="' . base_url("profile/add-district") .
                                '"><em>Add district</em></a></li>';
                    }

                    if ($profile['country']) {
                        print "<li><b>Country: </b>{$profile['country']}";
                        print ' <a href="' . base_url("profile/edit-country") .
                                '"><span class="glyphicon glyphicon-pencil"></span> <em>Edit</em></a>';
                        print "</li>";
                    }
                    else {
                        print '<li><b>Country: </b><a href="' . base_url("profile/add-country") .
                                '"><em>Add country</em></a></li>';
                    }
                } else {
                ?>
                    <li>From
                    <?php
                    if ($profile['district']) {
                        print " {$profile['district']}";
                    }

                    if ($profile['country'] && $profile['district']) {
                        print ", {$profile['country']}.";
                    }
                    elseif ($profile['country']) {
                        print " {$profile['country']}.";
                    }
                    elseif ($profile['district']) {
                        print ".";
                    }
                    print "</li>";
                    ?>
                    </li>
                <?php } // (!$is_visitor) ?>
            </ul>
        <?php } // End (!$is_visitor \\ ($profile['country'] || $profile['district'])) ?>
    <?php } // End nothing to show.?>
</div><!-- box -->
