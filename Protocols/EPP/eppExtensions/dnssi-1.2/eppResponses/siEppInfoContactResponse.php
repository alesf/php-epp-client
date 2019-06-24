<?php
namespace Metaregistrar\EPP;

class siEppInfoContactResponse extends eppInfoContactResponse
{
    /**
     *
     * @return string fax_telephone_number
     */
    public function getContactType() {
        return $this->queryPath('/epp:epp/epp:response/epp:extension/dnssi:ext/dnssi:info/dnssi:contact/@type');
    }

    /**
     *
     * @return array
     */
    public function getContactPostalInfo()
    {
        $xpath = $this->xPath();
        $result = $xpath->query('/epp:epp/epp:response/epp:resData/contact:infData/contact:postalInfo');
        $postalinfo = [];
        foreach ($result as $postalresult) {
            /* @var $postalresult \DOMElement */
            $testtype = $postalresult->getAttributeNode('type');
            $type = eppContact::TYPE_LOC;
            if ($testtype) {
                $type = $testtype->value;
            }
            $testname = $postalresult->getElementsByTagName('name');
            $name = null;
            if ($testname->length > 0) {
                $name = $testname->item(0)->nodeValue;
            }
            $testorg = $postalresult->getElementsByTagName('org');
            $org = null;
            if ($testorg->length > 0) {
                $org = $testorg->item(0)->nodeValue;
            }
            $city = null;
            $country = null;
            $zipcode = null;
            $province = null;
            $streets = null;
            $testaddr = $postalresult->getElementsByTagName('addr');
            if ($testaddr->length > 0) {
                $addr = $testaddr->item(0);
                /* @var $addr \DOMElement */
                $testcity = $addr->getElementsByTagName('city');
                /* @var $postalresult \DOMElement */

                if ($testcity->length > 0) {
                    $city = $testcity->item(0)->nodeValue;
                }
                $testcc = $addr->getElementsByTagName('cc');

                if ($testcc->length > 0) {
                    $country = $testcc->item(0)->nodeValue;
                }
                $testpc = $addr->getElementsByTagName('pc');

                if ($testpc->length > 0) {
                    $zipcode = $testpc->item(0)->nodeValue;
                }
                $testsp = $addr->getElementsByTagName('sp');

                if ($testsp->length > 0) {
                    $province = $testsp->item(0)->nodeValue;
                }
                $teststreet = $addr->getElementsByTagName('street');
                if ($teststreet->length > 0) {
                    foreach ($teststreet as $street) {
                        $streets[] = $street->nodeValue;
                    }
                }
            }
            $contactType = $this->getContactType();
            $postalinfo[] = new siEppContactPostalInfo($name, $city, $country, $org, $streets, $province, $zipcode, $type, $contactType);
        }
        return $postalinfo;
    }
}
