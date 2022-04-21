<?php

require_once "functions.php";

function message_date($date)
{

    $message_date = date_parse_from_format("Y-m-d H:i:s", $date);
    echo $message_date['hour'] > 12 ? $message_date['hour'] - 12 . ":$message_date[minute] PM" : "$message_date[hour]:$message_date[minute] AM";
}

function image_name($img_name){

    $rand_str = rand_str('none', 18);

    return "chatApp_" . $img_name . "_" . $rand_str;
}