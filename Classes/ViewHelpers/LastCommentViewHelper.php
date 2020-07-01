<?php
namespace Nitsan\NsNewsComments\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
/**
 *  Get last comment of news record
 */
class LastCommentViewHelper extends AbstractViewHelper
{

    /**
     * commentRepository
     *
     * @var \Nitsan\NsNewsComments\Domain\Repository\CommentRepository
     */
    protected $commentRepository = null;

    /**
     * Inject a news repository to enable DI
     *
     * @param \Nitsan\NsNewsComments\Domain\Repository\CommentRepository $commentRepository
     */
    public function injectCommentRepository(\Nitsan\NsNewsComments\Domain\Repository\CommentRepository $commentRepository)
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
        $newsuid = $this->arguments['newsuid'];

        // Get last comment of news
        $newscommentData = $this->commentRepository->getLastCommentOfNews($newsuid);
        return $newscommentData;
    }
}
