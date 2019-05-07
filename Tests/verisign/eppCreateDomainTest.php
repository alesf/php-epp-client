<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateDomainTest extends eppTestCase
{
    /**
     * @group failed
     */
    public function testCreateDomainWithRegistrant()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.com');
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');        
        $response = $this->conn->writeandread($create);        
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    public function testCreateDomainWithOneHost()
    {
        // TODO: one host is not allowed
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(20).'.com';
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $hostname = 'ns1.siel.si';        
        $host = new Metaregistrar\EPP\eppHost($hostname);       
        $domain->addHost($host);        
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');        
        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    public function testCreateDomainWithTwoHosts()
    {
        // TODO: succesfull two hosts
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(20).'.com';
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $hostname = 'ns1.siel.si';
        $host = new Metaregistrar\EPP\eppHost($hostname);       
        $domain->addHost($host);
        $problem_host = 'ns1.siel.si';
        $hostname = $problem_host;
        $host = new Metaregistrar\EPP\eppHost($hostname);       
        $domain->addHost($host); 
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');        
        $response = $this->conn->writeandread($create);        
        $this->expectException('Metaregistrar\EPP\eppException', sprintf('Error 2306: Parameter value policy error (duplicate ns %s passed)', $problem_host));
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertFalse($response->Success());        
    }

    public function testCreateDomainWithoutRegistrant()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.com');
        $domain->setPeriod(1);
        // $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');        
        $response = $this->conn->writeandread($create);     
        /*
        $this->expectException('Metaregistrar\EPP\eppException', 'No valid registrant in create domain request');
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertFalse($response->Success());        
        */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }


    public function testCreateDomainWithoutAuthcode()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.com');
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->expectException('Metaregistrar\EPP\eppException', "Error 2001: Command syntax error; Element '{urn:ietf:params:xml:ns:domain-1.0}create': Missing child element(s). Expected is one of ( {urn:ietf:params:xml:ns:domain-1.0}contact, {urn:ietf:params:xml:ns:domain-1.0}authInfo ).");
        $this->assertFalse($response->Success());
    }
}
