<?php

namespace Nitsan\NsNewsComments\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2024
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
 * Comment
 */
class Comment extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var int
     */
    protected int $sysLanguageUid = 0;

    /**
     * crdate as unix timestamp
     *
     * @var int
     */
    protected int $crdate = 0;

    /**
     * hidden
     *
     * @var int
     */
    protected int $hidden = 0;

    /**
     * feuserid
     *
     * @var int
     */
    protected int $feuserid = 0;

    /**
     * username
     *
     * @var string
     */
    protected string $username = '';

    /**
     * userimage
     *
     * @var string
     */
    protected string $userimage = '';

    /**
     * usermail
     *
     * @var string
     */
    protected string $usermail = '';

    /**
     * captcha
     *
     * @var string
     */
    protected string $captcha = '';

    /**
     * paramlink
     *
     * @var string
     */
    protected string $paramlink = '';

    /**
     * pageid
     *
     * @var int
     */
    protected int $pageid;

    /**
     * newsuid
     *
     * @var int
     */
    protected int $newsuid = 0;

    /**
     * accesstoken
     *
     * @var string
     */
    protected string $accesstoken;

    /**
     * description
     *
     * @var string
     */
    protected string $description = '';

    /**
     * childcomment
     *
     * @var ObjectStorage<Comment>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $childcomment = null;

    /**
     * terms
     *
     * @var bool
     */
    protected bool $terms = false;


    /**
     * Returns the newsuid
     *
     * @return int $newsuid
     */
    public function getNewsuid(): int
    {
        return $this->newsuid;
    }

    /**
     * Sets the newsuid
     *
     * @param int $newsuid
     * @return void
     */
    public function setNewsuid($newsuid): void
    {
        $this->newsuid = $newsuid;
    }

    /**
     * Returns the accesstoken
     *
     * @return string $accesstoken
     */
    public function getAccesstoken(): string
    {
        return $this->accesstoken;
    }

    /**
     * Sets the accesstoken
     *
     * @param string $accesstoken
     * @return void
     */
    public function setAccesstoken($accesstoken): void
    {
        $this->accesstoken = $accesstoken;
    }

    /**
     * Returns the feuserid
     *
     * @return int $feuserid
     */
    public function getFeuserid(): int
    {
        return $this->feuserid;
    }

    /**
     * Sets the feuserid
     *
     * @param int $feuserid
     * @return void
     */
    public function setFeuserid($feuserid): void
    {
        $this->feuserid = $feuserid;
    }

    /**
     * Returns the username
     *
     * @return string $username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Sets the username
     *
     * @param string $username
     * @return void
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * Returns the userimage
     *
     * @return string $userimage
     */
    public function getUserimage(): string
    {
        return $this->userimage;
    }

    /**
     * Sets the userimage
     *
     * @param string $userimage
     * @return void
     */
    public function setUserimage($userimage): void
    {
        $this->userimage = $userimage;
    }

    /**
     * Returns the usermail
     *
     * @return string $usermail
     */
    public function getUsermail(): string
    {
        return $this->usermail;
    }

    /**
     * Sets the usermail
     *
     * @param string $usermail
     * @return void
     */
    public function setUsermail($usermail): void
    {
        $this->usermail = $usermail;
    }

    /**
     * Returns the captcha
     *
     * @return string $captcha
     */
    public function getCaptcha(): string
    {
        return $this->captcha;
    }

    /**
     * Sets the captcha
     *
     * @param string $captcha
     * @return void
     */
    public function setCaptcha($captcha): void
    {
        $this->captcha = $captcha;
    }

    /**
     * Returns the paramlink
     *
     * @return string $paramlink
     */
    public function getParamlink(): string
    {
        return $this->paramlink;
    }

    /**
     * Sets the paramlink
     *
     * @param string $paramlink
     * @return void
     */
    public function setParamlink($paramlink): void
    {
        $this->paramlink = $paramlink;
    }

    /**
     * Returns the crdate
     *
     * @return int $crdate
     */
    public function getCrdate(): int
    {
        return $this->crdate;
    }

    /**
     * Sets the crdate
     *
     * @param int $crdate
     * @return void
     */
    public function setCrdate($crdate): void
    {
        $this->crdate = $crdate;
    }

    /**
     * Returns the hidden
     *
     * @return int $hidden
     */
    public function getHidden(): int
    {
        return $this->hidden;
    }

    /**
     * Sets the hidden
     *
     * @param int $hidden
     * @return void
     */
    public function setHidden($hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Returns the pageid
     *
     * @return int $pageid
     */
    public function getPageid(): int
    {
        return $this->pageid;
    }

    /**
     * Sets the pageid
     *
     * @param int $pageid
     * @return void
     */
    public function setPageid($pageid): void
    {
        $this->pageid = $pageid;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description): void
    {
        $description = trim($description);

        $threeNewLines = "\r\n\r\n\r\n";
        $twoNewLines = "\r\n\r\n";
        do {
            $description = str_replace($threeNewLines, $twoNewLines, $description);
        } while (strstr($description, $threeNewLines));

        // Decode html tags
        $description = htmlspecialchars($description);
        $description = preg_replace('/(((http(s)?\:\/\/)|(www\.))([^\s]+[^\.\s]+))/', '<a href="http$4://$5$6">$1</a>', $description);

        $this->description = $description;
    }

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects(): void
    {
        $this->childcomment = new ObjectStorage();
    }

    /**
     * Adds a Comment
     *
     * @param Comment $childcomment
     * @return void
     */
    public function addChildcomment(self $childcomment): void
    {
        $this->childcomment->attach($childcomment);
    }

    /**
     * Removes a Comment
     *
     * @param Comment $childcommentToRemove The Comment to be removed
     * @return void
     */
    public function removeChildcomment(self $childcommentToRemove): void
    {
        $this->childcomment->detach($childcommentToRemove);
    }

    /**
     * Returns the childcomment
     *
     * @return ObjectStorage<Comment> $childcomment
     */
    public function getChildcomment()
    {
        return $this->childcomment;
    }

    /**
     * Sets the childcomment
     *
     * @param ObjectStorage<Comment> $childcomment
     * @return void
     */
    public function setChildcomment(ObjectStorage $childcomment): void
    {
        $this->childcomment = $childcomment;
    }

    /**
     * @return bool
     */
    public function getTerms(): bool
    {
        return $this->terms;
    }

    /**
     * @param bool $terms
     * @return void
     */
    public function setTerms($terms): void
    {
        $this->terms = $terms;
    }

    /**
     * Set sys language
     *
     * @param int $sysLanguageUid
     */
    public function setSysLanguageUid($sysLanguageUid): void
    {
        $this->sysLanguageUid = $sysLanguageUid;
        $this->_languageUid = $sysLanguageUid;
    }

    /**
     * Get sys language
     *
     * @return int
     */
    public function getSysLanguageUid(): int
    {
        return $this->_languageUid;
    }

}
