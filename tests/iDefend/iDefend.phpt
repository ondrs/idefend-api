<?php

use ondrs\iDefendApi\iDefend;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


class iDefendTest extends \Tester\TestCase
{

    /** @var ondrs\iDefendApi\iDefend  */
    private $idefend;


    function setUp()
    {
        $username = 'test_user';
        $password = 'z6WEd4qS';
        $url = 'https://idefend.apiary.io/ws';

        $this->idefend = new iDefend($username, $password);
    }


    function tearDown()
    {

    }


    function testStartSession()
    {
        $response = $this->idefend->startSession();

        Assert::type('stdClass', $response->data->User);
        Assert::type('string', $response->payload->User->username);
        Assert::type('string', $response->payload->User->password);
    }


    function testGetProducts()
    {
        $response = $this->idefend->getProducts();
        Assert::type('array', $response->data->Product);
    }


    function testGetPaymentTerms()
    {
        $response = $this->idefend->getPaymentTerms(4);
        Assert::notEqual(NULL, $response->payload->product_id);
        Assert::type('array', $response->data->PaymentTerm);
    }


    function testGetInsuranceTerms()
    {
        $response = $this->idefend->getInsuranceTerms(2);
        Assert::notEqual(NULL, $response->payload->product_id);
        Assert::type('array', $response->data->InsuranceTerm);
    }


    function testGetTitles()
    {
        $response = $this->idefend->getTitles();
        Assert::type('stdClass', $response->data->Title);
    }


    function testGetExtras()
    {
        $response = $this->idefend->getExtras();
        Assert::type('array', $response->data->Extra);
    }


    function testGetLoadings()
    {
        $response = $this->idefend->getLoadings();
        Assert::type('array', $response->data->Loading);
    }


    function testGetCoverages()
    {
        $data = [
            'Policy' => [
                'product_id' => 4,
                'auto_model_id' => 123,
                'vehicle_reg_date' => '2014-02-05',
                'vehicle_purchase_date' => '2014-02-05',
                'vehicle_odometer' => 500,
                'vehicle_mfg_inception' => '2014-02-05',
                'vehicle_engine_size' => 1990,
                'vehicle_mfg_warr_term' => 36,
                'vehicle_mfg_warr_km' => 10000,
                'payment_term' => 'LumpSum',
                'vehicle_purchase_price' => 12345.25,
                'ins_term' => 36,
            ],
            'Extra' => [
                [
                    'id' => 2,
                    'name' => 'Trip Interruption',
                    'selected' => TRUE,
                ],
                [
                    'id' => 4,
                    'name' => 'Trip Interruption',
                    'selected' => TRUE,
                ],
            ],
            'Loading' => [
                [
                    'id' => 3,
                    'type' => 'TERM',
                    'value' => 9,
                    'selected' => TRUE,
                ],
                [
                    'id' => 10,
                    'type' => 'KM_LIMIT',
                    'value' => 'Unlimited',
                    'selected' => TRUE,
                ],
                [
                    'id' => 9,
                    'type' => 'DEDUCTIBLE',
                    'value' => 0,
                    'selected' => TRUE,
                ],
                [
                    'id' => 36,
                    'type' => 'CLAIM_LIMIT',
                    'value' => 50000,
                    'selected' => TRUE,
                ],
            ]
        ];

        $response = $this->idefend->getCoverages($data);
        Assert::type('stdClass', $response->payload->Policy);
        Assert::type('stdClass', $response->data->Policy);
        Assert::type('array', $response->data->Extra);
        Assert::type('array', $response->data->Loading);
        Assert::type('array', $response->data->Coverage);
        Assert::type('array', $response->data->LoadingType);
    }


    /**
     * @skip
     */
    function testSavePolicy()
    {
        $data = [];

        $response = $this->idefend->savePolicy($data);
    }


    function testGetPolicy()
    {
        $response = $this->idefend->getPolicy('CWRG-003766');
        Assert::type('stdClass', $response->data->Policy);
        Assert::type('string', $response->data->Policy->id);
        Assert::type('string', $response->data->Policy->policy_no);
    }


    function testDeletePolicy()
    {
        $response = $this->idefend->deletePolicy('CWRG-003766');
        Assert::type('stdClass', $response->data->Policy);
        Assert::type('string', $response->data->Policy->id);
        Assert::type('string', $response->data->Policy->policy_no);
    }


    /**
     * @skip
     */
    function testGetProposal()
    {
        $response = $this->idefend->getProposal('CWRG-003766');

    }


    function testCloseSession()
    {
        $response = $this->idefend->closeSession();
        Assert::null($response->payload);
        Assert::equal('Session is closed successfuly', $response->data);
    }


}

$test = new iDefendTest;
$test->run();
