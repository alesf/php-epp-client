<?php
namespace Metaregistrar\EPP;

class euridEppContact extends eppContact
{
    private $accepted_lang_codes = [
        'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'et', 'fi', 'fr', 'ga', 'hr',
        'hu', 'it', 'lt', 'lv', 'mt', 'nl', 'pl', 'pt', 'ro', 'sk', 'sl', 'sv'
    ];

    const EXT_TYPE_BILLING = 'billing';
    const EXT_TYPE_TECH = 'tech';
    const EXT_TYPE_REGISTRANT = 'registrant';
    const EXT_TYPE_ONSITE = 'onsite';
    const EXT_TYPE_RESELLER = 'reseller';

    private $ext_type;
    private $ext_lang = 'en';
    private $ext_vat;

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

    public function setExtType($type)
    {
        $this->ext_type = $type;
    }

    public function getExtType()
    {
        return $this->ext_type;
    }

    public function setExtVat($vat)
    {
        $this->ext_vat = $vat;
    }

    public function getExtVat()
    {
        return $this->ext_vat;
    }

    public function setExtLang($lang)
    {
        if (in_array($lang, $this->accepted_lang_codes)) {
            $this->ext_lang = $lang;
        } else {
            throw new \Exception('Contact language code not supported.');
        }
    }

    public function getExtLang()
    {
        return $this->ext_lang;
    }
}
