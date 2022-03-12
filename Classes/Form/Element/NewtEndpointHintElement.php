<?php
declare(strict_types = 1);
namespace Infonique\Newt\Form\Element;

use Infonique\Newt\NewtApi\EndpointInterface;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class NewtEndpointHintElement extends AbstractFormElement
{
    private string $languageFile = 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:';

    public function render()
    {
        $message = '';

        $endpointClass = reset($this->data['databaseRow']['endpoint_class']);
        if (! empty($endpointClass)) {
            $endpoint = GeneralUtility::makeInstance($endpointClass);
            $labels = [];
            if ($endpoint instanceof EndpointInterface) {
                foreach ($endpoint->getAvailableMethodTypes() as $method) {
                    $label = $GLOBALS['LANG']->sL($this->languageFile . 'tx_newt_domain_model_endpoint.method.' . $method);
                    if (!empty($label)) {
                        $labels[] = $label;
                    }
                }
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
                if (count($labels) > 0) {
                    $message .= '<h4 class="alert-title">' . $GLOBALS['LANG']->sL($this->languageFile . 'tx_newt_domain_model_endpoint.methods_available') . '</h4>';
                    $message .= '<p class="alert-message">' . implode(', ', $labels) . '</p>';
                } else {
                    $message .= '<h4 class="alert-title">' . $GLOBALS['LANG']->sL($this->languageFile . 'tx_newt_domain_model_endpoint.methods_available_none') . '</h4>';
                }
                $message .= '</div>';
                $message .= '</div>';
                $message .= '</div>';
                $message .= '</div>';
            }
        }

        $result = $this->initializeResultArray();
        $result['html'] = $message;

        return $result;
    }
}
