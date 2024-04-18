<?php

namespace Nitsan\NsNewsComments\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;

/**
 *  Get last comment of news record
 */
class LastCommentViewHelper extends AbstractViewHelper
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
    public function render()
    {
        $newsUid = $this->arguments['newsuid'];
        $newsCommentData = [];
        if($newsUid) {
            $commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
            // Get last comment of news
            $newsCommentData = $commentRepository->getLastCommentOfNews((int) $newsUid);
        }
        return $newsCommentData;

    }
}
