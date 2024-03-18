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
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('newsuid', 'int', 'news uid', true);
    }

    /**
     * Last Comment
     *
     */
    public function render() : int
    {
        $newsuid = (int) $this->arguments['newsuid'];
        $commentCount = 0;
        if ($newsuid) {
            $commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
            // Get the counts of news comments
            $commentCount = $commentRepository->getCountOfComments((int)$newsuid);
        }
        return $commentCount;
    }
}
