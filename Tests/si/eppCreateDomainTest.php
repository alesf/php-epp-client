<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateDomainTest extends eppTestCase
{
    public function testCreateDomainWithRegistrant()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.si');
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode('fubar');
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain);
        $response = $this->conn->writeandread($create);
        // echo $response->saveXML();
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    /**
     * @group test
     */
    public function testCreateDomainWithOneHost()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.si');
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode('fubar');
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));

        $host1 = $this->createHost();
        $domain->addHost(new \Metaregistrar\EPP\eppHost($host1));

        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2306: Parameter value policy error');
        $response->Success();
    }

    public function testCreateDomainWithTwoHosts()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.si');
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode('fubar');
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));

        $host1 = $this->createHost();
        $host2 = $this->createHost();
        $domain->addHost(new \Metaregistrar\EPP\eppHost($host1));
        $domain->addHost(new \Metaregistrar\EPP\eppHost($host2));

        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    public function testCreateDomainWithoutRegistrant()
    {
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.si');
        $domain->setPeriod(1);
        $domain->setAuthorisationCode('fubar');
        $this->expectException('Metaregistrar\EPP\eppException', 'No valid registrant in create domain request');
        new \Metaregistrar\EPP\eppCreateDomainRequest($domain);
    }


    public function testCreateDomainWithoutAuthcode()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.si');
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain);
        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->expectException('Metaregistrar\EPP\eppException', "Error 2001: Command syntax error; Element '{urn:ietf:params:xml:ns:domain-1.0}create': Missing child element(s). Expected is one of ( {urn:ietf:params:xml:ns:domain-1.0}contact, {urn:ietf:params:xml:ns:domain-1.0}authInfo ).");
        $this->assertFalse($response->Success());
    }

    public function testCreateIDNDomainWithRegistrant()
    {
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(20).'.si';
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode('fubar');
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain);
        $response = $this->conn->writeandread($create);
        // echo $response->saveXML();
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

}