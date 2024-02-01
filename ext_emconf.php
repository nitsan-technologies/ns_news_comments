<?php

$EM_CONF['ns_news_comments'] = [
    'title' => '[NITSAN] Comment Plugin for EXT:news',
    'description' => 'Do you want to smoothly integrate comment features in your favourite News extension? This extension includes powerful features like post comment, reply to comment, multi-level comment thread, moderation etc., Demo: https://demo.t3planet.com/t3t-extensions/news-comments/ You can download PRO version for more-features & free-support at https://t3planet.com/news-comment-typo3-extension/',
    'category' => 'plugin',
    'author' => 'Team NITSAN',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'NITSAN Technologies Pvt Ltd',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '12.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-12.4.99',
            'news' => '11.0.0-11.9.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
