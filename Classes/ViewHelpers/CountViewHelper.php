<?php
namespace Nitsan\NsNewsComments\ViewHelpers;

/**
 *  Get the counts of news comments
 */
class CountViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $newsuid = (int) $this->arguments['newsuid'];
        if ($newsuid) {
            // Get the counts of news comments
            $commentCount = $this->commentRepository->getCountOfComments($newsuid);
            return $commentCount;
        }
    }
}
