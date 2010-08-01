<?php

class DEC_Email {

    /**
     * layout driven emailer
     *
     * @param unknown_type $template
     * @param unknown_type $to
     * @param unknown_type $from
     * @param unknown_type $subject
     * @param unknown_type $variables
     */
    function __construct($template = null, $to = null, $from = null, $subject = null, $variables = array())
    {
        $config = Zend_Registry::get('config');

        Zend_Loader::loadClass('Zend_Mail');
        Zend_Loader::loadClass('Zend_Mail_Transport_Smtp');
        $smtp_settings = array('auth' => 'login',
                    'username' => $config->smtp_username,
                    'password' => $config->smtp_password,
                    'ssl'  => 'tls',
                    'port' => $config->smtp_port);

        $this->transport = new Zend_Mail_Transport_Smtp($config->smtp_server, $smtp_settings);
        $this->mail = new Zend_Mail('utf-8');

        // a bunch of optional construct thangs.
        if ($template !== null) {
            $this->setTemplate($template);
        }

        if ($to !== null)
        {
            $this->addTo($to);
        }

        if ($from !== null)
        {
            $this->setFrom($from);
        }

        if ($subject !== null)
        {
            $this->setSubject($subject);
        }
        if (is_array($variables))
        {
            foreach ($variables as $key => $value) {
                $this->setVariable($key, $value);
            }
        }
    }

    function setTemplate($template)
    {
        $this->emailTemplate = new Zend_Layout();
        $scriptPath = '../application/layouts/emails';
        $this->emailTemplate->setLayoutPath($scriptPath);
        $this->emailTemplate->setLayout($template);
    }
    function setVariable($name, $value)
    {
        if ($this->emailTemplate === null) {
            throw new Exception('cannot set variables when template is not set');
        }
        $this->emailTemplate->$name = $value;
    }

    function setVariables($variables)
    {
        // set a bunch of variables from array
        if (is_array($variables)) {
            foreach ($variables as $key => $value) {
                $this->emailTemplate->$key = $value;
            }
        }
    }

    function addTo($email)
    {
        if (!is_array($email)) {
            throw new Exception('$email is not an array. $email requires a "name" and "address" key');
        }
        $this->mail->addTo($email['address'], $email['name']);
    }

    function setFrom($email)
    {
        if (!is_array($email)) {
            throw new Exception('$email is not an array. $email requires a "name" and "address" key');
        }
        // send from the submitter
        $this->mail->setFrom($email['address'], $email['name']);
    }

    function setSubject($subject)
    {
        $this->mail->setSubject($subject);
    }

    function sendEmail()
    {
        if ($this->emailTemplate) {
            $message = $this->emailTemplate->render();
        } else {
            $message = '';
        }

        $this->mail->setBodyText(strip_tags($message));
        $this->mail->setBodyHtml($message);
        try {
            $this->mail->send($this->transport);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        // this will throw an exception
    }
}
