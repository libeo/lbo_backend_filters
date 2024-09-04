<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Filtres backend',
    'description' => 'Filtres personnalisés pour les listes du backend',
    'category' => 'backend',
    'author' => ['Pierre Boivin'],
    'author_email' => 'pierre.boivin@libeo.com',
    'author_company' => 'Libéo',
    'state' => 'stable',
    'version' => '12.0.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '12.4.0-12.4.99',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);
