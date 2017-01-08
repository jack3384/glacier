<?php
require_once 'vendor/autoload.php';

$app=require_once __DIR__.'/System/init.php';


$app['kernel']->run();