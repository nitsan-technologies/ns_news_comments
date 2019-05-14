<?php
$EM_CONF[$_EXTKEY] = array(
	'title' => '[NITSAN] Comment Plugin for EXT:news',
	'description' => 'Do you want to smoothly integrate comment feature in your favourite News extension? In this extension, there are features like post comment, reply to comment, multi-level comment thread, moderation etc., Know more from extension manual.',
	'category' => 'plugin',
	'author' => 'NITSAN Technologies',
	'author_email' => 'sanjay@nitsan.in',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '2.0.2',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-9.5.99',
			'news' => '3.0.0-7.9.9',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
