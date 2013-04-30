# YiiMailer is a mail extensions based on Yii Framework

## Requires
* PHP >= 5
* fsockopen function or configuration php mail

## Configuration
```
'components' => array(
    'mailer' => array(
        // for smtp
        'class' => 'ext.mailer.SmtpMailer',
        'server' => 'smtp.163.com',
        'port' => '25',
        'username' => 'your username',
        'password' => 'your password',

        // for php mail
        'class' => 'ext.mailer.PhpMailer',
    ),
)
```

## Usage
```
$to = array(
    'somemail@gmail.com',
);

// or

$to = 'somemail@gmail.com';

$subject = 'Hello Mailer';
$content = 'Some content';

Yii::app()->mailer->send($to, $subject, $content);
```
