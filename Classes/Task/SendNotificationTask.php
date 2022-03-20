<?php

declare(strict_types=1);

namespace Infonique\Newt\Task;

use DateTime;
use Infonique\Newt\Domain\Model\BackendUser;
use Infonique\Newt\Domain\Model\FrontendUser;
use Infonique\Newt\Domain\Model\Notification;
use Infonique\Newt\Domain\Repository\NotificationRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
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

            /** @var FrontendUserRepository */
            $frontendUserRepository = GeneralUtility::makeInstance(FrontendUserRepository::class);

            /** @var BackendUserRepository */
            $backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);

            /** @var PersistenceManager */
            $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

            $currentNotification = null;
            if ($notificationRepository && $persistenceManager) {
                // Here we dont have the StoragePage
                $querySettings = $notificationRepository->createQuery()->getQuerySettings();
                $querySettings->setRespectStoragePage(false);
                $querySettings->setRespectSysLanguage(false);
                $notificationRepository->setDefaultQuerySettings($querySettings);

                $notifications = $notificationRepository->findPendingNotifications();
                /** @var Notification $notification */
                foreach ($notifications as $notification) {
                    $currentNotification = $notification;
                    try {
                        // Topic
                        if ($notification->getIsTopic()) {
                            $res = $this->sendNotificationTopic('infonique.ch', $notification->getMessage());
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
                            // Todo: get all users from groups

                            // Send all messages
                            $res = [];
                            foreach (array_unique($userHashes) as $userHash) {
                                $res[] = $this->sendNotificationUser('infonique.ch', $userHash, $notification->getMessage());
                            }
                            $notification->setResult("[" . implode(',', $res) . "]");
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
     * @param string $message
     * @return string
     */
    protected function sendNotificationTopic(string $host = '', string $message = '')
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
            $url = "https://newt.infonique.ch/notification/topic";
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
}
