<?php

$datestr = '2017-12-04 16:20:00';
$unixtime = strtotime($datestr);

if ($unixtime === false) {
    echo "Wrong time format .... ";
} // end if
else {
    echo "Date string: $datestr <br>";
    echo "Unix time: " . $unixtime . "<br>";
}
