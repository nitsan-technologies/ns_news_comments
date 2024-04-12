<?php

namespace Nitsan\NsNewsComments\ViewHelpers;

use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 *  Get last comment of news record
 */
class LastCommentViewHelper extends AbstractViewHelper
{
    /**
     * commentRepository
     *
     * @var CommentRepository
     */
    protected $commentRepository = null;

    /**
     * Inject a news repository to enable DI
     *
     * @param CommentRepository $commentRepository
     */
    public function injectCommentRepository(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

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
        $newsUid = $this->arguments['newsuid'];

        // Get last comment of news
        return $this->commentRepository->getLastCommentOfNews($newsUid);
    }
}
