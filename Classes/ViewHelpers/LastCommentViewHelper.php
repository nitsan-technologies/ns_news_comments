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
    public function initializeArguments()
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
        $newsuid = $this->arguments['newsuid'];
        $newscommentData = [];
        if($newsuid){
            $commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
            // Get last comment of news
            $newscommentData = $commentRepository->getLastCommentOfNews((int) $newsuid);
        }
        return $newscommentData;
       
    }
}
