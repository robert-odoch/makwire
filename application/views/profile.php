<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');

if ($visitor) {
    require_once("common/secondary-user-nav.php");
}
?>

<div class="box">
    <?php
    // If there is nothing to show.
    if (!$profile['programmes'] && !$profile['colleges'] && !$profile['schools'] &&
        !$profile['year_of_study'] && !$profile['halls'] && !$profile['hostels'] &&
        !$profile['country'] && !$profile['district']) {
        print("<div class='alert alert-info'><p>Nothing to show...</p></div>");
    }
    ?>
    <ul class="about">
        <?php if ($profile['programmes'] || $profile['colleges'] || $profile['schools']): ?>
        <li>
            <?php
            if ($profile['programmes']) {
                print("Studies/Studied <a href=''>{$profile['programmes'][0]['programme_name']}</a>");
            }
            if ($profile['colleges']) {
                print(" at <a href=''>{$profile['colleges'][0]['college_name']}</a>");
            }
            if ($profile['schools']) {
                print(", <a href=''>{$profile['schools'][0]['school_name']}</a>.");
            }
            ?>
        </li>
        <?php endif; ?>
        <?php
        if ($profile['year_of_study']) {
            $years = array(1=>'I', 'II', 'III', 'IV', 'V');
            print("<li>Currently in Year {$years[$profile['year_of_study']]}.</li>");
        }
        if ($profile['halls']) {
            if ($profile['halls'][0]['resident']) {
                print("<li>Is/Was a residence of <a href=''>{$profile['halls'][0]['hall_name']}</a>.</li>");
            }
            else {
                print("<li>Is/Was attached to <a href=''>{$profile['halls'][0]['hall_name']}</a>.</li>");
            }
        }
        if ($profile['hostels']) {
            print("<li>Stays/Stayed at <a href=''>{$profile['hostels'][0]['hostel_name']}</a>.</li>");
        }
        if ($profile['country'] || $profile['district']) {
            print("<li>From ");
            if ($profile['district']) {
                print("{$profile['district']}");
            }

            if ($profile['country'] && $profile['district']) {
                print(", {$profile['country']}.");
            }
            elseif ($profile['country']) {
                print("{$profile['country']}.");
            }
            elseif ($profile['district']) {
                print(".");
            }

            print("</li>");
        }
        ?>
    </ul>
</div><!-- box -->
