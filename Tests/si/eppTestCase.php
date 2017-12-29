<?php
use Metaregistrar\EPP\eppException;

require(dirname(__FILE__).'/../../autoloader.php');

class eppTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Metaregistrar\EPP\eppConnection
     *
     */
    protected $conn;

    protected function setUp($configfile = null)
    {
        if (!$configfile) {
            $configfile = dirname(__FILE__).'/testsetup.ini';
        }
        $this->conn = self::setupConnection($configfile);
    }

    protected function tearDown()
    {
        self::teardownConnection($this->conn);
    }

    private static function setupConnection($configfile)
    {
        try {
            if ($conn = Metaregistrar\EPP\siEppConnection::create($configfile)) {
                /* @var $conn Metaregistrar\EPP\eppConnection */
                //$conn->enableRgp();
                if ($conn->login()) {
                    return $conn;
                }
            }
        } catch (Metaregistrar\EPP\eppException $e) {
            echo "Test setup error in ".$e->getClass().": " . $e->getMessage() . "\n\n";
            die();
        }
        return null;
    }

    /**
     * @param Metaregistrar\EPP\eppConnection $conn
     */
    private static function teardownConnection($conn)
    {
        if ($conn) {
            $conn->logout();
        }
    }

    protected static function randomstring($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected static function randomnumber($length)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Create a hostname to be used in create host or create domain testing
     * @var string $hostname
     * @return string
     * @throws \Metaregistrar\EPP\eppException
     */
    protected function createHost($hostname = null)
    {
        if (!$hostname) {
            $hostname = 'ns1.'.self::randomstring(30).'.net';
        }
        $host = new Metaregistrar\EPP\eppHost($hostname);
        // $host->setIpAddress('81.4.97.247');
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        if ($response = $this->conn->request($create)) {
            /* @var $response Metaregistrar\EPP\eppCreateHostResponse */
            return $hostname;
        }
        return null;
    }

    /**
     * Create a contact to be used in create contact or create domain testing
     * @return string
     * @throws \Metaregistrar\EPP\eppException
     */
    protected function createContact()
    {
        $name = 'Test name';
        $city = 'Celje';
        $country = 'SI';
        $organization = 'Test company';
        $address = 'Teststreet 1';
        $province = '';
        $postcode = '1000';
        $email = 'ewout@mdmailaaaa.si';
        $telephone = '+386.31222222';
        $password = self::randomstring(8);
        $postalinfo = new Metaregistrar\EPP\siEppContactPostalInfo($name, $city, $country, $organization, $address, $province, $postcode, Metaregistrar\EPP\eppContact::TYPE_INT);
        $postalinfo->setContactType(Metaregistrar\EPP\siEppContactPostalInfo::ARNES_CONTACT_TYPE_PERSON);
        $postalinfo->setContactID('2010982500111');
        $contactinfo = new Metaregistrar\EPP\eppContact($postalinfo, $email, $telephone);
        $contactinfo->setPassword($password);
        $create = new Metaregistrar\EPP\siEppCreateContactRequest($contactinfo);

        // print_r($create->contactobject->ownerDocument->saveXML());
        // exit();
        $response = null;

        try {
            $response = $this->conn->request($create);
        } catch (eppException $e) {
            $create->contactobject->ownerDocument->formatOutput = true;
            print_r($create->contactobject->ownerDocument->saveXML());

            print_r($e->getLastCommand());
            exit();
        }

        if ($response) {
            return $response->getContactId();
        }
        return null;
    }

    protected function createDns($domainname = null)
    {
        $domainname = $this->createDomain($domainname);
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $records[] = ['name' => $domainname, 'type' => 'A', 'content' => '127.0.0.1', 'ttl' => 3600];
        $create = new Metaregistrar\EPP\eppCreateDnsRequest($domain, $records);
        //echo $create->saveXML();
        $response = $this->conn->writeandread($create);
        //echo $response->saveXML();
        $this->assertInstanceOf('Metaregistrar\EPP\metaregCreateDnsResponse', $response);
        /* @var $response Metaregistrar\EPP\metaregCreateDnsResponse */
        return $domainname;
    }

    protected function createDomain($domainname = null)
    {
        // If no domain name was given, test with a random .si domain name
        if (!$domainname) {
            $domainname = $this->randomstring(20).'.si';
        }

        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $domain->setPeriod(1);
        $domain->setAuthorisationCode('fubar01');

        $contactid = $this->createContact();
        $domain->setRegistrant($contactid);
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));

        $hostname = $this->createHost();
        $host = new \Metaregistrar\EPP\eppHost($hostname);
        $domain->addHost($host);

        $hostname = $this->createHost();
        $host = new \Metaregistrar\EPP\eppHost($hostname);
        $domain->addHost($host);

        $create = new \Metaregistrar\EPP\siEppCreateDomainRequest($domain);

        $response = $this->conn->writeandread($create);

        if ($response) {
            /* @var $response \Metaregistrar\EPP\eppCreateDomainResponse */
            return $response->getDomainName();
        }
        return null;
    }

    protected function deleteDomain($domainname)
    {
        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $delete = new \Metaregistrar\EPP\eppDeleteDomainRequest($domain);
        if ($response = $this->conn->request($delete)) {
            if ($response->getResultCode() == 1000) {
                return true;
            }
        }
        return false;
    }

    protected function updateDomain($domainname, $data)
    {
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $add = null;
        $del = null;
        $mod = null;
        foreach ($data as $type => $var) {
            if (!empty($var)) {
                $$type = new Metaregistrar\EPP\eppDomain($domainname);
            }
            if (isset($var['hosts'])) {
                foreach ($var['hosts'] as $host) {
                    $host = new Metaregistrar\EPP\eppHost($host);
                    $$type->addHost($host);
                }
            }
            if (isset($var['auth'])) {
                $$type->setAuthorisationCode($var['auth']);
            }
        }

        $update = new Metaregistrar\EPP\eppUpdateDomainRequest($domain, $add, $del, $mod, true);
        echo $update->saveXML();
        // $response = $this->conn->writeandread($update);
        if ($response = $this->conn->request($update)) {
            if ($response->getResultCode() == 1000) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets information on a contact handle
     * @param $contacthandle
     * @return \Metaregistrar\EPP\eppInfoContactResponse|\Metaregistrar\EPP\eppResponse
     * @throws \Metaregistrar\EPP\eppException
     */
    protected function getContactInfo($contacthandle)
    {
        $epp = new Metaregistrar\EPP\eppContactHandle($contacthandle);
        $info = new Metaregistrar\EPP\eppInfoContactRequest($epp);
        if ((($response = $this->conn->writeandread($info)) instanceof Metaregistrar\EPP\eppInfoContactResponse) && ($response->Success())) {
            /* @var $response Metaregistrar\EPP\eppInfoContactResponse */
            return $response;
        }
        return null;
    }
}
