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

class iDefend
{

    /** @var string */
    private $tempDir;

    /** @var string */
    private $url = 'https://test.idefend.eu/ws';

    /** @var string */
    private $username;

    /** @var string */
    private $password;


    /**
     * @param string $tempDir
     * @param string|null $url
     */
    public function __construct($tempDir, $url = NULL)
    {
        $this->tempDir = $tempDir;

        if($url !== NULL) {
            $this->url = $url;
        }
    }


    /**
     * @param string $username
     * @param string $password
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function startSession($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $request = $this->request('/user/startSession');

        $response = $request->post(Json::encode([
            'User' => [
                'username' => $this->username,
                'password' => $this->password
            ],
        ]));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data;
    }


    /**
     * @return array
     * @throws CurlException
     * @throws iDefendException
     */
    public function getProducts()
    {
        $request = $this->request('/policy/getProducts');
        $response = $request->post(Json::encode(''));
        
        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data->Product;
    }


    /**
     * @param int $productId
     * @return array
     * @throws CurlException
     * @throws iDefendException
     */
    public function getPaymentTerms($productId)
    {
        $request = $this->request('/policy/getPaymentTerms');
        $response = $request->post(Json::encode([
            'product_id' => $productId,
        ]));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        $data = [];
        foreach($result->data->PaymentTerm as $p) {
            $data[] = [
                'id' => $p,
                'name' => $p
            ];
        }

        return $result->data->PaymentTerm;
    }


    /**
     * @param int $productId
     * @return array
     * @throws CurlException
     * @throws iDefendException
     */
    public function getInsuranceTerms($productId)
    {
        $request = $this->request('/policy/getInsuranceTerms');
        $response = $request->post(Json::encode([
            'product_id' => $productId,
        ]));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        $data = [];
        foreach($result->data->InsuranceTerm as $p) {
            $data[] = [
                'id' => $p,
                'name' => $p
            ];
        }

        return $data;
    }


    /**
     * @return array
     * @throws CurlException
     * @throws iDefendException
     */
    public function getTitles()
    {
        $request = $this->request('/policy/getTitles');
        $response = $request->post(Json::encode(''));
        
        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        $data = [];
        foreach($result->data->Title as $k => $p) {
            $data[] = [
                'id' => $k,
                'name' => $p
            ];
        }

        return $data;
    }


    /**
     * @return array
     * @throws CurlException
     * @throws iDefendException
     */
    public function getExtras()
    {
        $request = $this->request('/policy/getExtras');
        $response = $request->post(Json::encode(''));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data->Extra;
    }


    /**
     * @param $productId
     * @return array
     * @throws CurlException
     * @throws iDefendException
     */
    public function getLoadings($productId)
    {
        $request = $this->request('/policy/getLoadings');
        $response = $request->post(Json::encode([
            'product_id' => $productId,
        ]));
        
        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data->Loading;
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function getCoverages(array $data)
    {
        $request = $this->request('/policy/getCoverages');
        $response = $request->post(Json::encode($data));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result;
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function savePolicy(array $data)
    {
        $request = $this->request('/policy/savePolicy');
        $response = $request->post(Json::encode($data));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result;
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function getPolicy($policyNo)
    {
        $request = $this->request('/policy/getPolicy');
        $response = $request->post(Json::encode([
            'policy_no' => $policyNo,
        ]));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data;
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function deletePolicy($policyNo)
    {
        $request = $this->request('/policy/deletePolicy');
        $response = $request->post(Json::encode([
            'policy_no' => $policyNo,
        ]));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data;
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function getProposal($policyNo)
    {
        $request = $this->request('/policy/getProposal');
        $response = $request->post(Json::encode([
            'policy_no' => $policyNo,
        ]));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data;
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function closeSession()
    {
        $request = $this->request('/user/closeSession');
        $response = $request->post(Json::encode(''));

        $result = Json::decode($response->getResponse());

        if( isset($result->data->error) )
            throw new iDefendException($result->data->error);

        return $result->data;
    }


    /**
     * @param $url
     * @return Request
     */
    private function request($url)
    {
        $request = new Request($this->url . $url);
        $request->setCertificationVerify(FALSE);

        $request->options['COOKIESESSION'] = TRUE;
        $request->options['COOKIEFILE'] = $this->tempDir . '/iDefend.cookie';
        $request->options['COOKIEJAR'] = $this->tempDir . '/iDefend.cookie';

        $request->headers['Content-Type'] = 'application/json';

        return $request;
    }

} 
