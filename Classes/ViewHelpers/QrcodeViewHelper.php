<?php

namespace Swisscode\Newt\ViewHelpers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class QrcodeViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'Content of QrCode', true, '');
    }

    /**
     * @return string
     */
    public function render()
    {
        $content = $this->arguments['content'];

        $options = new QROptions([
            'imageTransparent' => false,
        ]);
        $qrcode = new QRCode($options);

        return $qrcode->render($content);
    }
}
