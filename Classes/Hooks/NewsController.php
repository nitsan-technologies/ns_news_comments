<?php
namespace Nitsan\NsNewsComments\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class NewsController
{
    public function modify(array $params)
    {
        $sessionService = GeneralUtility::makeInstance(\TYPO3\CMS\Install\Service\SessionService::class);
        $sessionService->startSession();
        $_SESSION['params'] = $params;
        $settings = $params['originalSettings'];
        return $settings;
    }
}
