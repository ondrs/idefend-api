<?php

namespace ondrs\iDefendApi;


use Kdyby\Curl\CurlException;
use Kdyby\Curl\CurlSender;
use Kdyby\Curl\Request;
use Nette\Utils\Json;

class Sender
{

    /** @var \Kdyby\Curl\CurlSender */
    private $curlSender;

    /** @var string */
    private $url = 'https://www.idefend.eu/ws';


    /**
     * @param string $url
     * @param CurlSender $curlSender
     */
    public function __construct($url = NULL, CurlSender $curlSender = NULL)
    {
        $this->curlSender = $curlSender;

        if ($this->curlSender === NULL) {
            $this->curlSender = new CurlSender;
        }

        if ($url !== NULL) {
            $this->url = rtrim($url, '/');
        }
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * @param string $cookieFileName
     * @return $this
     */
    public function setup($cookieFileName)
    {
        $this->curlSender->setCertificationVerify(FALSE);

        $this->curlSender->options['cookieSession'] = TRUE;
        $this->curlSender->options['cookieFile'] = $cookieFileName;
        $this->curlSender->options['cookieJar'] = $cookieFileName;

        $this->curlSender->headers['Content-Type'] = 'application/json';

        return $this;
    }


    /**
     * @param string $url
     * @param string|array $data
     * @return \Kdyby\Curl\Response
     * @throws iDefendCurlException
     */
    public function send($url, $data = '')
    {
        try {
            $request = new Request($this->url . $url);
            $request->setSender($this->curlSender);

            return $request->post(Json::encode($data));

        } catch (CurlException $e) {
            throw new iDefendCurlException($e->getMessage());
        }
    }

} 
