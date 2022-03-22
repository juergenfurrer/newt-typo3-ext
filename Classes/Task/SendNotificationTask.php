<?php

declare(strict_types=1);

namespace Infonique\Newt\Task;

use DateTime;
use Infonique\Newt\Domain\Model\BackendUser;
use Infonique\Newt\Domain\Model\FrontendUser;
use Infonique\Newt\Domain\Model\FrontendUserGroup;
use Infonique\Newt\Domain\Model\Notification;
use Infonique\Newt\Domain\Repository\BackendUserRepository;
use Infonique\Newt\Domain\Repository\FrontendUserRepository;
use Infonique\Newt\Domain\Repository\NotificationRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class SendNotificationTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{
    /**
     * Execute the task
     *
     * @return boolean
     */
    public function execute()
    {
        try {
            /** @var NotificationRepository */
            $notificationRepository = GeneralUtility::makeInstance(NotificationRepository::class);
            $notificationRepository->setDetachedQuerySettings();

            /** @var FrontendUserRepository */
            $frontendUserRepository = GeneralUtility::makeInstance(FrontendUserRepository::class);
            $frontendUserRepository->setDetachedQuerySettings();

            /** @var BackendUserRepository */
            $backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);
            $backendUserRepository->setDetachedQuerySettings();

            /** @var PersistenceManager */
            $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

            $currentNotification = null;
            if ($notificationRepository && $persistenceManager) {
                $notifications = $notificationRepository->findPendingNotifications();
                /** @var Notification $notification */
                foreach ($notifications as $notification) {
                    $currentNotification = $notification;
                    try {
                        /** @var ConfigurationManager */
                        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
                        $conf = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
                        $settings = $conf['plugin.']['tx_newt.']['settings.'] ?? [];
                        $host = $settings['serverTopic'];
                        // Topic
                        if ($notification->getIsTopic()) {
                            $res = $this->sendNotificationTopic($host, $host, $notification->getMessage());
                            $notification->setResult($res);
                            $notification->setResultDatetime(new DateTime());
                        } else {
                            $userHashes = [];
                            /** @var BackendUser $beuser */
                            foreach ($notification->getBeusers() as $beuser) {
                                $backendUser = $backendUserRepository->findByUid($beuser->getUid());
                                if ($backendUser) {
                                    $userHashes[] = md5($backendUser->getUserName());
                                }
                            }
                            /** @var FrontendUser $feuser */
                            foreach ($notification->getFeusers() as $feuser) {
                                $frontendUser = $frontendUserRepository->findByUid($feuser->getUid());
                                if ($frontendUser) {
                                    $userHashes[] = md5($frontendUser->getUserName());
                                }
                            }
                            /** @var BackendUserGroup $beusergroup */
                            foreach ($notification->getBeusergroups() as $beusergroup) {
                                $backendUsers = $backendUserRepository->findByUsergroupId($beusergroup->getUid());
                                foreach ($backendUsers as $backendUser) {
                                    $userHashes[] = md5($backendUser->getUserName());
                                }
                            }
                            /** @var FrontendUserGroup $feusergroup */
                            foreach ($notification->getFeusergroups() as $feusergroup) {
                                $frontendUsers = $frontendUserRepository->findByUsergroupId($feusergroup->getUid());
                                foreach ($frontendUsers as $frontendUser) {
                                    $userHashes[] = md5($frontendUser->getUserName());
                                }
                            }
                            $res = $this->sendNotificationUsers($host, array_unique($userHashes), $notification->getMessage());
                            $notification->setResult($res);
                            $notification->setResultDatetime(new DateTime());
                        }
                    } catch (\Exception $ex) {
                        $notification->setResult($ex->getMessage());
                        $notification->setResultDatetime(new DateTime());
                    }
                    $notificationRepository->update($notification);
                    $persistenceManager->persistAll();
                    $currentNotification = null;
                }
                return true;
            }
        } catch (\Exception $e) {
            if ($currentNotification) {
                $currentNotification->setResult($e->getMessage());
                $currentNotification->setResultDatetime(new DateTime());
                $notificationRepository->update($currentNotification);
                $persistenceManager->persistAll();
            }
        }
        return false;
    }

    /**
     * Send notification to topic
     *
     * @param string $host
     * @param string $topic
     * @param string $message
     * @return string
     */
    protected function sendNotificationTopic(string $host = '', string $topic = '', string $message = '')
    {
        /** @var ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $conf = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $settings = $conf['plugin.']['tx_newt.']['settings.'] ?? [];

        if (isset($settings['serverSecret']) && ! empty($settings['serverSecret'])) {
            $url = "https://newt.infonique.ch/notification/topic";
            $fields = [
                'host' => $host,
                'secret' => $settings['serverSecret'],
                'topic' => $topic,
                'message' => $message,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }

        return "serverSecret missing";
    }

    /**
     * Send notification to an user
     *
     * @param string $host
     * @param string $username
     * @param string $message
     * @return string
     */
    protected function sendNotificationUser(string $host = '', string $username = '', string $message = '')
    {
        /** @var ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $conf = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $settings = $conf['plugin.']['tx_newt.']['settings.'] ?? [];

        if (isset($settings['serverSecret']) && ! empty($settings['serverSecret'])) {
            $url = "https://newt.infonique.ch/notification/user";
            $fields = [
                'host' => $host,
                'secret' => $settings['serverSecret'],
                'user' => $username,
                'message' => $message,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }

        return "serverSecret missing";
    }

    /**
     * Send notification to users
     *
     * @param string $host
     * @param array $usernames
     * @param string $message
     * @return string
     */
    protected function sendNotificationUsers(string $host = '', array $usernames = [], string $message = '')
    {
        /** @var ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $conf = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $settings = $conf['plugin.']['tx_newt.']['settings.'] ?? [];

        if (isset($settings['serverSecret']) && ! empty($settings['serverSecret'])) {
            $url = "https://newt.infonique.ch/notification/user";
            $fields = [
                'host' => $host,
                'secret' => $settings['serverSecret'],
                'message' => $message,
            ];
            foreach ($usernames as $key => $username) {
                $fields["users[".strval($key)."]"] = $username;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }

        return "serverSecret missing";
    }
}
