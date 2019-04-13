<?php
use Metaregistrar\EPP\eppInfoDomainRequest;

include_once(dirname(__FILE__).'/eppTestCase.php');

class eppTransferDomainTest extends eppTestCase
{
    /**
     * @group demo
     */
    public function testPrepareTransferDomain()
    {
        $domainname = self::randomstring(10).'.si';
        $password = 'transfer';
        $password_hash = hash('sha256', $password);

        $user2 = dirname(__FILE__).'/../testsetup/siEpp2.ini';
        $this->tearDown();
        $this->setUp($user2);

        $this->createDomain($domainname);
        $this->updateDomain($domainname, ['mod' => ['auth' => $password_hash]]);
    }

    /**
     * @group test
     */
    public function testRequestTransferDomain()
    {
        $user1 = dirname(__FILE__).'/../testsetup/siEpp.ini';
        $user2 = dirname(__FILE__).'/../testsetup/siEpp2.ini';

        $domainname = self::randomstring(30).'.si';
        $password = 'transfer';
        $password_hash = hash('sha256', $password);

        $this->tearDown();
        $this->setUp($user2);

        $this->createDomain($domainname);
        $this->updateDomain($domainname, ['mod' => ['auth' => $password_hash]]);

        // ----

        $this->tearDown();
        $this->setUp($user1);

        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $domain->setAuthorisationCode($password);

        $transfer = new \Metaregistrar\EPP\eppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);

        $response = $this->conn->writeandread($transfer);
        echo $response->SaveXML();

        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);

        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    public function testDoubleTransferDomain()
    {
        $user1 = dirname(__FILE__).'/../testsetup/siEpp.ini';
        $user2 = dirname(__FILE__).'/../testsetup/siEpp2.ini';

        $domainname = self::randomstring(30).'.si';
        $password = 'transfer';
        $password_hash = hash('sha256', $password);

        $this->tearDown();
        $this->setUp($user2);

        $this->createDomain($domainname);
        $this->updateDomain($domainname, ['mod' => ['auth' => $password_hash]]);

        // ----

        $this->tearDown();
        $this->setUp($user1);

        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        // $domain->setPeriod(1);
        $domain->setAuthorisationCode($password);

        $transfer = new \Metaregistrar\EPP\eppTransferRequest(\Metaregistrar\EPP\eppTransferRequest::OPERATION_REQUEST, $domain);

        $response = $this->conn->writeandread($transfer);

        // First Successful

        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);

        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());

        // Second unsuccessful

        $response = $this->conn->writeandread($transfer);

        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);

        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2106: Object is not eligible for transfer');
        $this->assertFalse($response->Success());
    }

    public function testDomainTransferStatus()
    {
        $this->assertTrue(true);
        # code...
    }

    public function testCancelDomainTransfer()
    {
        $this->assertTrue(true);
        # code...
    }

    public function testTransferWithoutCode()
    {
        $this->assertTrue(true);
        # code...
    }
}
