<?php

use ondrs\iDefendApi\iDefend;
use ondrs\iDefendApi\PolicyDoc;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


class iDefendTest extends \Tester\TestCase
{

    /** @var ondrs\iDefendApi\iDefend */
    private $idefend;


    function setUp()
    {
        $this->idefend = new iDefend(TEMP_DIR, new \ondrs\iDefendApi\Sender('https://test.idefend.eu/ws'));

        $username = 'test_user';
        $password = 'Ch9ddgy';

        $this->idefend->setCredentials($username, $password);
    }


    function tearDown()
    {

    }


    function testStartWrongSession()
    {
        $this->idefend->setCredentials('pepa', 'zdepa');

        Assert::exception(function () {
            $this->idefend->startSession();
        }, 'ondrs\iDefendApi\iDefendException', "Authorisation error");
    }


    function testStartSession()
    {
        $response = $this->idefend->startSession();
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

    /*
    function testGetCoverages()
    {
       $data = \Nette\Utils\Json::decode(file_get_contents(__DIR__ . '/data/covers.warranty.request.json'), \Nette\Utils\Json::FORCE_ARRAY);

       $response = $this->idefend->getCoverages($data);
       Assert::type('stdClass', $response->Policy);
       Assert::type('array', $response->Extra);
       Assert::type('array', $response->Loading);
       Assert::type('array', $response->Coverage);
       Assert::type('array', $response->LoadingType);
    }



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
        Assert::exception(function () {
            $this->idefend->getPolicy('NONSENSE');
        }, 'ondrs\iDefendApi\iDefendException', "The policy couldn't be found");
    }


    function testGetQuote()
    {
        // TODO: implement
    }


    function testSaveQuote()
    {
        // TODO: implement
    }


    function testGetPolicyList()
    {
        $response = $this->idefend->getPolicyList(1, 30);

        Assert::type('array', $response->data);

        Assert::equal(1, $response->Paging->currentPage);
        Assert::type('int', $response->Paging->recordsInPage);
        Assert::type('int', $response->Paging->pageCount);
        Assert::type('int', $response->Paging->recordsCount);
    }


    function testDeletePolicy()
    {
        Assert::exception(function () {
            $this->idefend->deletePolicy('NONSENSE');
        }, 'ondrs\iDefendApi\iDefendException', 'Could not delete the policy no: NONSENSE');
    }


    function testGetProposal()
    {
        Assert::exception(function () {
            $this->idefend->getProposal('NONSENSE');
        }, 'ondrs\iDefendApi\iDefendException', 'Could not return proposal for this policy: NONSENSE');

    }


    function testGetCancellationCodificators()
    {
        $result = $this->idefend->getCancellationCodificators();

        Assert::type('stdClass', $result);

        Assert::type('array', $result->cancellation_reason);
        Assert::type('array', $result->cancellation_refund);
        Assert::type('array', $result->refund_paid_to);
    }


    function testCancelPolicy()
    {

    }


    function testSavePdfFile()
    {

    }


    function testGetUploadDocTypes()
    {
        $types = $this->idefend->getUploadDocTypes();

        Assert::type('array', $types);
        Assert::count(11, $types);
    }


    function testGetPolicyUploadedDocs()
    {
        $response = $this->idefend->getPolicyList(1, 30);
        $policyNo = $response->data[0]->policy_no;

        $result = $this->idefend->getPolicyUploadedDocs($policyNo);
        Assert::type('array', $result);
    }


    function testUploadPolicyDocs()
    {
        $response = $this->idefend->getPolicyList(1, 30);
        $policyNo = $response->data[0]->policy_no;

        $filename = __DIR__ . '/data/doc.pdf';

        $docs = [new PolicyDoc($filename)];

        $result = $this->idefend->uploadPolicyDocs($policyNo, $docs);
        Assert::true($result);
    }


    function testGetPolicyDefaultValues()
    {
        $values = $this->idefend->getPolicyDefaultValues(2);

        Assert::type('array', $values);
    }


    function testCloseSession()
    {
        $response = $this->idefend->closeSession();
        Assert::equal('Session is closed successfuly', $response);
    }


}

id(new iDefendTest)->run();
