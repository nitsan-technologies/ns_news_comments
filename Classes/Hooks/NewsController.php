<?php

namespace Nitsan\NsNewsComments\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Service\SessionService;

class NewsController
{
    public function modify(array $params) : array
    {
        $sessionService = GeneralUtility::makeInstance(SessionService::class);
        $sessionService->startSession();
        $_SESSION['params'] = $params;
        $settings = $params['originalSettings'];
        return $settings;
    }
}
