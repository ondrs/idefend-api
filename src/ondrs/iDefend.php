<?php
/**
 * Created by PhpStorm.
 * User: Ondra
 * Date: 22.2.14
 * Time: 11:11
 */

namespace ondrs\iDefendApi;


use Kdyby\Curl\CurlException;
use Kdyby\Curl\Request;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class iDefend
{

    /** @var string */
    private $url = 'https://test.idefend.eu/ws';

    /** @var string */
    private $username;

    /** @var string */
    private $password;


    /**
     * @param string|null $url
     */
    public function __construct($url = NULL)
    {
        if($url !== NULL) {
            $this->url = $url;
        }
    }


    /**
     * @param string $username
     * @param string $password
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     * @throws iDefendException
     */
    public function startSession($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $request = $this->request('/user/startSession');

        $response = $request->post(Json::encode([
            'username' => $this->username,
            'password' => $this->password,
        ]));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result;
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getProducts()
    {
        $request = $this->request('/policy/getProducts');
        $response = $request->post(Json::encode(''));
        return Json::decode($response->getResponse());
    }


    /**
     * @param int $productId
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getPaymentTerms($productId)
    {
        $request = $this->request('/policy/getPaymentTerms');
        $response = $request->post(Json::encode([
            'product_id' => $productId,
        ]));
        return Json::decode($response->getResponse());
    }


    /**
     * @param int $productId
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getInsuranceTerms($productId)
    {
        $request = $this->request('/policy/getInsuranceTerms');
        $response = $request->post(Json::encode([
            'product_id' => $productId,
        ]));
        return Json::decode($response->getResponse());
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getTitles()
    {
        $request = $this->request('/policy/getTitles');
        $response = $request->post(Json::encode(''));
        return Json::decode($response->getResponse());
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getExtras()
    {
        $request = $this->request('/policy/getExtras');
        $response = $request->post(Json::encode(''));
        return Json::decode($response->getResponse());
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getLoadings()
    {
        $request = $this->request('/policy/getLoadings');
        $response = $request->post(Json::encode(''));
        return Json::decode($response->getResponse());
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getCoverages(array $data)
    {
        $request = $this->request('/policy/getCoverages');
        $response = $request->post(Json::encode($data));
        return Json::decode($response->getResponse());
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function savePolicy(array $data)
    {
        $request = $this->request('/policy/savePolicy');
        $response = $request->post(Json::encode($data));
        return Json::decode($response->getResponse());
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getPolicy($policyNo)
    {
        $request = $this->request('/policy/getPolicy');
        $response = $request->post(Json::encode([
            'policy_no' => $policyNo,
        ]));
        return Json::decode($response->getResponse());
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function deletePolicy($policyNo)
    {
        $request = $this->request('/policy/deletePolicy');
        $response = $request->post(Json::encode([
            'policy_no' => $policyNo,
        ]));
        return Json::decode($response->getResponse());
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function getProposal($policyNo)
    {
        $request = $this->request('/policy/getProposal');
        $response = $request->post(Json::encode([
            'policy_no' => $policyNo,
        ]));
        return Json::decode($response->getResponse());
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws JsonException
     */
    public function closeSession()
    {
        $request = $this->request('/user/closeSession');
        $response = $request->post(Json::encode(''));
        return Json::decode($response->getResponse());
    }


    /**
     * @param $url
     * @return Request
     */
    private function request($url)
    {
        $request = new Request($this->url . $url);
        $request->setCertificationVerify(FALSE);

        $request->options['httpHeader'] = [
            'Content-Type: application/json',
        ];

        return $request;
    }

} 
