<?php
/**
 * A mail extension for yii
 *
 * Based on smtp protocol
 *
 * @link      http://github.com/tlikai/teaconf
 * @author    likai<youyuge@gmail.com>
 * @license   http://www.teaconf.com/license New BSD License
 */

require dirname(__FILE__) . DIRECTORY_SEPARATOR .  'Mailer.php';

class SmtpMailer extends Mailer
{
    /**
     * Smtp server address
     *
     * @var string
     */
    public $server;

    /**
     * Smtp server port
     *
     * @var integer
     */
    public $port = 25;

    /**
     * Connecting timeout
     *
     * @var integer
     */
    public $timeout = 3;

    /**
     * Say hello command
     * 
     * @var string
     */
    public $hello = 'EHLO';

    /**
     * auth username
     *
     * @var string
     */
    public $username;

    /**
     * auth password
     *
     * @var string
     */
    public $password;

    /**
     * smtp server return code
     * 
     * @var integer
     */
    public $code;

    /**
     * smtp server return data
     * 
     * @var integer
     */
    public $return;

    /**
     * fsockopen handle
     * 
     * @var resource
     */
    private $_fp;

    /**
     * init fsockopen connect
     */
    public function init()
    {
        parent::init();

        $this->_fp = fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        if(!$this->_fp)
            throw new CException('Connect to smtp server failed: ' . $errstr);

        $this->hello();
        $this->authenticate();
    }

    /**
     * send mail
     *
     * @param mixed $to 
     * @param string $subject
     * @param string $message
     * 
     * @return boolean
     */
    public function send($to, $subject, $message)
    {
        $to = is_array($to) ? $to : array($to);
        $message = str_replace("\r\n", "\n", $message);

        $code = $this->put("MAIL FROM:<{$this->username}>");
        if($code != 250 && $code != 235)
            throw new CException($this->return);

        foreach($to as $email)
        {
            $code = $this->put("RCPT TO:<{$email}>");
            if($code != 250)
                throw new CException($this->return);
        }

        $code = $this->put("DATA");
        if($code != 334 && $code != 250)
            throw new CException($this->return);

        $output = '';
        foreach($to as $email)
            $output .= "To: {$email}{$this->crlf}";
        $output .= "Date: " . gmdate('r') . $this->crlf;
        $output .= "From: {$this->username}{$this->crlf}";
        $output .= "Subject: {$subject}{$this->crlf}";
        foreach($this->headers as $header)
            $output .= $header . $this->crlf;
        $output .= $this->crlf . $this->crlf;
        $output .= $message;
        $output .= $this->crlf . ".";
        $code = $this->put($output);

        fclose($this->_fp);
        if($code != 250)
            return false;
        return true;
    }

    /**
     * say hello
     */
    protected function hello()
    {
        $auth = strtoupper($this->hello) == 'EHLO' ? 'EHLO' : 'HELO';
        $code = $this->put("{$auth} {$_SERVER['HTTP_HOST']}");
        if($code != 220)
            throw new CException($this->return);
    }

    /**
     * authenticate
     */
    protected function authenticate()
    {
        $code = $this->put("AUTH LOGIN");
        if($code != 250)
            throw new CException($data);

        $code = $this->put(base64_encode($this->username));
        if($code != 250 && $code != 334)
            throw new CException($this->return);

        $code = $this->put(base64_encode($this->password));
        if($code != 250 && $code != 334)
            throw new CException($this->return);
    }

    /**
     * put a command to smtp server
     *
     * @param string $cmd
     *
     * @return integer status code
     */
    protected function put($cmd)
    {
        fputs($this->_fp, $cmd . $this->crlf);

        $this->return = '';
        while($line = fgets($this->_fp, 128))
        {
            $this->return .= $line;
            if(trim(substr($line, 3, 1)) == '')
                break;
        }
        $this->code = substr($this->return, 0 , 3);

        return $this->code;
    }
}
