<?php

namespace Nitsan\NsNewsComments\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * The repository for Comments
 */
class CommentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     *
     * @param int $newsId
     */
    public function getCommentsByNews(int $newsId)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('newsuid', $newsId),
                $query->equals('comment', 0),
            )
        );
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);
        $result = $query->execute();
        return $result;
    }


    /**
     *
     * @param int $newsuid
     */
    public function getLastCommentOfNews(int $newsuid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('newsuid', $newsuid),
            )
        );
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);
        $result = $query->setLimit(1)->execute();
        return $result;
    }

    /**
     *
     * @param int $newsId
     * @return int
     */
    public function getCountOfComments(int $newsId) : int
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('newsuid', $newsId),
            )
        );
        return (int) $query->execute()->count();
    }
}
