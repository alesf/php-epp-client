<?php
namespace Metaregistrar\EPP;

class euridEppContact extends eppContact
{
    private $acceptedLangCodes = [
        'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'et', 'fi', 'fr', 'ga', 'hr',
        'hu', 'it', 'lt', 'lv', 'mt', 'nl', 'pl', 'pt', 'ro', 'sk', 'sl', 'sv'
    ];

    const CONTACT_EXT_TYPES = [
        'billing',
        'registrant',
        'tech',
        'onsite',
        'reseller',
    ];

    private $contactExtType;
    private $contactExtLang = 'en';
    private $contactExtVat  = null;

    /**
     *
     * @param eppContactPostalInfo $postalInfo
     * @param string $email
     * @param string $voice
     * @param string $fax
     * @param string $password
     * @param string $status
     */
    public function __construct($postalInfo = null, $email = null, $voice = null, $fax = null, $password = null, $status = null)
    {
        parent::__construct($postalInfo, $email, $voice, $fax, $password, $status);
    }

    public function setContactExtType($type)
    {
        if (in_array($type, self::CONTACT_EXT_TYPES)) {
            $this->contactExtType =  $type;
        } else {
            throw new \Exception('Contact ext type not supported.');
        }
    }

    public function setContactExtLang($lang)
    {
        if (in_array($lang, $this->acceptedLangCodes)) {
            $this->contactExtLang = $lang;
        } else {
            throw new \Exception('Contact language code not supported.');
        }
    }

    public function setContactExtVat($vat)
    {
        $this->contactExtVat =  $vat;
    }

    public function getContactExtType()
    {
        return $this->contactExtType;
    }

    public function getContactExtLang()
    {
        return $this->contactExtLang;
    }

    public function getContactExtVat()
    {
        return $this->contactExtVat;
    }
}
