<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Filtres backend',
    'description' => 'Filtres personnalisés pour les listes du backend',
    'category' => 'backend',
    'author' => ['Pierre Boivin'],
    'author_email' => 'pierre.boivin@libeo.com',
    'author_company' => 'Libéo',
    'priority' => '',
    'state' => 'stable',
    'uploadfolder' => '0',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '1.0.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '>=10.4.0',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);
