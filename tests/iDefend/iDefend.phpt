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
        $response = $this->idefend->getPaymentTerms(4);
        Assert::type('array', $response);
    }


    function testGetInsuranceTerms()
    {
        $response = $this->idefend->getInsuranceTerms(2);
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
        $data = \Nette\Utils\Json::decode(file_get_contents(__DIR__ . '/data/covers.gap.request.json'), \Nette\Utils\Json::FORCE_ARRAY);

        $response = $this->idefend->getCoverages($data);
        Assert::type('stdClass', $response->Policy);
        Assert::type('array', $response->Extra);
        Assert::type('array', $response->Loading);
        Assert::type('array', $response->Coverage);
        Assert::type('array', $response->LoadingType);
    }


    /*
    function testSavePolicy()
    {
        $data = \Nette\Utils\Json::decode(file_get_contents(__DIR__ . '/data/policy.request.json'), \Nette\Utils\Json::FORCE_ARRAY);

        $response = $this->idefend->savePolicy($data);
        Assert::type('stdClass', $response->Policy);
        Assert::type('array', $response->Extra);
        Assert::type('array', $response->Loading);
    }
    */


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
