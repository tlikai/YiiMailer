<?php
/**
 * A email extension for yii
 *
 * Based on php mail configure
 *
 * @link      http://github.com/tlikai/YiiMailer
 * @author    likai<youyuge@gmail.com>
 * @license   http://www.youyuge.com/license New BSD License
 */

require dirname(__FILE__) . DIRECTORY_SEPARATOR .  'Mailer.php';

class PhpMailer extends Mailer
{
    public function send($to, $subject, $message)
    {
        $to = is_array($to) ? implode(';', $to) : $to;
        $headers = implode($this->crlf, $this->headers);
        return mail($to, $subject, $message, $headers);
    }
}
