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
        $this->idefend = new iDefend(TEMP_DIR);
    }


    function tearDown()
    {

    }


    function testStartSession()
    {
        $username = 'test_user';
        $password = 'z6WEd4qS';

        $response = $this->idefend->startSession($username, $password);

        Assert::type('stdClass', $response->User);
    }



    function testGetProducts()
    {
        $response = $this->idefend->getProducts();
        Assert::type('array', $response);
    }


    function testGetPaymentTerms()
    {
        $response = $this->idefend->getPaymentTerms();
        Assert::type('array', $response);
    }


    function testGetInsuranceTerms()
    {
        $response = $this->idefend->getInsuranceTerms();
        Assert::type('array', $response);
    }


    function testGetTitles()
    {
        $response = $this->idefend->getTitles();
        Assert::type('array', $response);
    }


    function testGetExtras()
    {
        $response = $this->idefend->getExtras();
        Assert::type('array', $response);
    }


    function testGetLoadings()
    {
        $response = $this->idefend->getLoadings(4);
        Assert::type('array', $response);
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
        Assert::type('stdClass', $response->Policy);
        Assert::type('array', $response->Extra);
        Assert::type('array', $response->Loading);
        Assert::type('array', $response->Coverage);
        Assert::type('array', $response->LoadingType);
    }


    /**
     * @skip
     */
    function testSavePolicy()
    {
        $data = [];

        //$response = $this->idefend->savePolicy($data);
    }


    function testGetPolicy()
    {
        Assert::exception(function() {
            $this->idefend->getPolicy('NONSENSE');
        }, 'ondrs\iDefendApi\iDefendException', "The policy couldn't be found");
    }


    function testDeletePolicy()
    {
        Assert::exception(function() {
            $this->idefend->deletePolicy('NONSENSE');
        }, 'ondrs\iDefendApi\iDefendException', 'Could not delete the policy no: NONSENSE');
    }


    function testGetProposal()
    {
        Assert::exception(function() {
            $this->idefend->getProposal('NONSENSE');
        }, 'ondrs\iDefendApi\iDefendException', 'Could not return proposal for this policy: NONSENSE');

    }


    function testCloseSession()
    {
        $response = $this->idefend->closeSession();
        Assert::equal('Session is closed successfuly', $response);
    }


}

id(new iDefendTest)->run();
