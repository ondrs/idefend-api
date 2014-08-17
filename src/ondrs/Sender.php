<?php
/**
 * Created by PhpStorm.
 * User: Ondra
 * Date: 17.8.14
 * Time: 11:37
 */

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
    private $url = 'https://test.idefend.eu/ws';


    /**
     * @param null $url
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
     * @param $cookieFileName
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
     * @param $url
     * @param string $data
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
