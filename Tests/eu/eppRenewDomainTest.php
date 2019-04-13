<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppRenewDomainTest extends eppTestCase
{
    /**
     * Test successful domain renew
     */
    public function testRenewDomain()
    {
        // Prepare a domain name by creating it
        $domainname = $this->createDomain();
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $info = new Metaregistrar\EPP\eppInfoDomainRequest($domain);
        $info_response = $this->conn->writeandread($info);

        $period = 3;

        $domain->setPeriodUnit('y');
        $domain->setPeriod($period);
        $expdate = substr($info_response->getDomainExpirationDate(), 0, 10);

        $renew = new Metaregistrar\EPP\eppRenewRequest($domain, $expdate);

        $response = $this->conn->writeandread($renew);

        if ($response instanceof Metaregistrar\EPP\eppRenewResponse) {
            $this->assertTrue($response->Success());

            if ($response->Success()) {
                $domain = new Metaregistrar\EPP\eppDomain($domainname);
                $info = new Metaregistrar\EPP\eppInfoDomainRequest($domain);
                $info_response = $this->conn->writeandread($info);

                $new_expdate = substr($info_response->getDomainExpirationDate(), 0, 10);
                $check_expdate = date('Y-m-d', strtotime("$expdate +$period years"));
                $this->assertEquals($new_expdate, $check_expdate);
            }
        }
    }

    /**
     * Test successful domain renew
     */
    public function testRenewDomainMoreThanMaxYears()
    {
        // Prepare a domain name by creating it
        $domainname = $this->createDomain();

        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $info = new Metaregistrar\EPP\eppInfoDomainRequest($domain);
        $info_response = $this->conn->writeandread($info);

        $period = 10;

        $domain->setPeriodUnit('y');
        $domain->setPeriod($period);
        $expdate = substr($info_response->getDomainExpirationDate(), 0, 10);

        $renew = new Metaregistrar\EPP\eppRenewRequest($domain, $expdate);

        $response = $this->conn->writeandread($renew);

        if ($response instanceof Metaregistrar\EPP\eppRenewResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', 'Error 2306: Parameter value policy error');
            $this->assertFalse($response->Success());
        }
    }
}
