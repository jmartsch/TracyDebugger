<?php

use Tracy\ILogger;
use Tracy\Logger;
use Tracy\Dumper;

class SlackLogger implements ILogger {

    public function log($value, $priority = ILogger::INFO) {

        $message = nl2br(Logger::formatMessage($value));

        switch ($priority) {
            case ILogger::DEBUG:
            case ILogger::INFO:
                $icon = ':information_source:';
                break;
            case ILogger::ERROR:
            case ILogger::WARNING:
                $icon = ':warning:';
                break;
            case ILogger::EXCEPTION:
            case ILogger::CRITICAL:
                $icon = ':octagonal_sign:';
            break;
            default:
                $icon = '';
                break;
        }

        $this->message($icon . ' ' . $message);
    }

    protected $endpointUrl = 'https://slack.com/api/';
    protected $endPoint;
    protected $query = [];

    public function message($message) {

        $this->endpoint = 'chat.postMessage';

        $this->query = [
            "token" => \TracyDebugger::getDataValue('slackAppOauthToken'),
            "text" => $message,
            "channel" => \TracyDebugger::getDataValue('slackChannel'),
            "username" => 'TracyDebugger',
            "icon_emoji" => ':beetle:',
        ];

        return $this->send();
    }

    private function send() {
        $ch = curl_init($this->endpointUrl . $this->endpoint);
        $data = http_build_query($this->query);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


}

Tracy\Debugger::setLogger(new SlackLogger);
