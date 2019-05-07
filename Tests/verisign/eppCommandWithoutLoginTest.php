<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCommandWithoutLoginTest extends eppTestCase
{
    public function testCommandWithoutLogin()
    {
        $conn = new Metaregistrar\EPP\verisignEppConnection(false);
        $conn->setHostname('ssl://epp-ote.verisign-grs.com');
        $conn->setPort(700);
        $conn->enableCertification('/home/samot/projects/php-epp-client/Tests/verisign/core.storkregistry.com.CA.pem', null, true);
        if ($conn->connect()) {
            $domain = new Metaregistrar\EPP\eppDomain('fasfasfasfashfgaf.com');
            $info = new Metaregistrar\EPP\eppInfoDomainRequest($domain);
            $response = $conn->writeandread($info);
            $this->assertInstanceOf('Metaregistrar\EPP\eppInfoResponse', $response);
            if ($response instanceof Metaregistrar\EPP\eppInfoResponse) {
                $this->expectException('Metaregistrar\epp\eppException', 'Error 2202: Invalid authorization information');
                $this->assertFalse($response->Success());
            }
        }
    }
}
