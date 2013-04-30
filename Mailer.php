<?php
/**
 * A mail extension for yii
 *
 * @link      http://github.com/tlikai/YiiMailer
 * @author    likai<youyuge@gmail.com>
 * @license   http://www.youyuge.com/license New BSD License
 */

abstract class Mailer extends CApplicationComponent
{
    /**
     * CRLF
     *
     * @var string
     */
    public $crlf;

    public function init()
    {
        parent::init();

        if(defined('PHP_EOL'))
            $this->crlf = PHP_EOL;
        else
            $this->crlf = !strpos(PHP_OS, 'WIN') ? "\n" : "\r\n";
    }

    public function getHeaders()
    {
        return array(
            'X-Priority: 3',
            'X-Mailer: Yii mailer',
            'MIME-Version: 1.0',
            'Content-type: text/plain; charset=' . Yii::app()->charset,
        );
    }

    abstract public function send($to, $subject, $message);
}
