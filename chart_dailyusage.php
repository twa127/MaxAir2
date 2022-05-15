<?php
/*
             __  __                             _
            |  \/  |                    /\     (_)
            | \  / |   __ _  __  __    /  \     _   _ __
            | |\/| |  / _` | \ \/ /   / /\ \   | | |  __|
            | |  | | | (_| |  >  <   / ____ \  | | | |
            |_|  |_|  \__,_| /_/\_\ /_/    \_\ |_| |_|

                   S M A R T   T H E R M O S T A T

*************************************************************************"
* MaxAir is a Linux based Central Heating Control systems. It runs from *"
* a web interface and it comes with ABSOLUTELY NO WARRANTY, to the      *"
* extent permitted by applicable law. I take no responsibility for any  *"
* loss or damage to you or your property.                               *"
* DO NOT MAKE ANY CHANGES TO YOUR HEATING SYSTEM UNTILL UNLESS YOU KNOW *"
* WHAT YOU ARE DOING                                                    *"
*************************************************************************"
*/
$graphs_page = '1';
echo "<h4>".$lang['graph_temperature']."</h4></p>".$lang['graph_24h']."</p>";

$num_Charts = 3;
echo '<div class="flot-chart"><div class="flot-chart-content" id="placeholder"></div></div><br>';
for ($x = 2; $x <= $num_Charts; $x++) {
        if ($x < $num_Charts) {
                echo '<div class="flot-chart"><div class="flot-chart-content" id="graph'.$x.'"></div></div><br>';
        } else {
                echo '<div class="flot-chart"><div class="flot-chart-content" id="graph'.$x.'"></div></div>';
        }
}
?>
