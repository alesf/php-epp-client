<?php
use Metaregistrar\EPP\eppInfoDomainRequest;

include_once(dirname(__FILE__).'/eppTestCase.php');


class eppTransferDomainTest extends eppTestCase
{
    public function testRequestTransferDomain()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.com';
        $password = '2Te$tPWS$';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user2);
        $this->createDomain($domainname);
        if ($this->updateDomain($domainname, ['mod' => ['auth' => $password]])) {
            // ST: if update is successful, we initiate domain transfer from user1
            $this->tearDown();
            $this->setUp($user1);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $transfer = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);
            $transfer->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($transfer);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            // ST: returns 1001 'Command completed successfully; action pending'
            // $this->assertEquals('Command completed successfully', $response->getResultMessage());
            // $this->assertEquals(1000, $response->getResultCode());
            $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
            $this->assertEquals(1001, $response->getResultCode());
        }
    }

    public function testDoubleTransferDomain()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.com';
        $password = '2Te$tPWS$';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user2);
        $this->createDomain($domainname);
        if ($this->updateDomain($domainname, ['mod' => ['auth' => $password]])) {
            // ST: if update is successful, we initiate domain transfer from user1
            $this->tearDown();
            $this->setUp($user1);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $transfer = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);
            $transfer->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($transfer);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            // ST: returns 1001 'Command completed successfully; action pending'
            // $this->assertEquals('Command completed successfully', $response->getResultMessage());
            // $this->assertEquals(1000, $response->getResultCode());
            $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
            $this->assertEquals(1001, $response->getResultCode());

            // Second attempt should be unsuccessful
            $response = $this->conn->writeandread($transfer);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->expectException('Metaregistrar\EPP\eppException', 'Error 2106: Object is not eligible for transfer');
            $this->assertFalse($response->Success());
        }
    }

    public function testDomainTransferStatus()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.com';
        $password = '2Te$tPWS$';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user2);
        $this->createDomain($domainname);
        if ($this->updateDomain($domainname, ['mod' => ['auth' => $password]])) {
            // ST: if update is successful, we initiate domain transfer from user1
            $this->tearDown();
            $this->setUp($user1);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $transfer = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);
            $transfer->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($transfer);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            // ST: returns 1001 'Command completed successfully; action pending'
            // $this->assertEquals('Command completed successfully', $response->getResultMessage());
            // $this->assertEquals(1000, $response->getResultCode());
            $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
            $this->assertEquals(1001, $response->getResultCode());

            // now we test the domain transfer status
            $status = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_QUERY, $domain);
            $status->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($status);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());
            // it should be pending transfer
            $this->assertEquals('pending', $response->getTransferStatus());

            // we accept the domaintransfer
            $this->tearDown();
            $this->setUp($user2);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $accept = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_APPROVE, $domain);
            $accept->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($accept);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());

            // now we test the domain transfer status
            $this->tearDown();
            $this->setUp($user1);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $status = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_QUERY, $domain);
            $status->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($status);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());
            // now it should be clientApproved
            $this->assertEquals('clientApproved', $response->getTransferStatus());
        }
    }

    public function testRejectDomainTransfer()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.com';
        $password = '2Te$tPWS$';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user2);
        $this->createDomain($domainname);
        if ($this->updateDomain($domainname, ['mod' => ['auth' => $password]])) {
            // ST: if update is successful, we initiate domain transfer from user1
            $this->tearDown();
            $this->setUp($user1);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $transfer = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);
            $transfer->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($transfer);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            // ST: returns 1001 'Command completed successfully; action pending'
            // $this->assertEquals('Command completed successfully', $response->getResultMessage());
            // $this->assertEquals(1000, $response->getResultCode());
            $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
            $this->assertEquals(1001, $response->getResultCode());

            // now we test the domain transfer status
            $status = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_QUERY, $domain);
            $status->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($status);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());
            // it should be pending transfer
            $this->assertEquals('pending', $response->getTransferStatus());

            // we accept the domaintransfer
            $this->tearDown();
            $this->setUp($user2);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $accept = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REJECT, $domain);
            $accept->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($accept);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());

            // now we test the domain transfer status
            $this->tearDown();
            $this->setUp($user1);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $status = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_QUERY, $domain);
            $status->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($status);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());
            // now it should be clientRejected
            $this->assertEquals('clientRejected', $response->getTransferStatus());
        }
    }

    public function testCancelDomainTransfer()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.com';
        $password = '2Te$tPWS$';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user2);
        $this->createDomain($domainname);
        if ($this->updateDomain($domainname, ['mod' => ['auth' => $password]])) {
            // ST: if update is successful, we initiate domain transfer from user1
            $this->tearDown();
            $this->setUp($user1);
            $domain = new \Metaregistrar\EPP\eppDomain($domainname);
            $domain->setAuthorisationCode($password);
            $transfer = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);
            $transfer->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($transfer);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            // ST: returns 1001 'Command completed successfully; action pending'
            // $this->assertEquals('Command completed successfully', $response->getResultMessage());
            // $this->assertEquals(1000, $response->getResultCode());
            $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
            $this->assertEquals(1001, $response->getResultCode());

            // now we test the domain transfer status
            $status = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_QUERY, $domain);
            $status->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($status);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());
            // it should be pending transfer
            $this->assertEquals('pending', $response->getTransferStatus());

            // we accept the domaintransfer
            $accept = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_CANCEL, $domain);
            $accept->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($accept);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());

            // now we test the domain transfer status
            $status = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_QUERY, $domain);
            $status->setSubProduct('dotCOM');
            $response = $this->conn->writeandread($status);
            $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed successfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());
            // now it should be clientApproved
            $this->assertEquals('clientCancelled', $response->getTransferStatus());
        }
    }

    public function testTransferWithoutCode()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.com';
        $password = '2Te$tPWS$';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user2);
        $this->createDomain($domainname);
        // no authorisation code is entered
        $this->tearDown();
        $this->setUp($user1);
        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $domain->setAuthorisationCode($password);
        $transfer = new \Metaregistrar\EPP\verisignEppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);
        $transfer->setSubProduct('dotCOM');
        $response = $this->conn->writeandread($transfer);
        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2202: Invalid authorization information (The requester has given authinfo which doesn\'t match)');
        $this->assertFalse($response->Success());
    }
}
