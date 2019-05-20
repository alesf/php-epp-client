<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppPollTest extends eppTestCase
{

    /**
     * empties Poll queue
     * @throws \Metaregistrar\EPP\eppException
     * @group ignore_mez
     */
    public function testEmptyPollQueue()
    {
        $poll = new Metaregistrar\EPP\eppPollRequest(Metaregistrar\EPP\eppPollRequest::POLL_REQ);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollRequest', $poll);
        $response = $this->conn->writeandread($poll);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppPollResponse) {
            /* @var $response Metaregistrar\EPP\eppPollResponse */
            $this->assertTrue($response->Success());
            while ($response->getMessageCount()>0) {
                //echo "message id:".$response->getMessageId()."\n";
                $ack = new Metaregistrar\EPP\eppPollRequest(Metaregistrar\EPP\eppPollRequest::POLL_ACK, $response->getMessageId());
                $response = $this->conn->writeandread($ack);
                $this->assertInstanceOf('Metaregistrar\EPP\eppPollResponse', $response);
                $poll = new Metaregistrar\EPP\eppPollRequest(Metaregistrar\EPP\eppPollRequest::POLL_REQ);
                $this->assertInstanceOf('Metaregistrar\EPP\eppPollRequest', $poll);
                $response = $this->conn->writeandread($poll);
            }
        }
    }


    /**
     * Test if poll queue is empty
     * Expects a standard result for an empty poll queue
     * @group ignore_me
     */
    public function testPollEmpty()
    {
        $poll = new Metaregistrar\EPP\eppPollRequest(Metaregistrar\EPP\eppPollRequest::POLL_REQ, 0);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollRequest', $poll);
        $response = $this->conn->writeandread($poll);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppPollResponse) {
            /* @var $response Metaregistrar\EPP\eppPollResponse */
            $this->assertTrue($response->Success());
            $this->assertSame(Metaregistrar\EPP\eppResponse::RESULT_NO_MESSAGES, $response->getResultCode());
            $this->assertSame(0, $response->getMessageCount());
        }
    }

    /**
     * Test if poll queue is empty
     * Expects a standard result for an empty poll queue
     */
    public function testPollTransfer()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.com';
        $password = '2Te$tPWS$';
        $this->tearDown();
        $this->setUp($user1);
        $this->createDomain($domainname);
        if ($this->updateDomain($domainname, ['mod' => ['auth' => $password]])) {
            $this->tearDown();
            $this->setUp($user2);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $transfer = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);
            $transfer->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($transfer);
        }

        $this->tearDown();
        $this->setUp($user1);

        $poll = new Metaregistrar\EPP\eppPollRequest(Metaregistrar\EPP\eppPollRequest::POLL_REQ, 0);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollRequest', $poll);
        $response = $this->conn->writeandread($poll);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppPollResponse) {
            /* @var $response Metaregistrar\EPP\eppPollResponse */
            $this->assertTrue($response->Success());
            echo $response->getResultCode();
            echo "\nMessages cnt: {$response->getMessageCount()}\n";
            echo "\nMESSAGE: \n";
            echo "Message: {$response->getMessage()}\n";
            echo "MessageID: {$response->getMessageId()}\n";
            echo "DomainName: {$response->getDomainName()}\n";
            echo "DomainStatus: {$response->getDomainStatus()}\n";
            echo "DomainStatusText: {$response->getDomainStatusText()}\n";
            echo "DomainActionDate: {$response->getDomainActionDate()}\n";
            echo "DomainRequestDate: {$response->getDomainRequestDate()}\n";
            echo "MessageType: {$response->getMessageType()}\n";
            echo "DomainTrStatus: {$response->getDomainTrStatus()}\n";
            echo "ReqClientID: {$response->getDomainRequestClientId()}\n";
            echo "AcClientID: {$response->getDomainActionClientId()}\n";
            echo "\n---\n";

            // $password = $this->randomstring(12, true);
            $contact = new Metaregistrar\EPP\eppContactHandle($response->getDomainRequestClientId());
            // $contact->setPassword($password);
            $info = new Metaregistrar\EPP\verisignEppInfoContactRequest($contact);
            $info->setSubProduct('dotCOM');
            $con_res = $this->conn->writeandread($info);

            print_r($con_res->dumpContents());

            // echo "CONTACT EMAIL: {$con_res->getContactEmail()}\n";
            // echo "CONTACT NAME: {$con_res->getContactName()}\n";
        }
    }

    /**
     * Test if poll queue is empty
     * Expects a standard result for an empty poll queue
     */
    public function testPollMessage()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        // $this->tearDown();
        // $this->setUp($user2);

        $poll = new Metaregistrar\EPP\eppPollRequest(Metaregistrar\EPP\eppPollRequest::POLL_REQ, 0);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollRequest', $poll);
        $response = $this->conn->writeandread($poll);
        $this->assertInstanceOf('Metaregistrar\EPP\eppPollResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppPollResponse) {
            /* @var $response Metaregistrar\EPP\eppPollResponse */
            $this->assertTrue($response->Success());
            echo $response->getResultCode();
            echo "\nMessages cnt: {$response->getMessageCount()}\n";
            echo "\nMESSAGE: \n";
            echo "Message: {$response->getMessage()}\n";
            echo "MessageID: {$response->getMessageId()}\n";
            echo "DomainName: {$response->getDomainName()}\n";
            echo "DomainStatus: {$response->getDomainStatus()}\n";
            echo "DomainStatusText: {$response->getDomainStatusText()}\n";
            echo "DomainActionDate: {$response->getDomainActionDate()}\n";
            echo "DomainRequestDate: {$response->getDomainRequestDate()}\n";
            echo "ReqClientID: {$response->getDomainRequestClientId()}\n";
            echo "AcClientID: {$response->getDomainActionClientId()}\n";
            echo "MessageType: {$response->getMessageType()}\n";
            echo "DomainTrStatus: {$response->getDomainTrStatus()}\n";
            echo "\n---\n";
            $this->assertSame(Metaregistrar\EPP\eppResponse::RESULT_NO_MESSAGES, $response->getResultCode());
            $this->assertSame(0, $response->getMessageCount());

            // $password = $this->randomstring(12, true);
            $contact = new Metaregistrar\EPP\eppContactHandle($response->getDomainRequestClientId());
            // $contact->setPassword($password);
            $info = new Metaregistrar\EPP\verisignEppInfoContactRequest($contact);
            $info->setSubProduct('dotCOM');
            $con_res = $this->conn->writeandread($info);

            print_r($con_res->dumpContents());

            // echo "CONTACT EMAIL: {$con_res->getContactEmail()}\n";
            // echo "CONTACT NAME: {$con_res->getContactName()}\n";
        }
    }
}
