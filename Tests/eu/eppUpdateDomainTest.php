<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppUpdateDomainTest extends eppTestCase
{
    private $forcehostattr = true;

    /**
     * Test update of hosts on a domain name
     * Expects a standard result for a free domainname
     */
    public function testUpdateDomainHostAttr()
    {
        $domain_name = 'a-chg-test-'.self::randomstring(20) . '.eu';
        $this->createDomain($domain_name);
        $domain = new Metaregistrar\EPP\eppDomain($domain_name);
        $add = null;
        $del = new Metaregistrar\EPP\eppDomain($domain_name);
        $d1 = new Metaregistrar\EPP\eppHost('ns1.metaregistrar.nl');
        $d2 = new Metaregistrar\EPP\eppHost('ns2.metaregistrar.nl');
        $del->addHost($d1);
        $del->addHost($d2);
        $mod = new Metaregistrar\EPP\eppDomain($domain_name);
        $h1 = new Metaregistrar\EPP\eppHost('ns1.metaregistrar.nl');
        $h2 = new Metaregistrar\EPP\eppHost('ns2.metaregistrar.nl');
        $mod->addHost($h1);
        $mod->addHost($h2);
        $update = new Metaregistrar\EPP\eppUpdateDomainRequest($domain, $add, $del, $mod, $this->forcehostattr);
        // echo $update->saveXML();
        // exit();
        try {
            $response = $this->conn->writeandread($update);
        } catch (eppException $e) {
            print_r($e->getMessage());
            print_r($e->getLastCommand());
            // $create->contactobject->ownerDocument->formatOutput = true;
            // print_r($create->contactobject->ownerDocument->saveXML());
            exit();
        }

        $this->expectException('Metaregistrar\EPP\eppException', "Error 2001: Command syntax error; value:line: 2 column: 851 cvc-complex-type.2.4.a: Invalid content was found starting with element 'domain:ns'. One of '{\"urn:ietf:params:xml:ns:domain-1.0\":registrant, \"urn:ietf:params:xml:ns:domain-1.0\":authInfo}' is expected.");
        $this->assertFalse($response->Success());
    }

    /**
     * Test update of hosts on a domain name
     * Expects a standard result for a free domainname
     */
    public function testAddDomainHost()
    {
        $domain_name = 'a-add-test-'.self::randomstring(20) . '.eu';
        $this->createDomain($domain_name);
        $domain = new Metaregistrar\EPP\eppDomain($domain_name);
        $add = new Metaregistrar\EPP\eppDomain($domain_name);
        $a1 = new Metaregistrar\EPP\eppHost('ns1.metaregistrar.nl');
        $a2 = new Metaregistrar\EPP\eppHost('ns2.metaregistrar.nl');
        $add->addHost($a1);
        $add->addHost($a2);
        $del = null;
        $mod = null;

        $update = new Metaregistrar\EPP\eppUpdateDomainRequest($domain, $add, $del, $mod, $this->forcehostattr);
        // echo $update->saveXML();
        // exit();
        try {
            $response = $this->conn->writeandread($update);
        } catch (eppException $e) {
            print_r($e->getMessage());
            print_r($e->getLastCommand());
            exit();
        }

        $this->assertTrue($response->Success());
    }

    // public function testUpdateDomainRemoveAutorenew()
    // {
    //     $domainname = $this->createDomain();
    //     $update = new \Metaregistrar\EPP\metaregEppAutorenewRequest(new \Metaregistrar\EPP\eppDomain($domainname), false);
    //     $response = $this->conn->writeandread($update);
    //     $this->assertTrue($response->Success());
    //     $this->assertEquals(1000, $response->getResultCode());
    //     $this->assertEquals('Command completed succesfully', $response->getResultMessage());
    //     //echo $response->saveXML();
    // }
}
