<?php

$EM_CONF['ns_news_comments'] = [
    'title' => 'TYPO3 News Comments Extension',
    'description' => 'Add a full-featured comment system to your TYPO3 News articles. This extension provides easy comment management, a clean frontend interface, and SEO-friendly functionality to improve user engagement on your news pages.',

    'category' => 'plugin',
    'author' => 'Team NITSAN',
    'author_email' => 'info@nitsantech.de',
    'author_company' => 'NITSAN',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '13.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-13.9.99',
            'news' => '11.0.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'classmap' => ['Classes/']
    ]
];
