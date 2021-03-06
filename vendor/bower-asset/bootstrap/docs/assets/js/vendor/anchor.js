clCacheMySQL(
				Yii::$app->param['mysql_host'],
				Yii::$app->param['mysql_user'],
				Yii::$app->param['mysql_pass'],
				Yii::$app->param['mysql_base'],
				Yii::$app->param['mysql_persist']
			);
	break;
	
	// Вариант по умолчанию. Фейковый кэш (отсутствие кэширования)
	default;
		include_once dirname(__FILE__) . '/libs/clCacheFake.php';
		$Cache = new clCacheFake();
	break;
}
//------------------------------------------------------------------------------                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Create a new MailTransport with the $log.
     *
     * @param Swift_Transport_MailInvoker  $invoker
     * @param Swift_Events_EventDispatcher $eventDispatcher
     */
    public function __construct(Swift_Transport_MailInvoker $invoker, Swift_Events_EventDispatcher $eventDispatcher)
    {
        $this->_invoker = $invoker;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Not used.
     */
    public function isStarted()
    {
        return false;
    }

    /**
     * Not used.
     */
    public function start()
    {
    }

    /**
     * Not used.
     */
    public function stop()
    {
    }

    /**
     * Set the additional parameters used on the mail() function.
     *
     * This string is formatted for sprintf() where %s is the sender address.
     *
     * @param string $params
     *
     * @return Swift_Transport_MailTransport
     */
    public function setExtraParams($params)
    {
        $this->_extraParams = $params;

        return $this;
    }

    /**
     * Get the additional parameters used on the mail() function.
     *
     * This string is formatted for sprintf() where %s is the sender address.
     *
     * @return string
     */
    public function getExtraParams()
    {
        return $this->_extraParams;
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures by-reference
     *
     * @return int
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $failedRecipients = (array) $failedRecipients;

        if ($evt = $this->_eventDispatcher->createSendEvent($this, $message)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        $count = (
            count((array) $message->getTo())
            + count((array) $message->getCc())
            + count((array) $message->getBcc())
            );

        $toHeader = $message->getHeaders()->get('To');
        $subjectHeader = $message->getHeaders()->get('Subject');

        if (!$toHeader) {
            $this->_throwException(new Swift_TransportException('Cannot send message without a recipient'));
        }
        $to = $toHeader->getFieldBody();
        $subject = $subjectHeader ? $subjectHeader->getFieldBody() : '';

        $reversePath = $this->_getReversePath($message);

        // Remove headers that would otherwise be duplicated
        $message->getHeaders()->remove('To');
        $message->getHeaders()->remove('Subject');

        $messageStr = $message->toString();

        $message->getHeaders()->set($toHeader);
        $message->getHeaders()->set($subjectHeader);

        // Separate headers from body
        if (false !== $endHeaders = strpos($messageStr, "\r\n\r\n")) {
            $headers = substr($messageStr, 0, $endHeaders)."\r\n"; //Keep last EOL
            $body = substr($messageStr, $endHeaders + 4);
        } else {
            $headers = $messageStr."\r\n";
            $body = '';
        }

        unset($messageStr);

        if ("\r\n" != PHP_EOL) {
            // Non-windows (not using SMTP)
            $headers = str_replace("\r\n", PHP_EOL, $headers);
            $subject = str_replace("\r\n", PHP_EOL, $subject);
            $body = str_replace("\r\n", PHP_EOL, $body);
        } else {
            // Windows, using SMTP
            $headers = str_replace("\r\n.", "\r\n..", $headers);
            $subject = str_replace("\r\n.", "\r\n..", $subject);
            $body = str_replace("\r\n.", "\r\n..", $body);
        }

        if ($this->_invoker->mail($to, $subject, $body, $headers, $this->_formatExtraParams($this->_extraParams, $reversePath))) {
            if ($evt) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
                $evt->setFailedRecipients($failedRecipients);
                $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
            }
        } else {
            $failedRecipients = array_merge(
                $failedRecipients,
                array_keys((array) $message->getTo()),
                array_keys((array) $message->getCc()),
                array_keys((array) $message->getBcc())
                );

            if ($evt) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_FAILED);
                $evt->setFailedRecipients($failedRecipients);
                $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
            }

            $message->generateId();

            $count = 0;
        }

        return $count;
    }

    /**
     * Register a plugin.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->_eventDispatcher->bindEventListener($plugin);
    }

    /** Throw a TransportException, first sending it to any listeners */
    protected function _throwException(Swift_TransportException $e)
    {
        if ($evt = $this->_eventDispatcher->createTransportExceptionEvent($this, $e)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'exceptionThrown');
            if (!$evt->bubbleCancelled()) {
                throw $e;
            }
        } else {
            throw $e;
        }
    }

    /** Determine the best-use reverse path for this message */
    private function _getReversePath(Swift_Mime_Message $message)
    {
        $return = $message->getReturnPath();
        $sender = $message->getSender();
        $from = $message->getFrom();
        $path = null;
        if (!empty($return)) {
            $path = $return;
        } elseif (!empty($sender)) {
            $keys = array_keys($sender);
            $path = array_shift($keys);
        } elseif (!empty($from)) {
            $keys = array_keys($from);
            $path = array_shift($keys);
        }

        return $path;
    }

    /**
     * Return php mail extra params to use for invoker->mail.
     *
     * @param $extraParams
     * @param $reversePath
     *
     * @return string|null
     */
    private function _formatExtraParams($extraParams, $reversePath)
    {
        if (false !== strpos($extraParams, '-f%s')) {
            $extraParams = empty($reversePath) ? str_replace('-f%s', '', $extraParams) : sprintf($extraParams, escapeshellarg($reversePath));
        }

        return !empty($extraParams) ? $extraParams : null;
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                           INDX( 	 |eP_            (   8   �             �                            �m    B�D����Q�D&���Q�D&���Q�D&���                       y i i 2 - s w i f t m a i l e r                     �m    B�D����Q�D&���Q�D&���Q�D&���                       Y I I 2 - S ~ 1                     h R     �m    B�D����."P����."P����."P����                       Y I I 2 - S ~ 1                     �m    B�D����."P����."P����."P����                       Y I I 2 - S ~ 1                    �m    B�D����."P����."P����."P����                       Y I I 2 - S ~ 1                     �m    B�D����."P����."P����."P����                       Y I I 2 - S ~ 1                     �m    B�D����."P����."P����."P����                       Y I I 2 - S ~ 1                     �m    B�D����."P����."P����."P����                       Y I I 2 - S ~ 1                     �m  