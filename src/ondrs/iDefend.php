<?php
/**
 * Created by PhpStorm.
 * User: Ondra
 * Date: 22.2.14
 * Time: 11:11
 */

namespace ondrs\iDefendApi;


use Kdyby\Curl\CurlException;
use Kdyby\Curl\Response;
use Nette\Utils\FileSystem;

use Nette\Utils\Strings;

class iDefend
{

    /** @var string */
    private $tempDir;

    /** @var array */
    private $toDelete = [];

    /** @var \ondrs\iDefendApi\Sender */
    private $sender;

    /** @var string */
    private $username;

    /** @var string */
    private $password;


    /**
     * @param $tempDir
     * @param Sender $sender
     */
    public function __construct($tempDir, Sender $sender)
    {
        $this->tempDir = $tempDir;
        $this->sender = $sender;

        FileSystem::createDir($this->tempDir);
    }


    /**
     * Clean up - delete downloaded files
     */
    public function __destruct()
    {
        foreach ($this->toDelete as $file) {
            FileSystem::delete($file);
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

        $this->sender->setup($this->tempDir . '/' . $username . '.cookie');

        return $this;
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function startSession()
    {
        $response = $this->sender->send('/user/startSession', [
            'User' => [
                'username' => $this->username,
                'password' => $this->password
            ],
        ]);

        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @return array
     * @throws CurlException
     * @throws iDefendException
     */
    public function getProducts()
    {
        $response = $this->sender->send('/policy/getProducts');
        $result = Utils::jsonDecode($response);

        return $result->data->Product;
    }


    /**
     * @param int $productId
     * @return array
     * @throws iDefendException
     */
    public function getPaymentTerms($productId)
    {
        $response = $this->sender->send('/policy/getPaymentTerms', [
            'product_id' => $productId,
        ]);

        $result = Utils::jsonDecode($response);

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
     * @throws iDefendException
     */
    public function getInsuranceTerms($productId)
    {
        $response = $this->sender->send('/policy/getInsuranceTerms', [
            'product_id' => $productId,
        ]);

        $result = Utils::jsonDecode($response);

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
     * @throws iDefendException
     */
    public function getTitles()
    {
        $response = $this->sender->send('/policy/getTitles');
        $result = Utils::jsonDecode($response);

        $data = [];
        foreach ($result->data->Title as $key => $value) {
            $data[] = [
                'id' => $key,
                'name' => $value
            ];
        }

        return $data;
    }


    /**
     * @return array
     * @throws iDefendException
     */
    public function getExtras()
    {
        $response = $this->sender->send('/policy/getExtras');
        $result = Utils::jsonDecode($response);

        return $result->data->Extra;
    }


    /**
     * @param $productId
     * @return array
     * @throws iDefendException
     */
    public function getLoadings($productId)
    {
        $response = $this->sender->send('/policy/getLoadings', [
            'product_id' => $productId,
        ]);

        $result = Utils::jsonDecode($response);

        return $result->data->Loading;
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws iDefendException
     */
    public function getCoverages(array $data)
    {
        $response = $this->sender->send('/policy/getCoverages', $data);
        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws iDefendException
     */
    public function savePolicy(array $data)
    {
        $response = $this->sender->send('/policy/savePolicy', $data);
        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @param array $data
     * @return \stdClass
     * @throws iDefendException
     */
    public function saveQuote(array $data)
    {
        $response = $this->sender->send('/policy/saveQuote', $data);
        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws iDefendException
     */
    public function getPolicy($policyNo)
    {
        $response = $this->sender->send('/policy/getPolicy', [
            'policy_no' => $policyNo,
        ]);

        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @param int $page
     * @param int $limit
     * @param null|array $conditions
     * @param null|array $fields
     * @return mixed
     * @return \stdClass
     * @throws iDefendException
     */
    public function getPolicyList($page = 1, $limit = 30, $conditions = NULL, $fields = NULL)
    {
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

        $response = $this->sender->send('/policy/getPolicyList', $data);
        $result = Utils::jsonDecode($response);

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
     * @throws iDefendException
     */
    public function deletePolicy($policyNo)
    {
        $response = $this->sender->send('/policy/deletePolicy', [
            'policy_no' => $policyNo,
        ]);

        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws iDefendException
     */
    public function getProposal($policyNo)
    {
        $response = $this->sender->send('/policy/getProposal', [
            'policy_no' => $policyNo,
        ]);

        if($file = $this->savePdfFile($policyNo, $response)) {
            return $file;
        }

        // intently just decode - for catching an error
        Utils::jsonDecode($response);

        throw new iDefendException("Wrong response - no PDF or error");
    }


    /**
     * @param string $policyNo
     * @return \stdClass
     * @throws iDefendException
     */
    public function getQuote($policyNo)
    {
        $response = $this->sender->send('/policy/getQuote', [
            'policy_no' => $policyNo,
        ]);

        if($file = $this->savePdfFile($policyNo, $response)) {
            return $file;
        }

        // intently just decode - for catching an error
        Utils::jsonDecode($response);

        throw new iDefendException("Wrong response - no PDF or error");
    }


    /**
     * @return \stdClass
     * @throws iDefendException
     */
    public function closeSession()
    {
        $response = $this->sender->send('/user/closeSession');
        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @return \stdClass
     * @throws CurlException
     * @throws iDefendException
     */
    public function getCancellationCodificators()
    {
        $response = $this->sender->send('/user/getCancellationCodificators');
        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /**
     * @param array $data
     * @return mixed
     * @throws iDefendException
     */
    public function cancelPolicy(array $data)
    {
        $response = $this->sender->send('/user/cancelPolicy', $data);
        $result = Utils::jsonDecode($response);

        return $result->data;
    }


    /****************** Helper functions *****************/


    /**
     * @param $policyNo
     * @param Response $response
     * @return bool|string
     */
    public function savePdfFile($policyNo, Response $response)
    {
        if ($response->getHeaders()['Content-Type'] !== 'application/pdf') {
            return FALSE;
        }

        $body = $response->getResponse();

        $filename = $this->tempDir . '/' . $policyNo . '-' . Strings::substring(md5($body), 0, 5) . '.pdf';
        $this->toDelete[] = $filename;
        file_put_contents($filename, $body);

        return $filename;
    }

} 
