<?php
declare(strict_types = 1);
namespace Infonique\Newt\Form\Element;

use Infonique\Newt\NewtApi\EndpointOptionsInterface;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class NewtEndpointOptionsHintElement extends AbstractFormElement
{
    private string $languageFile = 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:';

    public function render()
    {
        $message = '';

        $endpointClass = reset($this->data['databaseRow']['endpoint_class']);
        if (! empty($endpointClass)) {
            $endpoint = GeneralUtility::makeInstance($endpointClass);
            $message .= '<div class="typo3-messages">';
            $message .= '<div class="alert alert-info">';
            $message .= '<div class="media">';
            $message .= '<div class="media-left">';
            $message .= '<span class="fa-stack fa-lg">';
            $message .= '<i class="fa fa-circle fa-stack-2x"></i>';
            $message .= '<i class="fa fa-info fa-stack-1x"></i>';
            $message .= '</span>';
            $message .= '</div>';
            $message .= '<div class="media-body">';
            if ($endpoint instanceof EndpointOptionsInterface) {
                $hint = $endpoint->getEndpointOptionsHint();
                $message .= '<h4 class="alert-title">' . $GLOBALS['LANG']->sL($this->languageFile . 'tx_newt_domain_model_endpoint.options_hint') . '</h4>';
                $message .= '<p class="alert-message">' . nl2br(trim($hint)) . '</p>';
            } else {
                $message .= '<h4 class="alert-title">' . $GLOBALS['LANG']->sL($this->languageFile . 'tx_newt_domain_model_endpoint.options_hint_none') . '</h4>';
            }
            $message .= '</div>';
            $message .= '</div>';
            $message .= '</div>';
            $message .= '</div>';
        }

        $result = $this->initializeResultArray();
        $result['html'] = $message;

        return $result;
    }
}
