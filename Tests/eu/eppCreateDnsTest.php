<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateDnsTest extends eppTestCase
{
    /**
     * Test successful dns create
     */
    public function testCreateDnsSuccess()
    {
        $c_reg = $this->createContact('registrant');
        $c_billing = 'c446234'; // you probably already have a billing contact
        $c_tech = 'c448321'; // you probably already have a tech contact
        $domainname = 'a-test-' . $this->randomstring(20).'.eu';
        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $domain->setPeriod(1);
        $domain->setRegistrant($c_reg);
        $domain->setAuthorisationCode('fubar');
        $domain->addHost(new \Metaregistrar\EPP\eppHost("dns.siel.si"));
        $domain->addHost(new \Metaregistrar\EPP\eppHost("dns1.siel.si"));
        $domain->addHost(new \Metaregistrar\EPP\eppHost("dns2.siel.si"));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($c_tech, 'tech'));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($c_billing, 'billing'));
        $create = new \Metaregistrar\EPP\eppCreateDomainRequest($domain, true);

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
}
