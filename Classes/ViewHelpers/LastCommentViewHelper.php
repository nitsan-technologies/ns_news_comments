<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\ViewHelpers;

use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 *  Get last comment of news record
 */
class LastCommentViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly CommentRepository $commentRepository
    ) {}

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
        if ($newsUid) {
            $newsCommentData = $this->commentRepository->getLastCommentOfNews((int)$newsUid);
        }
        return $newsCommentData;
    }
}
