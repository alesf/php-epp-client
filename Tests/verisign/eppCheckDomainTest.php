<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCheckDomainTest extends eppTestCase
{

    /**
     * Test if random domain name is available
     * Expects a standard result for a free domainname
     */
    public function testCheckDomainAvailable()
    {
        $domainname = self::randomstring(30).'.com';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\verisignEppCheckDomainRequest($domain);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckDomainRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckDomainResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckDomainResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedDomains();
                $this->assertCount(1, $checks);
                $check = $checks[0];
                $this->assertArrayHasKey('domainname', $check);
                $this->assertSame($domainname, $check['domainname']);
                $this->assertArrayHasKey('available', $check);
                $this->assertTrue($check['available']);
                $this->assertArrayHasKey('reason', $check);
                $this->assertNull($check['reason']);
            }
        }
    }

    /**
     * Test if nic.frl domain name is taken
     * Expects a standard result for a taken domain name
     */
    public function testCheckDomainTaken()
    {
        $domainname = 'test.com';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\verisignEppCheckDomainRequest($domain);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckDomainRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckDomainResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckDomainResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedDomains();
                $this->assertCount(1, $checks);
                $check = $checks[0];
                $this->assertArrayHasKey('domainname', $check);
                $this->assertSame($domainname, $check['domainname']);
                $this->assertArrayHasKey('available', $check);
                $this->assertFalse($check['available']);
                $this->assertArrayHasKey('reason', $check);
                $this->assertSame('Domain exists', $check['reason']);
            }
        }
    }

    /**
     * Test if test.frl domain name is reserved
     * Expects a standard result for a taken domain name
     */
    public function testCheckDomainReserved()
    {
        $domainname = 'test.com';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\verisignEppCheckDomainRequest($domain);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckDomainRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckDomainResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckDomainResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedDomains();
                $this->assertCount(1, $checks);
                $check = $checks[0];
                $this->assertArrayHasKey('domainname', $check);
                $this->assertSame($domainname, $check['domainname']);
                $this->assertArrayHasKey('available', $check);
                $this->assertFalse($check['available']);
                $this->assertArrayHasKey('reason', $check);
                $this->assertSame('Domain exists', $check['reason']);
            }
        }
    }

    /**
     * Test if test.frl domain name with illegal characters
     * Expects an error result domainname is invalid
     */
    public function testCheckDomainIllegalChars()
    {
        $domainname = 'test%test.com';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\verisignEppCheckDomainRequest($domain);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckDomainRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckDomainResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckDomainResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedDomains();
                $this->assertCount(1, $checks);
                $check = $checks[0];
                $this->assertArrayHasKey('domainname', $check);
                $this->assertSame($domainname, $check['domainname']);
                $this->assertArrayHasKey('available', $check);
                $this->assertFalse($check['available']);
                $this->assertArrayHasKey('reason', $check);
                $this->assertSame('Invalid Domain Name', $check['reason']);
            }
        }
    }

    /**
     * Test if test.frl domain name with illegal characters
     * Expects an error result domainname is invalid
     */
    public function testCheckDomainUnknownExtension()
    {
        $domainname = self::randomstring(30).'.abracadabra';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\verisignEppCheckDomainRequest($domain);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckDomainRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckDomainResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckDomainResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedDomains();
                $this->assertCount(1, $checks);
                $check = $checks[0];
                $this->assertArrayHasKey('domainname', $check);
                $this->assertSame($domainname, $check['domainname']);
                $this->assertArrayHasKey('available', $check);
                $this->assertFalse($check['available']);
                $this->assertArrayHasKey('reason', $check);
                $this->assertSame('Not an authoritative TLD', $check['reason']);
            }
        }
    }
}
