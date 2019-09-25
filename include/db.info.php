<?php
session_start(); // ready to go!

error_reporting(E_ALL ^ E_NOTICE);
session_start();

$passcode = 123456789; //gotta make this random once every 12 hours, and no messages after a week
$method = "AES-128-ECB";

$dbAddress = "localhost";
$dbPass = "";
$dbUsername = "root";
$dbName = "erewhon";