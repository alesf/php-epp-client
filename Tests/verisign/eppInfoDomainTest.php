<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppInfoDomainTest extends eppTestCase
{
    /**
     * @group failed
     * Test succesful domain info
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testInfoDomainSuccess()
    {
        // $domainname = 'nNGGug89D8hqSZFUPJ6a.com';
        $domainname = $this->createDomain();
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $info = new Metaregistrar\EPP\verisignEppInfoDomainRequest($domain);
        $info->setSubProduct('dotCOM');
        $response = $this->conn->writeandread($info);        
        $this->assertInstanceOf('Metaregistrar\EPP\eppInfoDomainResponse', $response);
        /* @var $response Metaregistrar\EPP\eppInfoDomainResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    /**
     * Test succesful domain info giving an authcode
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testInfoDomainWithAuthcode()
    {
        $domainname = $this->createDomain();
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $domain->setAuthorisationCode('DM$r5$$78');
        $info = new Metaregistrar\EPP\verisignEppInfoDomainRequest($domain);
        $info->setSubProduct('dotCOM');
        $response = $this->conn->writeandread($info);
        $this->assertInstanceOf('Metaregistrar\EPP\eppInfoDomainResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    /**
     * Test domain info without an authcode
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testInfoDomainWithoutAuthcode()
    {
        $domainname = $this->createDomain();
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $info = new Metaregistrar\EPP\verisignEppInfoDomainRequest($domain);
        $info->setSubProduct('dotCOM');
        $response = $this->conn->writeandread($info);
        $this->assertInstanceOf('Metaregistrar\EPP\eppInfoDomainResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }
}
