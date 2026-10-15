<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\Backend;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Premium strip with a free-trial link to the ns_license module.
 */
final class PremiumStrip
{
    public const STYLESHEET = 'EXT:ns_news_comments/Resources/Public/css/backend-premium-strip.css';

    private const LICENSE_MODULE_ROUTE = 'nitsan_nslicensemodule';
    private const LICENSE_FREE_SECTION_ANCHOR = '#free-section';
    private const LANGUAGE_FILE = 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang.xlf:';

    /**
     * Returns an empty string when ns_license (and with it the trial route) is not available.
     */
    public function render(): string
    {
        try {
            $trialUrl = (string)GeneralUtility::makeInstance(UriBuilder::class)
                ->buildUriFromRoute(self::LICENSE_MODULE_ROUTE, ['trial' => 'ns_news_comments'])
                . self::LICENSE_FREE_SECTION_ANCHOR;
        } catch (RouteNotFoundException) {
            return '';
        }

        $iconSize = enum_exists(IconSize::class) ? IconSize::SMALL : 'small';
        $icon = GeneralUtility::makeInstance(IconFactory::class)->getIcon('actions-rocket', $iconSize)->render();

        return '<div class="ns-news-comments-premium-strip d-flex flex-wrap align-items-center justify-content-between gap-3">'
            . '<div class="d-flex align-items-center gap-3">'
            . '<span class="ns-news-comments-premium-strip-icon">' . $icon . '</span>'
            . '<div>'
            . '<div class="ns-news-comments-premium-strip-headline">' . $this->translate('premium.strip.headline') . '</div>'
            . '<div class="ns-news-comments-premium-strip-description">' . $this->translate('premium.strip.description') . '</div>'
            . '</div>'
            . '</div>'
            . '<a href="' . htmlspecialchars($trialUrl) . '" class="btn btn-primary">'
            . $this->translate('premium.strip.trial') . ' <span aria-hidden="true">&rarr;</span></a>'
            . '</div>';
    }

    private function translate(string $key): string
    {
        $languageService = $GLOBALS['LANG'] ?? null;
        $label = $languageService instanceof LanguageService ? $languageService->sL(self::LANGUAGE_FILE . $key) : '';

        return htmlspecialchars($label);
    }
}
