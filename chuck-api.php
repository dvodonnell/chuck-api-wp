<?php
/*
Plugin Name: Chuck WP Api
*/

require_once(__DIR__ . '/lib/ChuckAPI.php');

$pg = new ChuckAPI();

$pg->initialize();