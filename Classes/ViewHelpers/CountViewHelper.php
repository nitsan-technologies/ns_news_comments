<?php

namespace Nitsan\NsNewsComments\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;

/**
 *  Get the counts of news comments
 */
class CountViewHelper extends AbstractViewHelper
{
    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('newsuid', 'int', 'news uid', true);
    }

    /**
     * Last Comment
     *
     */
    public function render(): int
    {
        $newsUid = (int) $this->arguments['newsuid'];
        $commentCount = 0;
        if ($newsUid) {
            $commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
            // Get the counts of news comments
            $commentCount = $commentRepository->getCountOfComments($newsUid);
        }
        return $commentCount;
    }
}
