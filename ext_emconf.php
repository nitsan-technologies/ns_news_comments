<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '[NITSAN] Comment Plugin for EXT:news',
    'description' => 'Do you want to smoothly integrate comment features in your favourite News extension? This extension includes powerful features like post comment, reply to comment, multi-level comment thread, moderation etc., Demo: https://demo.t3terminal.com/t3t-extensions/news-comment/ You can download PRO version for more-features & free-support at https://t3terminal.com/ns-news-comments-pro/',
    'category' => 'plugin',
    'author' => 'T3:Keval Pandya, FE:Mehul Nimavat, QA:Vandna Kalivada',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'NITSAN Technologies Pvt Ltd',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '4.2.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.0.0-10.9.99',
            'news' => '3.0.0-8.9.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
