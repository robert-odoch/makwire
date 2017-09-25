<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'profile');
require_once('common/user-page-start.php');
require_once("common/secondary-user-nav.php");
?>

<?php
if ( ! empty($profile_questions)) {
    print "div class='box'>";
        foreach ($profile_questions as $pq) {
            print $pq;
        }
    print "</div>";
}

// If there is nothing to show.
if ($is_visitor && !$profile['schools'] && !$profile['halls']
    && !$profile['hostels'] && !$profile['origin']['district']) {
    print "<div class='box'>
                <div class='alert alert-info' role='alert'>
                    <span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span>
                    <p>Nothing to show.</p>
                </div>
            </div>";
}
?>

<?php if (!$is_visitor || $profile['schools']) { ?>
    <div class='box profile'>
        <h4>Education</h4>
        <?php
        if ($profile['schools']) {
            foreach ($profile['schools'] as $sch) {
                print "<p class='year'><span>{$sch['start_year']} - {$sch['end_year']}</span></p>";

                print "<table class='table table-bordered'>";

                if ($sch['college_name']) {
                    print "<tr><td><b>College</b></td><td>{$sch['college_name']}</td></tr>";
                }

                print "<tr><td><b>School</b></td><td>{$sch['school_name']}";
                if (!$is_visitor) {
                    print " <a href='" . base_url("profile/edit-school/{$sch['id']}") .
                        "'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span> Edit</a>";
                }
                print "</td></tr>";

                if ($sch['has_programme']) {
                    print "<tr><td><b>Programme</b></td><td>{$sch['programme']['programme_name']}";
                    if (!$is_visitor) {
                        print " <a href='" . base_url("profile/edit-programme/{$sch['programme']['id']}") .
                            "'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span> Edit</a>";
                    }
                    print "</td></tr>";
                }
                else {
                    if (!$is_visitor) {
                        print "<tr><td><b>Programme</b></td><td><a href='".
                                base_url("profile/add-programme/{$sch['id']}") .
                                "'>Add programme</a></td></tr>";
                    }
                }

                print "</table>";
            }  // End foreach.

            if (!$is_visitor) {
                print "<a href='". base_url("profile/add-school") . "' class='pull-right'>Add school</a>
                        <span class='clearfix'></span>";
            }
        }
        else {
            if (!$is_visitor) {
                print "<a href='". base_url("profile/add-school") . "'>Add school</a>";
            }
        }  // ($profile['schools'])
        ?>
    </div>

<?php } // (!$is_visitor || $profile['schools']) ?>

<?php if (!$is_visitor || ($profile['hostels'] || $profile['halls'])) { ?>
    <div class='box profile'>
        <h4>Residence</h4>
        <?php if ($profile['halls'] || $profile['hostels']): ?>
            <table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Hall/Hostel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($profile['halls']) {
                        foreach ($profile['halls'] as $hall) {
                            print "<tr><td>{$hall['start_year']} - {$hall['end_year']}</td>";
                            if ($hall['resident']) {
                                print "<td>Resident of {$hall['hall_name']}";
                                if (!$is_visitor) {
                                    print " <a href='" . base_url("profile/edit-hall/{$hall['id']}") .
                                            "'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span> Edit</a>";
                                }
                                print "</td>";
                            }
                            else {
                                print "<td>Attached to {$hall['hall_name']}";
                                if (!$is_visitor) {
                                    print " <a href='" . base_url("profile/edit-hall/{$hall['id']}") .
                                            "'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span> Edit</a>";
                                }
                                print "</td>";
                            }
                            print "</tr>";
                        }
                    }

                    if ($profile['hostels']) {
                        foreach ($profile['hostels'] as $hostel) {
                            print "<tr><td>{$hostel['start_year']} - {$hostel['end_year']}</td>";

                            print "<td>{$hostel['hostel_name']}";
                            if (!$is_visitor) {
                                print " <a href='" . base_url("profile/edit-hostel/{$hostel['id']}") .
                                        "'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span> Edit</a>";
                            }
                            print "</td></tr>";
                        }
                    }
                    ?>
                </tdoby>
            </table>

            <?php
            if (!$is_visitor) {
                print "<a href='" . base_url("profile/add-hall") . "' class='pull-right'>Add hall</a>
                        <span class='pull-right'>&nbsp;&nbsp;&middot;&nbsp;&nbsp;</span>
                        <a href='" . base_url("profile/add-hostel") . "' class='pull-right'>Add hostel</a>
                        <span class='clearfix'></span>";
            }
            ?>
        <?php else:
            if (!$is_visitor) {
                print "<a href='" . base_url("profile/add-hall") . "'>Add hall</a>
                        <span>&nbsp;&nbsp;&middot;&nbsp;&nbsp;</span>
                        <a href='" . base_url("profile/add-hostel") . "'>Add hostel</a>";
            }
        endif;?>
    </div>
<?php } // if ($profile['halls'] || $profile['hostels']) ?>

<?php if (!$is_visitor || $profile['origin']['district']) { ?>
    <div class='box profile'>
        <h4>Origin</h4>
        <?php
        if (!$is_visitor) {
            print "<table class='table table-bordered'>";

            if ($profile['origin']['district']) {
                print "<tr><td><b>District</b></td><td>{$profile['origin']['district']}
                        <a href='" . base_url("profile/edit-district") .
                        "'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span> Edit</a>
                        </td></tr>";

                print "<tr><td><b>Country</b></td><td>{$profile['origin']['country']}</td></tr>";
            }
            else {
                print "<a href='" . base_url("profile/add-district") . "'>Add district</a>";
            }

            print "</table>";
        } else {
        ?>
            <p>
                <?php
                if ($profile['origin']['district']) {
                    print "From {$profile['origin']['district']}, {$profile['origin']['country']}.";
                }
                ?>
            </p>
        <?php } // (!$is_visitor) ?>
    </div>
<?php } // End (!$is_visitor \\ ($profile['country'] || $profile['district'])) ?>
