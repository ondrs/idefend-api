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

        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir);
        }

        if ($url !== NULL) {
            $this->url = $url;
        }
    }


    /**
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function startSession()
    {
        $request = $this->request('/user/startSession');

        $response = $request->post(Json::encode([
            'User' => [
                'username' => $this->username,
                'password' => $this->password
            ],
        ]));

        $result = Json::decode($response->getResponse());

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

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

        if (isset($result->data->error))
            throw new iDefendException($result->data->error);

        $data = [];
        foreach ($result->data->PaymentTerm as $p) {
            $data[] = [
                'id' => $p,
                'name' => $p
            ];
        }

        return $data;
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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

        $data = [];
        foreach ($result->data->InsuranceTerm as $p) {
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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

        $data = [];
        foreach ($result->data->Title as $k => $p) {
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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

        return $result->data;
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

        if (isset($result->data->error)) {
            $err = $result->data->error;

            if (is_object($err)) {
                $msg = [];

                foreach ($err as $k => $e) {
                    $msg[] = $k . ': ' . (is_array($e) ? join(', ', $e) : $e);
                }

                throw new iDefendException(join('; ', $msg));
            } else {
                throw new iDefendException($err);
            }
        }

        return $result->data;
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function saveQuote(array $data)
    {
        $request = $this->request('/policy/saveQuote');
        $response = $request->post(Json::encode($data));

        $result = Json::decode($response->getResponse());

        if (isset($result->data->error)) {
            $err = $result->data->error;

            if (is_object($err)) {
                $msg = [];

                foreach ($err as $k => $e) {
                    $msg[] = $k . ': ' . (is_array($e) ? join(', ', $e) : $e);
                }

                throw new iDefendException(join('; ', $msg));
            } else {
                throw new iDefendException($err);
            }
        }

        return $result->data;
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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

        return $result->data;
    }


    /**
     * @param int $page
     * @param int $limit
     * @param null|array $conditions
     * @param null|array $fields
     * @return mixed
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function getPolicyList($page = 1, $limit = 30, $conditions = NULL, $fields = NULL)
    {
        $request = $this->request('/policy/getPolicyList');

        if ($fields === NULL) {
            $fields = [
                'id',
                'policy_no',
                'vehicle_vin',
                'premium',
                'created',
                'status_opened_on',
                'status_certified_on',
                'status_canceled_on',
                'status_paid_on',
                'customer_first_name',
                'customer_last_name',
                'customer_company_name',
                'customer_id_no'
            ];
        }

        $data = [
            'page' => $page,
            'limit' => $limit,
            'fields' => $fields,
        ];

        if ($conditions !== NULL) {
            $data['conditions'] = $conditions;
        }

        $response = $request->post(Json::encode($data));

        $result = Json::decode($response->getResponse());

        if (isset($result->data->error))
            throw new iDefendException($result->data->error);

        $return = new \stdClass();

        $return->Paging = $result->data->Paging;
        unset($result->data->Paging);

        $return->data = [];
        foreach ($result->data as $policy) {
            $return->data[] = $policy->Policy;
        }

        return $return;
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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

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

        $body = $response->getResponse();

        if ($response->getHeaders()['Content-Type'] == 'application/pdf') {
            $filename = $this->tempDir . '/' . $policyNo . '-' . md5($body) . '.pdf';
            file_put_contents($filename, $body);
            return $filename;
        }

        $json = Json::decode($body);

        if (isset($json->data->error)) {
            throw new iDefendException($json->data->error);
        }

        throw new iDefendException("Wrong response - no PDF or error");
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function getQuote($policyNo)
    {
        $request = $this->request('/policy/getQuote');
        $response = $request->post(Json::encode([
            'policy_no' => $policyNo,
        ]));

        $body = $response->getResponse();

        if ($response->getHeaders()['Content-Type'] == 'application/pdf') {
            $filename = $this->tempDir . '/' . $policyNo . '-' . md5($body) . '.pdf';
            file_put_contents($filename, $body);
            return $filename;
        }

        $json = Json::decode($body);

        if (isset($json->data->error)) {
            throw new iDefendException($json->data->error);
        }

        throw new iDefendException("Wrong response - no PDF or error");
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

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

        return $result->data;
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function getCancellationCodificators()
    {
        $request = $this->request('/user/getCancellationCodificators');
        $response = $request->post(Json::encode(''));

        $result = Json::decode($response->getResponse());

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

        return $result->data;
    }


    /**
     * @param array $data
     * @return mixed
     * @throws iDefendException
     */
    public function cancelPolicy(array $data)
    {
        $request = $this->request('/user/cancelPolicy');
        $response = $request->post(Json::encode($data));

        $result = Json::decode($response->getResponse());

        if (isset($result->data->error)) {
            throw new iDefendException($result->data->error);
        }

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

        $file = $this->tempDir . '/' . $this->username . '.cookie';

        $request->options['cookieSession'] = TRUE;
        $request->options['cookieFile'] = $file;
        $request->options['cookieJar'] = $file;

        $request->headers['Content-Type'] = 'application/json';

        return $request;
    }

} 
