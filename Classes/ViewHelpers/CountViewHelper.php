<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\ViewHelpers;

use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 *  Get the counts of news comments
 */
class CountViewHelper extends AbstractViewHelper
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
    public function render(): int
    {
        $newsUid = (int)$this->arguments['newsuid'];
        $commentCount = 0;
        if ($newsUid) {
            $commentCount = $this->commentRepository->getCountOfComments($newsUid);
        }
        return $commentCount;
    }
}
