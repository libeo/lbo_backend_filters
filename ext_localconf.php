<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    // XCLASS pour modification du rendu de la recherche dans le backend.
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList::class] = [
        'className' => Libeo\LboBackendFilters\XClass\DatabaseRecordList::class,
    ];

}
