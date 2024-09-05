<?php

if (!defined('TYPO3')) {
    die ('Access denied.');
}

// XCLASS pour modification du rendu de la recherche dans le backend.
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\RecordList\DatabaseRecordList::class] = [
    'className' => Libeo\LboBackendFilters\XClass\DatabaseRecordList::class,
];
