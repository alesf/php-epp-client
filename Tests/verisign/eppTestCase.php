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
            if ($conn = Metaregistrar\EPP\verisignEppConnection::create($configfile)) {
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

    protected static function randomstring($length, $pass = false)
    {
        if ($pass) {
            return self::generateStrongPassword($length, true, 'luds');
        }
        return self::generateStrongPassword($length, false, 'lud');
    }

    protected static function randomnumber($length)
    {
        return self::generateStrongPassword($length, false, 'd');
    }

    protected static function generateStrongPassword($length = 15, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }
        if (strpos($available_sets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }
        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }
        if (strpos($available_sets, 's') !== false) {
            $sets[] = '!@#$%&*?';
        }
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[self::tweak_array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[self::tweak_array_rand($all)];
        }
        $password = str_shuffle($password);
        if (!$add_dashes) {
            return $password;
        }
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    protected static function tweak_array_rand($array)
    {
        if (function_exists('random_int')) {
            return random_int(0, count($array) - 1);
        } elseif (function_exists('mt_rand')) {
            return mt_rand(0, count($array) - 1);
        } else {
            return array_rand($array);
        }
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
            $hostname = self::randomstring(30).'.HCtM7kDdxtU3dBNXzy2X.com';
        }                
        $ipaddresses = ['8.8.8.8'] ;
        $host = new Metaregistrar\EPP\eppHost($hostname, $ipaddresses);        
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');        
        $response = $this->conn->writeandread($create);       
        if ($response->Success()) {            
            return $hostname;
        }
        return null;
    }

    /**
     * Create a contact to be used in create contact or create domain testing
     * @return string
     * @throws \Metaregistrar\EPP\eppException
     */
    protected function createContact($subProduct = 'dotCOM', $password = null)
    {
        $name = 'Test name';
        $city = 'Celje';
        $country = 'SI';
        $organization = 'Test company';
        $address = 'Teststreet 1';
        $province = '';
        $postcode = '1000';
        $email = 'ewout@mdmailaaaa.com';
        $telephone = '+386.31222222';
        $password = $password ? $password : self::randomstring(8, true);
        $postalinfo = new Metaregistrar\EPP\eppContactPostalInfo($name, $city, $country, $organization, $address, $province, $postcode, Metaregistrar\EPP\eppContact::TYPE_INT);
        $contactinfo = new Metaregistrar\EPP\eppContact($postalinfo, $email, $telephone);
        $contactinfo->setPassword($password);
        $create = new Metaregistrar\EPP\verisignEppCreateContactRequest($contactinfo);
        $create->setSubProduct($subProduct);

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
        $create = new Metaregistrar\EPP\metaregCreateDnsRequest($domain, $records); 
        // echo $create->saveXML();                       
        $response = $this->conn->request($create);
        // echo $response;
        return $domainname;
    }

    protected function createDomain($domainname = null)
    {
        // If no domain name was given, test with a random .com domain name
        if (!$domainname) {
            $domainname = $this->randomstring(20).'.com';
        }
        
        $domain = new \Metaregistrar\EPP\eppDomain($domainname);
        $domain->setPeriod(1);
        $domain->setAuthorisationCode('DM$r5$$78');

        $contactid = $this->createContact();        
        $domain->setRegistrant($contactid);
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));

        // $hostname = $this->createHost($this->randomstring(20).'.'.$domainname);        
        // $host = new \Metaregistrar\EPP\eppHost($hostname);
        // $domain->addHost($host);

        // $hostname = $this->createHost($this->randomstring(20).'.'.$domainname);
        // $host = new \Metaregistrar\EPP\eppHost($this->randomstring(20).'.'.$domainname);
        // $domain->addHost($host);

        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');        
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
        $delete = new \Metaregistrar\EPP\verisignEppDeleteDomainRequest($domain);
        $delete->setSubProduct('dotCOM'); // TODO: check which domain extension the domain has
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

        $update = new Metaregistrar\EPP\verisignEppUpdateDomainRequest($domain, $add, $del, $mod, true);
        $update->setSubProduct('dotCOM'); // TODO: correct ending        
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
        $info = new Metaregistrar\EPP\verisignEppInfoContactRequest($epp);
        $info->setSubProduct('dotCOM');
        if ((($response = $this->conn->writeandread($info)) instanceof Metaregistrar\EPP\eppInfoContactResponse) && ($response->Success())) {
            /* @var $response Metaregistrar\EPP\eppInfoContactResponse */
            return $response;
        }
        return null;
    }
}
