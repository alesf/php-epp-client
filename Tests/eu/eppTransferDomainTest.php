<?php
use Metaregistrar\EPP\eppInfoDomainRequest;

include_once(dirname(__FILE__).'/eppTestCase.php');


class eppTransferDomainTest extends eppTestCase
{
    /*
    public function testRequestTransferDomain()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.eu';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user1);
        $this->createDomain($domainname);

        // this is the way to obtain the transfer password
        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $info = new Metaregistrar\EPP\euridEppInfoDomainRequest($domain, null, true);
        $response = $this->conn->writeandread($info);
        $this->assertInstanceOf('Metaregistrar\EPP\euridEppInfoDomainResponse', $response);
        $password = $response->getDomainAuthInfo();

        $this->tearDown();
        $this->setUp($user2);

        $c_reg = $this->createContact('registrant');
        $domain->setRegistrant($c_reg);
        $domain->setAuthorisationCode($password);
        // user2 tech and billing contacts
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle('c705164', 'tech'));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle('c446234', 'billing'));

        $transfer = new \Metaregistrar\EPP\euridEppTransferDomainRequest(\Metaregistrar\EPP\euridEppTransferDomainRequest::OPERATION_REQUEST, $domain);

        $response = $this->conn->writeandread($transfer);
        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());

    }

    public function testDoubleTransferDomain()
    {
        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.eu';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user1);
        $this->createDomain($domainname);

        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $info = new Metaregistrar\EPP\euridEppInfoDomainRequest($domain, null, true);
        $response = $this->conn->writeandread($info);
        $this->assertInstanceOf('Metaregistrar\EPP\euridEppInfoDomainResponse', $response);
        $password = $response->getDomainAuthInfo();

        $this->tearDown();
        $this->setUp($user2);

        $c_reg = $this->createContact('registrant');
        $domain->setRegistrant($c_reg);
        $domain->setAuthorisationCode($password);
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle('c705164', 'tech'));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle('c446234', 'billing'));

        $transfer = new \Metaregistrar\EPP\euridEppTransferDomainRequest(\Metaregistrar\EPP\euridEppTransferDomainRequest::OPERATION_REQUEST, $domain);

        $response = $this->conn->writeandread($transfer);
        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());

        // Second attempt should be unsuccessful
        $response = $this->conn->writeandread($transfer);
        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2106: Object is not eligible for transfer');
        $this->assertFalse($response->Success());

    }

*/


    public function testTransferWithoutCode()
    {

        $user1 = dirname(__FILE__).'/testsetup.ini';
        $user2 = dirname(__FILE__).'/testsetup2.ini';
        $domainname = self::randomstring(30).'.eu';
        // $password_hash = hash('sha256', $password);
        $this->tearDown();
        $this->setUp($user1);
        $this->createDomain($domainname);

        // this is the way to obtain the transfer password in start the transfer process
        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $info = new Metaregistrar\EPP\euridEppInfoDomainRequest($domain, null, true);
        $response = $this->conn->writeandread($info);
        $this->assertInstanceOf('Metaregistrar\EPP\euridEppInfoDomainResponse', $response);
        $password = $response->getDomainAuthInfo();

        $this->tearDown();
        $this->setUp($user2);

        $c_reg = $this->createContact('registrant');
        $domain->setRegistrant($c_reg);
        // user2 tech and billing contacts
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle('c705164', 'tech'));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle('c446234', 'billing'));

        $transfer = new \Metaregistrar\EPP\euridEppTransferDomainRequest(\Metaregistrar\EPP\euridEppTransferDomainRequest::OPERATION_REQUEST, $domain);

        $response = $this->conn->writeandread($transfer);
        $this->assertInstanceOf('Metaregistrar\EPP\eppTransferResponse', $response);
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2202: Invalid authorization information (The requester has given authinfo which doesn\'t match)');
        $this->assertFalse($response->Success());
    }

}
