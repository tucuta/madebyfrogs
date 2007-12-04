<?php

/**
 * class SettingsController
 *
 * Enter description here...
 *
 * @author Philippe Archambault <philippe.archambault@gmail.com>
 * @since  0.8.7
 */

class SettingController extends Controller
{

    function __construct()
    {
        AuthUser::load();
        if ( ! AuthUser::isLoggedIn())
        {
            redirect(get_url('login'));
        }
        else if ( ! AuthUser::hasPermission('administrator'))
        {
            Flash::set('error', __('You don\'t have permissions to access requested page!'));
            redirect(get_url());
        }
        
        $this->setLayout('backend');
    }

    function index()
    {
        global $iso_639_1;
        
        // check if trying to save
        if (get_request_method() == 'POST')
            return $this->_save();
        
        $languages = array('en' => 'English');
        
        if ($handle = opendir(APP_PATH.'/i18n'))
        {
            while (false !== ($file = readdir($handle)))
            {
                if (strpos($file, '.') !== 0)
                {
                    $code = substr($file, 0, 2);
                    $languages[$code] = isset($iso_639_1[$code]) ? $iso_639_1[$code]: 'unknown';
                }
            }
            closedir($handle);
        }
        asort($languages);
        
        $this->display('setting/index', array('languages' => $languages));
    }

    function _save()
    {
        $data = $_POST['setting'];

        Setting::saveFromData($data);
        
        Flash::set('success', __('Settings has been saved!'));
        
        redirect(get_url('setting'));
    } // _save

} // end SettingController class

$GLOBALS['iso_639_1'] = array(
'aa' => 'Afar',
'ab' => 'Abkhazian',
'af' => 'Afrikaans',
'am' => 'Amharic',
'ar' => 'Arabic',
'as' => 'Assamese',
'ay' => 'Aymara',
'az' => 'Azerbaijani',
'ba' => 'Bashkir',
'be' => 'Byelorussian',
'bg' => 'Bulgarian',
'bh' => 'Bihari',
'bi' => 'Bislama',
'bn' => 'Bengali',
'bo' => 'Tibetan',
'br' => 'Breton',
'ca' => 'Catalan',
'co' => 'Corsican',
'cs' => 'Czech',
'cy' => 'Welsh',
'da' => 'Danish',
'de' => 'German',
'dz' => 'Bhutani',
'el' => 'Greek',
'en' => 'English',
'eo' => 'Esperanto',
'es' => 'Spanish',
'et' => 'Estonian',
'eu' => 'Basque',
'fa' => 'Persian',
'fi' => 'Finnish',
'fj' => 'Fiji',
'fo' => 'Faeroese',
'fr' => 'French',
'fy' => 'Frisian',
'ga' => 'Irish',
'gd' => 'Gaelic',
'gl' => 'Galician',
'gn' => 'Guarani',
'gu' => 'Gujarati',
'ha' => 'Hausa',
'hi' => 'Hindi',
'hr' => 'Croatian',
'hu' => 'Hungarian',
'hy' => 'Armenian',
'ia' => 'Interlingua',
'ie' => 'Interlingue',
'ik' => 'Inupiak',
'in' => 'Indonesian',
'is' => 'Icelandic',
'it' => 'Italian',
'iw' => 'Hebrew',
'ja' => 'Japanese',
'ji' => 'Yiddish',
'jw' => 'Javanese',
'ka' => 'Georgian',
'kk' => 'Kazakh',
'kl' => 'Greenlandic',
'km' => 'Cambodian',
'kn' => 'Kannada',
'ko' => 'Korean',
'ks' => 'Kashmiri',
'ku' => 'Kurdish',
'ky' => 'Kirghiz',
'la' => 'Latin',
'ln' => 'Lingala',
'lo' => 'Laothian',
'lt' => 'Lithuanian',
'lv' => 'Latvian',
'mg' => 'Malagasy',
'mi' => 'Maori',
'mk' => 'Macedonian',
'ml' => 'Malayalam',
'mn' => 'Mongolian',
'mo' => 'Moldavian',
'mr' => 'Marathi',
'ms' => 'Malay',
'mt' => 'Maltese',
'my' => 'Burmese',
'na' => 'Nauru',
'ne' => 'Nepali',
'nl' => 'Dutch',
'no' => 'Norwegian',
'oc' => 'Occitan',
'om' => 'Oromo',
'or' => 'Oriya',
'pa' => 'Punjabi',
'pl' => 'Polish',
'ps' => 'Pashto',
'pt' => 'Portuguese',
'qu' => 'Quechua',
'rm' => 'Rhaeto-Romance',
'rn' => 'Kirundi',
'ro' => 'Romanian',
'ru' => 'Russian',
'rw' => 'Kinyarwanda',
'sa' => 'Sanskrit',
'sd' => 'Sindhi',
'sg' => 'Sangro',
'sh' => 'Serbo-Croatian',
'si' => 'Singhalese',
'sk' => 'Slovak',
'sl' => 'Slovenian',
'sm' => 'Samoan',
'sn' => 'Shona',
'so' => 'Somali',
'sq' => 'Albanian',
'sr' => 'Serbian',
'ss' => 'Siswati',
'st' => 'Sesotho',
'su' => 'Sudanese',
'sv' => 'Swedish',
'sw' => 'Swahili',
'ta' => 'Tamil',
'te' => 'Tegulu',
'tg' => 'Tajik',
'th' => 'Thai',
'ti' => 'Tigrinya',
'tk' => 'Turkmen',
'tl' => 'Tagalog',
'tn' => 'Setswana',
'to' => 'Tonga',
'tr' => 'Turkish',
'ts' => 'Tsonga',
'tt' => 'Tatar',
'tw' => 'Twi',
'uk' => 'Ukrainian',
'ur' => 'Urdu',
'uz' => 'Uzbek',
'vi' => 'Vietnamese',
'vo' => 'Volapuk',
'wo' => 'Wolof',
'xh' => 'Xhosa',
'yo' => 'Yoruba',
'zh' => 'Chinese',
'zu' => 'Zulu');