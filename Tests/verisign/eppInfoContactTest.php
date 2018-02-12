<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppInfoContactTest extends eppTestCase
{
    /**
     * @group test
     * Test succesful contact info
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testInfoContactSuccess()
    {
        $contactid = parent::createContact();
        $contact = new Metaregistrar\EPP\eppContactHandle($contactid);
        $info = new Metaregistrar\EPP\verisignEppInfoContactRequest($contact);
        $info->setSubProduct('dotCOM');
        $response = $this->conn->writeandread($info);
        echo $response->saveXML();
        $this->assertInstanceOf('Metaregistrar\EPP\eppInfoContactResponse', $response);
        /* @var $response Metaregistrar\EPP\eppInfoContactResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    /**
     * Test succesful contact info giving an authcode
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testInfoContactWithAuthcode()
    {
        $password = $this->randomstring(12, true);
        $contactid = parent::createContact('dotCOM', $password);
        $contact = new Metaregistrar\EPP\eppContactHandle($contactid);
        $contact->setPassword($password);
        $info = new Metaregistrar\EPP\verisignEppInfoContactRequest($contact);
        $info->setSubProduct('dotCOM');
        $response = $this->conn->writeandread($info);
        echo $response->saveXML();
        $this->assertInstanceOf('Metaregistrar\EPP\eppInfoContactResponse', $response);
        /* @var $response Metaregistrar\EPP\eppInfoContactResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }
}
