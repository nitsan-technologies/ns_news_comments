<?php

$EM_CONF['ns_news_comments'] = [
    'title' => 'News Comment',
    'description' => 'All New TYPO3 News Comment Extension helps add comments to your news pages and articles. It comes with a variety of features for managing and customizing comments. It is designed to enhance user experience with a simple interface and is SEO-friendly.

    *** Live Demo: https://demo.t3planet.com/t3-extensions/seo/news-comments/ *** Premium Version, Documentation & Free Support: https://t3planet.com/typo3-news-comment-extension',
    'category' => 'plugin',
    'author' => 'T3: Himanshu Ramavat, T3: Divya Goklani, T3: Nilesh Malankiya, QA: Krishna Dhapa',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'T3Planet // NITSAN',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '12.2.0',
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
