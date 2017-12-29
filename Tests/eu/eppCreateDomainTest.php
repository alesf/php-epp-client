<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateDomainTest extends eppTestCase
{
    private $forcehostattr = true;

    public function testCreateDomainWithRegistrant()
    {
        $c_reg = $this->createContact('registrant');
        $c_billing = 'c446232'; // you probably already have a billing contact
        $c_tech = 'c446264'; // you probably already have a tech contact
        $domain = new \Metaregistrar\EPP\eppDomain('a-test-' . $this->randomstring(20).'.eu');
        $domain->setPeriod(1);
        $domain->setRegistrant($c_reg);
        $domain->setAuthorisationCode('fubar');
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($c_tech, 'tech'));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($c_billing, 'billing'));
        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain, $this->forcehostattr);

        try {
            $response = $this->conn->writeandread($create);
        } catch (eppException $e) {
            print_r($e->getMessage());
            print_r($e->getLastCommand());
            $create->domainobject->ownerDocument->formatOutput = true;
            print_r($create->domainobject->ownerDocument->saveXML());
            exit();
        }

        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    public function testCreateDomainWithoutRegistrant()
    {
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.eu');
        $domain->setPeriod(1);
        $domain->setAuthorisationCode('fubar');
        $this->expectException('Metaregistrar\EPP\eppException', 'No valid registrant in create domain request');
        new \Metaregistrar\EPP\eppCreateDomainRequest($domain, $this->forcehostattr);
    }


    public function testCreateDomainWithoutAuthcode()
    {
        $contactid = $this->createContact();
        $domain = new \Metaregistrar\EPP\eppDomain($this->randomstring(20).'.eu');
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain, $this->forcehostattr);
        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppCreateDomainResponse */
        $this->expectException('Metaregistrar\EPP\eppException', "Error 2001: Command syntax error; Element '{urn:ietf:params:xml:ns:domain-1.0}create': Missing child element(s). Expected is one of ( {urn:ietf:params:xml:ns:domain-1.0}contact, {urn:ietf:params:xml:ns:domain-1.0}authInfo ).");
        $this->assertFalse($response->Success());
    }
}
