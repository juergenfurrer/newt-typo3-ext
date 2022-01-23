<?php

declare(strict_types=1);

namespace Infonique\Newt\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class UserData
{
    private string $type = "BE";

    private int $uid = 0;

    private array $usergroups = [];

    private bool $isAdmin = false;

    private string $token = '';

    private ?\DateTime $tokenIssued = null;


    public function __construct($data = [])
    {
        if (isset($data['uid'])) {
            $this->uid = intval($data['uid']);
        }
        if (isset($data['usergroup'])) {
            $this->usergroups = GeneralUtility::intExplode(",", $data['usergroup']);
        }
        if (isset($data['admin'])) {
            $this->isAdmin = boolval($data['admin']);
        }
        if (isset($data['tx_newt_token'])) {
            $this->token = $data['tx_newt_token'];
        }
        if (isset($data['tx_newt_token_issued'])) {
            $timeZoneUtc = new \DateTimeZone('UTC');
            $timeZoneDefault = date_default_timezone_get();

            $tokenIssuedDate = new \DateTime('@' . $data['tx_newt_token_issued'], $timeZoneUtc);
            $tokenIssuedDate->setTimezone(new \DateTimeZone($timeZoneDefault));

            $this->tokenIssued = $tokenIssuedDate;
        }
    }


    /**
     * Get the value of type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of uid
     *
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * Set the value of uid
     *
     * @param int $uid
     *
     * @return self
     */
    public function setUid(int $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get the value of usergroups
     *
     * @return array
     */
    public function getUsergroups(): array
    {
        return $this->usergroups;
    }

    /**
     * Set the value of usergroups
     *
     * @param array $usergroups
     *
     * @return self
     */
    public function setUsergroups(array $usergroups): self
    {
        $this->usergroups = $usergroups;
        return $this;
    }

    /**
     * Get the value of isAdmin
     *
     * @return bool
     */
    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * Set the value of isAdmin
     *
     * @param bool $isAdmin
     *
     * @return self
     */
    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    /**
     * Get the value of token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @param string $token
     *
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get the value of tokenIssued
     *
     * @return \DateTime
     */
    public function getTokenIssued(): ?\DateTime
    {
        return $this->tokenIssued;
    }

    /**
     * Set the value of tokenIssued
     *
     * @param \DateTime $tokenIssued
     *
     * @return self
     */
    public function setTokenIssued(\DateTime $tokenIssued): self
    {
        $this->tokenIssued = $tokenIssued;
        return $this;
    }
}
