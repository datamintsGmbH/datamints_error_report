<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'datamints Error Report',
    'description' => 'Sends bundled error-log entries via email',
    'category' => 'services',
    'author' => 'Mark Weisgerber',
    'author_email' => 'm.weisgerber@datamints.com',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
