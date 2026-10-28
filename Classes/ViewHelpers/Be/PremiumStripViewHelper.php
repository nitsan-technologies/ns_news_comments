<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\ViewHelpers\Be;

use Nitsan\NsNewsComments\Backend\PremiumStrip;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders the premium strip in the Page module preview of the News Comment plugin.
 */
final class PremiumStripViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(
        private readonly PremiumStrip $premiumStrip,
        private readonly PageRenderer $pageRenderer
    ) {}

    public function render(): string
    {
        $strip = $this->premiumStrip->render();
        if ($strip !== '') {
            $this->pageRenderer->addCssFile(PremiumStrip::STYLESHEET);
        }

        return $strip;
    }
}
