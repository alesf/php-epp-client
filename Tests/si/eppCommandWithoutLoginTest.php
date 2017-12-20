<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCommandWithoutLoginTest extends eppTestCase
{
    public function testCommandWithoutLogin()
    {
        $conn = new Metaregistrar\EPP\metaregEppConnection(false);
        $conn->setHostname('ssl://epp-test.register.si');
        $conn->setPort(65000);
        if ($conn->connect()) {
            $domain = new Metaregistrar\EPP\eppDomain('fasfasfasfashfgaf.guru');
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
