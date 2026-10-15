<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\Form\Container;

use Nitsan\NsNewsComments\Backend\PremiumStrip;
use TYPO3\CMS\Backend\Form\Container\FlexFormNoTabsContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Core single-sheet FlexForm container with the premium strip above the News Comment plugin options.
 */
final class PremiumStripFlexFormNoTabsContainer extends FlexFormNoTabsContainer
{
    public function render(): array
    {
        $resultArray = parent::render();

        $strip = GeneralUtility::makeInstance(PremiumStrip::class)->render();
        if ($strip !== '') {
            $resultArray['html'] = $strip . $resultArray['html'];
            $resultArray['stylesheetFiles'][] = PremiumStrip::STYLESHEET;
        }

        return $resultArray;
    }
}
