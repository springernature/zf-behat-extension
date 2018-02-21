<?php

if ( ! isset($application) || ! $application instanceof \Zend\Mvc\Application) {
    throw new \RuntimeException('Application instance is not available');
}

$GLOBALS['bootstrap_ran'] = true;
