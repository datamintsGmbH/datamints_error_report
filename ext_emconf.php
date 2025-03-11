<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'datamints Error Report',
    'description' => 'Adds scheduler task to send bundled sys_log errors via email',
    'category' => 'services',
    'author' => 'Mark Weisgerber',
    'author_email' => 'm.weisgerber@datamints.com',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-13.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
