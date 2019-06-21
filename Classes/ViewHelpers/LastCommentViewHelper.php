<?php
namespace Nitsan\NsNewsComments\ViewHelpers;

/**
 *  Get last comment of news record
 */
class LastCommentViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * commentRepository
     *
     * @var \Nitsan\NsNewsComments\Domain\Repository\CommentRepository
     * @inject
     */
    protected $commentRepository = null;

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
