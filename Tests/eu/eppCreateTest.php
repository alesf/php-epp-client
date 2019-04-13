<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateTest extends eppTestCase
{
    /**
     * Tests the class factory
     */
    public function testCreateInterface()
    {
        $conn = Metaregistrar\EPP\eppConnection::create(dirname(__FILE__).'/../testsetup/euridEPP.ini');
        $this->assertInstanceOf('Metaregistrar\EPP\euridEppConnection', $conn);
        /* @var $conn Metaregistrar\EPP\euridEppConnection */
        $this->assertEquals($conn->getHostname(), 'ssl://epp.tryout.registry.eu');
        $this->assertEquals($conn->getPort(), 700);
    }

    public function testCreateInterfaceFileNotFound()
    {
        $this->expectException('Metaregistrar\EPP\eppException', 'File not found: dejdkjedkjejd.ini');
        Metaregistrar\EPP\eppConnection::create('dejdkjedkjejd.ini');
    }

    public function testCreateInterfaceNoParam()
    {
        $this->expectException('Metaregistrar\EPP\eppException', 'Configuration file not specified on eppConnection:create');
        Metaregistrar\EPP\eppConnection::create(null);
    }
}
