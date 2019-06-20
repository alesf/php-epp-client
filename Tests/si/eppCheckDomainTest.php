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
        $domainname = self::randomstring(30).'.si';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\eppCheckDomainRequest($domain);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckDomainRequest', $check);
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
        $domainname = self::randomstring(30).'.si';
        $this->createDomain($domainname);
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\eppCheckRequest($domain);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckResponse) {
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
                $this->assertSame('In use', $check['reason']);
            }
        }
    }

    /**
     * Test if test.frl domain name is reserved
     * Expects a standard result for a taken domain name
     * @group ignore_me
     */
    public function testCheckDomainReserved()
    {
        // TODO: check dfq is domain reserved
        $domainname = 'test.si';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\eppCheckRequest($domain);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckResponse) {
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
                $this->assertSame('In use', $check['reason']);
            }
        }
    }

    /**
     * Test if test.frl domain name with illegal characters
     * Expects an error result domainname is invalid
     */
    public function testCheckDomainIllegalChars()
    {
        $domainname = 'test%test.si';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\eppCheckRequest($domain);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckResponse) {
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
                $this->assertSame('Invalid name', $check['reason']);
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
        $check = new Metaregistrar\EPP\eppCheckRequest($domain);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckResponse) {
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
                $this->assertSame('Invalid name', $check['reason']);
            }
        }
    }
}
