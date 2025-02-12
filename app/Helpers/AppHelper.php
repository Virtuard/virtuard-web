<?php
use App\Currency;
use App\User;
use App\Models\UserPost;
use App\Models\FollowUser;
use App\Models\MediaFile;
use Carbon\Carbon;
use Modules\Art\Models\Art;
use Modules\Core\Models\Attributes;
use Modules\Core\Models\Terms;
use Modules\Core\Models\Settings;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

//include '../../custom/Helpers/CustomHelper.php';

define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
define( 'MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS );
define( 'YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS );

function setting_item($item,$default = '',$isArray = false){

    $res = Settings::item($item,$default);

    if($isArray and !is_array($res)){
        $res = (array) json_decode($res,true);
    }

    return $res;

}
function setting_item_array($item,$default = ''){

    return setting_item($item,$default,true);

}

function setting_item_with_lang($item,$locale = '',$default = '',$withOrigin = true){

    if(empty($locale)) $locale = app()->getLocale();

    if($withOrigin == false and $locale == setting_item('site_locale')){
        return $default;
    }

    if( empty(setting_item('site_locale'))
        OR empty(setting_item('site_enable_multi_lang'))
        OR  $locale == setting_item('site_locale')
    ){
        $locale = '';
    }

    return Settings::item($item.($locale ? '_'.$locale : ''),$withOrigin ? setting_item($item,$default) : $default);

}
function setting_item_with_lang_raw($item,$locale = '',$default = ''){

    return setting_item_with_lang($item,$locale,$default,false);
}
function setting_update_item($item,$val){

    $s = Settings::where('name',$item)->first();
    if(empty($s)){
        $s = new Settings();
        $s->name = $item;
    }

    if(is_array($val) or is_object($val)) $val = json_encode($val);
    $s->val = $val;

    $s->save();

    Cache::forget('setting_' . $item);

    return $s;
}

function app_get_locale($locale = false , $before = false , $after = false){
    if(setting_item('site_enable_multi_lang') and app()->getLocale() != setting_item('site_locale')){
        return $locale ? $before.$locale.$after : $before.app()->getLocale().$after;
    }
    return '';
}

function format_money($price){

   return Currency::format((float)$price);

}
function format_money_main($price){

   return Currency::format((float)$price,true);

}

function currency_symbol(){

    $currency_main = get_current_currency('currency_main');

    $currency = Currency::getCurrency($currency_main);

    return $currency['symbol'] ?? '';
}

function generate_menu($location = '',$options = [])
{
    $options['walker'] = $options['walker'] ?? '\\Modules\\Core\\Walkers\\MenuWalker';

    $setting = json_decode(setting_item('menu_locations'),true);

    if(!empty($setting))
    {
        foreach($setting as $l=>$menuId){
            if($l == $location and $menuId){
                $menu = (new \Modules\Core\Models\Menu())->findById($menuId);
                $translation = $menu->translate();

                $walker = new $options['walker']($translation);

                if(!empty($translation)){
                    $walker->generate($options);
                }
            }
        }
    }
}

function set_active_menu($item){
    \Modules\Core\Walkers\MenuWalker::setCurrentMenuItem($item);
}

 function get_exceprt($string,$length=200,$more = "[...]"){
        $string=strip_tags($string);
        if(str_word_count($string)>0) {
            $arr=explode(' ',$string);
            $excerpt='';
            if(count($arr)>0) {
                $count=0;
                if($arr) foreach($arr as $str) {
                    $count+=strlen($str);
                    if($count>$length) {
                        $excerpt.= $more;
                        break;
                    }
                    $excerpt.=' '.$str;
                }
                }return $excerpt;
            }
}

function getDatefomat($value) {
    return \Carbon\Carbon::parse($value)->format('j F, Y');

}

function get_file_url($file_id,$size="thumb",$resize = true){
    if(empty($file_id)) return null;
    return \Modules\Media\Helpers\FileHelper::url($file_id,$size,$resize);
}

function get_image_tag($image_id,$size = 'thumb',$options = []){
    $options = array_merge([
       'lazy'=>true
    ],$options);
    $url = get_file_url($image_id,$size);

    if($url){
        $alt = $options['alt'] ?? '';
        $attr = '';
        $class= $options['class'] ?? '';
        if(!empty($options['lazy'])){
            $class.=' lazy';
            $attr.=" data-src=".e($url)." ";
        }else{
            $attr.=" src='".e($url)."' ";
        }
        return sprintf("<img class='%s' %s alt='%s'>",e($class),$attr,e($alt));
    }
}
function get_date_format(){
    return setting_item('date_format','m/d/Y');
}
function get_moment_date_format(){
    return php_to_moment_format(get_date_format());
}
function php_to_moment_format($format){

    $replacements = [
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', // no equivalent
        'L' => '', // no equivalent
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '', // no equivalent
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz', // deprecated since version 1.6.0 of moment.js
        'I' => '', // no equivalent
        'O' => '', // no equivalent
        'P' => '', // no equivalent
        'T' => '', // no equivalent
        'Z' => '', // no equivalent
        'c' => '', // no equivalent
        'r' => '', // no equivalent
        'U' => 'X',
    ];
    $momentFormat = strtr($format, $replacements);
    return $momentFormat;
}

function display_date($time){

    if($time){
        if(is_string($time)){
            $time = strtotime($time);
        }

        if(is_object($time)){
            return $time->format(get_date_format());
        }
    }else{
       $time=strtotime(today());
    }

    return date(get_date_format(),$time);
}

function display_datetime($time){

    if(is_string($time)){
        $time = strtotime($time);
    }

    if(is_object($time)){
        return $time->format(get_date_format().' H:i');
    }

    return date(get_date_format().' H:i',$time);
}

function human_time_diff($from,$to = false){

    if(is_string($from)) $from = strtotime($from);
    if(is_string($to)) $to = strtotime($to);

    if ( empty( $to ) ) {
        $to = time();
    }

    $diff = (int) abs( $to - $from );

    if ( $diff < HOUR_IN_SECONDS ) {
        $mins = round( $diff / MINUTE_IN_SECONDS );
        if ( $mins <= 1 ) {
            $mins = 1;
        }
        /* translators: Time difference between two dates, in minutes (min=minute). %s: Number of minutes */
        if($mins){
            $since =__(':num mins',['num'=>$mins]);
        }else{
            $since =__(':num min',['num'=>$mins]);
        }

    } elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
        $hours = round( $diff / HOUR_IN_SECONDS );
        if ( $hours <= 1 ) {
            $hours = 1;
        }
        /* translators: Time difference between two dates, in hours. %s: Number of hours */
        if($hours){
            $since =__(':num hours',['num'=>$hours]);
        }else{
            $since =__(':num hour',['num'=>$hours]);
        }

    } elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
        $days = round( $diff / DAY_IN_SECONDS );
        if ( $days <= 1 ) {
            $days = 1;
        }
        /* translators: Time difference between two dates, in days. %s: Number of days */
        if($days){
            $since =__(':num days',['num'=>$days]);
        }else{
            $since =__(':num day',['num'=>$days]);
        }

    } elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
        $weeks = round( $diff / WEEK_IN_SECONDS );
        if ( $weeks <= 1 ) {
            $weeks = 1;
        }
        /* translators: Time difference between two dates, in weeks. %s: Number of weeks */
        if($weeks){
            $since =__(':num weeks',['num'=>$weeks]);
        }else{
            $since =__(':num week',['num'=>$weeks]);
        }

    } elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
        $months = round( $diff / MONTH_IN_SECONDS );
        if ( $months <= 1 ) {
            $months = 1;
        }
        /* translators: Time difference between two dates, in months. %s: Number of months */

        if($months){
            $since =__(':num months',['num'=>$months]);
        }else{
            $since =__(':num month',['num'=>$months]);
        }

    } elseif ( $diff >= YEAR_IN_SECONDS ) {
        $years = round( $diff / YEAR_IN_SECONDS );
        if ( $years <= 1 ) {
            $years = 1;
        }
        /* translators: Time difference between two dates, in years. %s: Number of years */
        if($years){
            $since =__(':num years',['num'=>$years]);
        }else{
            $since =__(':num year',['num'=>$years]);
        }
    }

    return $since;
}

function human_time_diff_short($from,$to = false){
    if(!$to) $to = time();
    $today = strtotime(date('Y-m-d 00:00:00',$to));

    $diff = $from - $to;

    if($from > $today){
        return date('h:i A',$from);
    }

    if($diff < 5* DAY_IN_SECONDS){
        return date('D',$from);
    }

    return date('M d',$from);
}

function _n($l,$m,$count){
    if($count){
        return $m;
    }
    return $l;
}
function get_country_lists(){
    $countries = array
    (
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua And Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia And Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island & Mcdonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic Of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle Of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States Of',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts And Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre And Miquelon',
        'VC' => 'Saint Vincent And Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome And Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia And Sandwich Isl.',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard And Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad And Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks And Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis And Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );
    return $countries;
}

function get_country_name($name){
    $all = get_country_lists();

    return $all[$name] ?? $name;
}

function get_page_url($page_id)
{
    $page = \Modules\Page\Models\Page::find($page_id);

    if($page){
        return $page->getDetailUrl();
    }
    return false;
}

function get_payment_gateway_obj($payment_gateway){

    $gateways = get_payment_gateways();

    if(empty($gateways[$payment_gateway]) or !class_exists($gateways[$payment_gateway]))
    {
        return false;
    }

    $gatewayObj = new $gateways[$payment_gateway]($payment_gateway);

    return $gatewayObj;

}

function recaptcha_field($action){
    return \App\Helpers\ReCaptchaEngine::captcha($action);
}

function add_query_arg($args,$uri = false) {

    if(empty($uri)) $uri = request()->url();

    $query = request()->query();

    if(!empty($args)){
        foreach ($args as $k=>$arg){
            $query[$k] = $arg;
        }
    }

    return $uri.'?'.http_build_query($query);
}

function is_default_lang($lang = '')
{
    if(!$lang) $lang = request()->query('lang');
    if(!$lang) $lang = request()->route('lang');

    if(empty($lang) or $lang == setting_item('site_locale')) return true;

    return false;
}

function get_lang_switcher_url($locale = false){

    $request =  request();
    $data = $request->query();
    $data['set_lang'] = $locale;

    $url = url()->current();

    $url.='?'.http_build_query($data);

    return url($url);
}
function get_currency_switcher_url($code = false){

    $request =  request();
    $data = $request->query();
    $data['set_currency'] = $code;

    $url = url()->current();

    $url.='?'.http_build_query($data);

    return url($url);
}


function translate_or_origin($key,$settings = [],$locale = '')
{
    if(empty($locale)) $locale = request()->query('lang');

    if($locale and $locale == setting_item('site_locale')) $locale = false;

    if(empty($locale)) return $settings[$key] ?? '';
    else{
        return $settings[$key.'_'.$locale] ?? '';
    }
}

function get_bookable_services(){

    $all = [];

    // Modules
    $custom_modules = \Modules\ServiceProvider::getActivatedModules();
    if(!empty($custom_modules)){
        foreach($custom_modules as $moduleData){
            $moduleClass = $moduleData['class'];
            if(class_exists($moduleClass))
            {
                $services = call_user_func([$moduleClass,'getBookableServices']);
                $all = array_merge($all,$services);
            }

        }
    }


    // Plugin Menu
    $plugins_modules = \Plugins\ServiceProvider::getModules();
    if(!empty($plugins_modules)){
        foreach($plugins_modules as $module){
            $moduleClass = "\\Plugins\\".ucfirst($module)."\\ModuleProvider";
            if(class_exists($moduleClass))
            {
                $services = call_user_func([$moduleClass,'getBookableServices']);
                $all = array_merge($all,$services);
            }
        }
    }
    foreach ($all as $id=>$class){
        $all[$id] = get_class(app()->make($class));

        // if (!in_array($id, menu_listing())) {
        //     unset($all[$id]);
        // }
    }

    return $all;
}
function get_payable_services(){
    $all = get_bookable_services();

    // Modules
    $custom_modules = \Modules\ServiceProvider::getActivatedModules();
    if(!empty($custom_modules)){
        foreach($custom_modules as $moduleData){
            $moduleClass = $moduleData['class'];
            if(class_exists($moduleClass))
            {
                $services = call_user_func([$moduleClass,'getPayableServices']);
                $all = array_merge($all,$services);
            }

        }
    }

    foreach ($all as $id=>$class){
        $all[$id] = get_class(app()->make($class));
    }

    return $all;
}
function get_reviewable_services(){

    $all = get_bookable_services();
    // Modules
    $custom_modules = \Modules\ServiceProvider::getActivatedModules();
    if(!empty($custom_modules)){
        foreach($custom_modules as $moduleData){
            $moduleClass = $moduleData['class'];
            if(class_exists($moduleClass))
            {
                $services = call_user_func([$moduleClass,'getReviewableServices']);
                $all = array_merge($all,$services);
            }

        }
    }

    foreach ($all as $id=>$class){
        $all[$id] = get_class(app()->make($class));
    }

    return $all;
}
function get_bookable_service_by_id($id){

    $all = get_bookable_services();

    return $all[$id] ?? null;
}

function file_get_contents_curl($url,$isPost = false,$data = []) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    if($isPost){
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function size_unit_format($number=''){
    switch (setting_item('size_unit')){
        case "m2":
            return $number." m<sup>2</sup>";
            break;
        default:
            return $number." ".__('sqft');
            break;
    }
}

function get_payment_gateways(){
    $gateways = config('payment.gateways');
    // Modules
    $custom_modules = \Modules\ServiceProvider::getModules();
    if(!empty($custom_modules)){
        foreach($custom_modules as $module){
            $moduleClass = "\\Modules\\".ucfirst($module)."\\ModuleProvider";
            if(class_exists($moduleClass))
            {
                $gateway = call_user_func([$moduleClass,'getPaymentGateway']);
                if(!empty($gateway)){
                    $gateways = array_merge($gateways,$gateway);
                }
            }
        }
    }
    //Plugin
    $plugin_modules = \Plugins\ServiceProvider::getModules();
    if(!empty($plugin_modules)){
        foreach($plugin_modules as $module){
            $moduleClass = "\\Plugins\\".ucfirst($module)."\\ModuleProvider";
            if(class_exists($moduleClass))
            {
                $gateway = call_user_func([$moduleClass,'getPaymentGateway']);
                if(!empty($gateway)){
                    $gateways = array_merge($gateways,$gateway);
                }
            }
        }
    }

    foreach ($gateways as $id=>$class){
        $gateways[$id] = get_class(app()->make($class));
    }
    return $gateways;
}

function get_current_currency($need,$default = '')
{
    return Currency::getCurrent($need,$default);
}

function booking_status_to_text($status)
{
    switch ($status){
        case "draft":
            return __('Draft');
            break;
        case "unpaid":
            return __('Unpaid');
            break;
        case "paid":
            return __('Paid');
            break;
        case "processing":
            return __('Processing');
            break;
        case "completed":
            return __('Completed');
            break;
        case "confirmed":
            return __('Confirmed');
            break;
        case "cancelled":
            return __('Cancelled');
            break;
        case "cancel":
            return __('Cancel');
            break;
        case "pending":
            return __('Pending');
            break;
        case "partial_payment":
            return __('Partial Payment');
            break;
        case "fail":
            return __('Failed');
            break;
        default:
            return ucfirst($status ?? '');
            break;
    }
}
function verify_type_to($type,$need = 'name')
{
    switch ($type){
        case "phone":
            return __("Phone");
            break;
        case "number":
            return __("Number");
            break;
        case "email":
            return __("Email");
            break;
        case "file":
            return __("Attachment");
            break;
        case "multi_files":
            return __("Multi Attachments");
            break;
        case "text":
        default:
            return __("Text");
            break;
    }
}

function get_all_verify_fields(){
    return setting_item_array('role_verify_fields');
}
/*Hook Functions*/
function add_action($hook, $callback, $priority = 20, $arguments = 1){
    return \Modules\Core\Facades\Hook::addAction($hook, $callback, $priority, $arguments);
}
function add_filter($hook, $callback, $priority = 20, $arguments = 1){
    return \Modules\Core\Facades\Hook::addFilter($hook, $callback, $priority, $arguments);
}
function do_action(){
    return \Modules\Core\Facades\Hook::action(...func_get_args());
}
function apply_filters(){
    return \Modules\Core\Facades\Hook::filter(...func_get_args());
}
function is_installed(){
    return file_exists(storage_path('installed'));
}
function is_enable_multi_lang(){
    return (bool) setting_item('site_enable_multi_lang');
}

function is_enable_language_route(){
    return (is_installed() and is_enable_multi_lang() and app()->getLocale() != setting_item('site_locale'));
}

function duration_format($hour,$is_full = false)
{
    $day = floor($hour / 24) ;
    $hour = $hour % 24;
    $tmp = '';

    if($day) $tmp = $day.__('D');

    if($hour)
    $tmp .= $hour.__('H');

    if($is_full){
        $tmp = [];
        if($day){
            if($day > 1){
                $tmp[] = __(':count Days',['count'=>$day]);
            }else{
                $tmp[] = __(':count Day',['count'=>$day]);
            }
        }
        if($hour){
            if($hour > 1){
                $tmp[] = __(':count Hours',['count'=>$hour]);
            }else{
                $tmp[] = __(':count Hour',['count'=>$hour]);
            }
        }

        $tmp = implode(' ',$tmp);
    }

    return $tmp;
}
function is_enable_guest_checkout(){
    return setting_item('booking_guest_checkout');
}

function handleVideoUrl($string,$video_id = false)
{
    if($video_id && !empty($string)){
        parse_str( parse_url( $string, PHP_URL_QUERY ), $values );
        return $values['v'];
    }
    if (strpos($string, 'youtu') !== false) {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $string, $matches);
        if (!empty($matches[0])) return "https://www.youtube.com/embed/" . e($matches[0]);
    }
    return $string;
}

function is_api(){
    return request()->segment(1) == 'api';
}

function is_demo_mode(){
    return env('DEMO_MODE',false);
}
function credit_to_money($amount){
    return $amount * setting_item('wallet_credit_exchange_rate',1);
}

function money_to_credit($amount,$roundUp = false){
    $res = $amount / setting_item('wallet_credit_exchange_rate',1);

    if($roundUp) return ceil($res);

    return $res;
}

function clean_by_key($object, $keyIndex, $children = 'children'){
    if(is_string($object)){
        return clean($object);
    }

    if(is_array($object)){
        if(isset($object[$keyIndex])){
            $newClean = clean($object[$keyIndex]);
            $object[$keyIndex] =  $newClean;
            if(!empty($object[$children])){
                $object[$children] = clean_by_key($object[$children], $keyIndex);
            }

        }else{
            foreach($object as $key => $oneObject){
                if(isset($oneObject[$keyIndex])){
                    $newClean = clean($oneObject[$keyIndex]);
                    $object[$key][$keyIndex] =  $newClean;
                }

                if(!empty($oneObject[$children])){
                    $object[$key][$children] = clean_by_key($oneObject[$children], $keyIndex);
                }
            }
        }

        return $object;
    }
    return $object;
}
function periodDate($startDate,$endDate,$day = true,$interval='1 day'){
    $begin = new \DateTime($startDate);
    $end = new \DateTime($endDate);
    if($day){
        $end = $end->modify('+1 day');
    }
    $interval = \DateInterval::createFromDateString($interval);
    $period = new \DatePeriod($begin, $interval, $end);
    return $period;
}

function _fixTextScanTranslations(){
    return __("Show on the map");
}


function is_admin(){
    if(!auth()->check()) return false;
    if(auth()->user()->hasPermission('dashboard_access')) return true;
    return false;
}
function is_vendor(){
    if(!auth()->check()) return false;
    if(auth()->user()->hasPermission('dashboard_vendor_access')) return true;
    return false;
    }

function get_link_detail_services($services, $id,$action='edit'){
    if( \Route::has($services.'.admin.'.$action) ){
        return route($services.'.admin.'.$action, ['id' => $id]);
    }else{
        return '#';
    }

}

function get_link_vendor_detail_services($services, $id,$action='edit'){
    if( \Route::has($services.'.vendor.'.$action) ){
        return route($services.'.vendor.'.$action, ['id' => $id]);
    }else{
        return '#';
    }

}

function format_interval($d1, $d2 = ''){
    $first_date = new DateTime($d1);
    if(!empty($d2)){
        $second_date = new DateTime($d2);
    }else{
        $second_date = new DateTime();
    }


    $interval = $first_date->diff($second_date);

    $result = "";
    if ($interval->y) { $result .= $interval->format("%y years "); }
    if ($interval->m) { $result .= $interval->format("%m months "); }
    if ($interval->d) { $result .= $interval->format("%d days "); }
    if ($interval->h) { $result .= $interval->format("%h hours "); }
    if ($interval->i) { $result .= $interval->format("%i minutes "); }
    if ($interval->s) { $result .= $interval->format("%s seconds "); }

    return $result;
}
function generate_timezone_list()
    {
        static $regions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        );

        $timezones = array();
        foreach( $regions as $region )
        {
            $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
        }

        $timezone_offsets = array();
        foreach( $timezones as $timezone )
        {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }

        // sort timezone by offset
        asort($timezone_offsets);

        $timezone_list = array();
        foreach( $timezone_offsets as $timezone => $offset )
        {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "$timezone (${pretty_offset})";
        }

        return $timezone_list;
    }

    function is_string_match($string,$wildcard){
        $pattern = preg_quote($wildcard,'/');
        $pattern = str_replace( '\*' , '.*', $pattern);
        return preg_match( '/^' . $pattern . '$/i' , $string );
    }
    function getNotify()
    {
        $checkNotify = \Modules\Core\Models\NotificationPush::query();
        if(is_admin()){
            $checkNotify->where(function($query){
                $query->where('for_admin',1);
                $query->orWhere('notifiable_id', Auth::id());
            });
        }else{
            $checkNotify->where('for_admin',0);
            $checkNotify->where('notifiable_id', Auth::id());
        }
        $notifications = $checkNotify->orderBy('created_at', 'desc')->limit(5)->get();
        $countUnread = $checkNotify->where('read_at', null)->count();
        return [$notifications,$countUnread];
    }

    function is_enable_registration(){
        return !setting_item('user_disable_register');
    }
    function is_enable_vendor_team(){
        return false;
        return setting_item('vendor_team_enable');
    }

    function is_enable_plan(){
        return setting_item('user_plans_enable') == true;
    }

    function get_main_lang(){
        return setting_item('site_locale');
    }

if (!function_exists('menu_listing')) {
    function menu_listing() {
        $data = [
            'hotel', //accomodation
            'space', //property
            'business',
            // 'boat', //vehicles
            // 'car', //vehicles
            // 'event',
            // 'natural',
            // 'cultural',
            // 'art',
        ];

        return $data;
    }
}

if (!function_exists('menu_listing_as')) {
    function menu_listing_as($str) {
        switch($str) {
            case 'hotel':
                $str = 'accomodation';
                break;
            case 'space':
                $str = 'property';
                break;
            case 'boat':
                $str = 'vehicle';
                break;
            case 'car':
                $str = 'car';
                break;
        }
        return $str;
    }
}

if (!function_exists('get_attribute_listing')) {
    function get_attribute_listing($key) {
        $data = [
            'old_key' => $key,
            'new_key' => $key,
        ];

        switch ($key) {
            case 'hotel':
                $data['new_key'] = 'accomodation';
                break;
            case 'space':
                $data['new_key'] = 'property';
                break;
            case 'boat':
                $data['new_key'] = 'vehicle';
                break;
            case 'car':
                $data['new_key'] = 'car';
                break;
        }

        return $data;
    }
}

if (!function_exists('menu_vendor')) {
    function menu_vendor() {
        $data = [
            'dashboard',
            'booking-history',
            'wishlist',
            'profile',
            'profile-setting',
            // 'password',
            'admin',
            'hotel',
            'space',
            'car',
            'event',
            // 'tour',
            // 'flight',
            // 'boat',
            // 'news',
            'verification',
            'my_plan',
            'booking_report',
            "enquiry",
            "payout",
            'virtuard360',
            'listing',
            'cultural',
            'art',
            'business',
            'natural',
            'chat',
            'referral',
        ];

        return $data;
    }
}

if (!function_exists('menu_admin')) {
    function menu_admin() {
        $data = [
            'admin',
            'menu',
            'general',
            'tools',
            'hotel',
            'space',
            'car',
            'event',
            // 'tour',
            // 'flight',
            // 'boat',
            'media',
            'news',
            'page',
            'users',
            'plan',
            'report',
            'payout',
            'coupon',
            'location',
            'review',
            'popup',
            'business',
            'natural',
            'cultural',
            'art',
            'theme',
            'virtuard360',
        ];

        return $data;
    }
}

if (!function_exists('getMenuVendorPlan')) {
    function getMenuVendorPlan()
    {
        $data = [
            // 'dashboard',
            'virtuard360',
            // 'hotel',
            // 'space',
            // 'business',
            // 'boat',
            // 'tour',
            // 'cultural',
            // 'art',
        ];

        return $data;
    }
}

if (!function_exists('checkMenuVendor')) {
    function checkMenuVendor($menu)
    {
        $status = false;
        if (isset($menu['id']) && in_array($menu['id'], getMenuVendorPlan())) $status = true;
        return $status;
    }
}

if (!function_exists('setMenuPosition')) {
    function setMenuPosition($menu)
    {
        $position = $menu['position'];

        switch ($menu['id']) {
            case 'natural':
                $position = 16;
                break;
            case 'cultural':
                $position = 17;
                break;
            case 'art':
                $position = 18;
                break;
            case 'hotel':
                $position = 11;
                break;
            case 'space':
                $position = 12;
                break;
            case 'business':
                $position = 13;
                break;
            case 'boat':
                $position = 14;
                break;
            case 'car':
                $position = 14;
                break;
            case 'event':
                $position = 15;
                break;
        }

        return $position;
    }
}

if (!function_exists('setMenuAdminPosition')) {
    function setMenuAdminPosition($model)
    {
        $position = 5;

        switch ($model) {
            case 'hotel':
                $position = 5;
                break;
            case 'space':
                $position = 6;
                break;
            case 'business':
                $position = 7;
                break;
            case 'boat':
                $position = 8;
                break;
            case 'car':
                $position = 8;
                break;
            case 'event':
                $position = 9;
                break;
            case 'natural':
                $position = 10;
                break;
            case 'cultural':
                $position = 11;
                break;
            case 'art':
                $position = 12;
                break;
        }

        return $position;
    }
}

if (!function_exists('getThumbPanorama')) {
    function getThumbPanorama($data) {
        $result = '/uploads/ipanoramaBuilder/upload/default.png';

        $json_data = json_decode($data->json_data);
        $filename = $json_data->config->scenes[0]->config->imageFront->url ?? '';
        if($filename != '') {
            $result = "/uploads/ipanoramaBuilder/upload/$data->user_id/$filename";
        }
        return $result;
    }
}

if (!function_exists('getUserPosts')) {
    function getUserPosts($id) {
        $data = UserPost::with('medias', 'ipanorama')->where('user_id', $id)->get();
        return $data;
    }
}

if (!function_exists('get_map_gmap_key')) {
    function get_map_gmap_key() {
        $data = Settings::select('val')->where('name', 'map_gmap_key')->first();
        return $data->val;
    }
}


if (!function_exists('get_attr_listing')) {
    function get_attr_listing($key) {
        $data  = [];

        switch ($key) {
            case 'business':
            case 'businesses' :
                $data['route_as'] = 'business';
                $data['svg'] = 'icon/shopping-bag.svg';
                break;
            case 'accomodation':
            case 'accomodations':
            case 'hotel':
                $data['route_as'] = 'hotel';
                $data['svg'] = 'icon/building.svg';
                break;
            case 'property':
            case 'properties':
            case 'space' :
                $data['route_as'] = 'space';
                $data['svg'] = 'icon/house-user.svg';
                break;
            case 'vehicle':
            case 'vehicles':
            case 'boat' :
                $data['route_as'] = 'boat';
                $data['svg'] = 'icon/directions-boat.svg';
                break;
            case 'car':
            case 'cars':
            case 'car' :
                $data['route_as'] = 'car';
                $data['svg'] = 'icon/car.svg';
                break;
            case 'event':
            case 'events':
                $data['route_as'] = 'event';
                $data['svg'] = 'icon/ticket.svg';
                break;
            case 'natural':
            case 'naturals':
                $data['route_as'] = 'natural';
                $data['svg'] = 'icon/mountain.svg';
                break;
            case 'cultural':
            case 'culturals' :
                $data['route_as'] = 'cultural';
                $data['svg'] = 'icon/church.svg';
                break;
            case 'art':
            case 'arts' :
                $data['route_as'] = 'art';
                $data['svg'] = 'icon/pencil-ruler.svg';
                break;
        }
        
        return $data;
    }
}

if (!function_exists('get_map_listing')) {
    function get_map_listing($key, $data) {
        $attr = get_attr_listing($key);

        $result = [
            'id' => $data->id,
            'category' => $key,
            'title' => $data->title,
            'map_lat' => $data->map_lat,
            'map_lng' => $data->map_lng,
            'address' => $data->address ?? '',
            'icon' => asset($attr['svg']),
            'image' => get_file_url($data->image_id),
            'banner_image_id' => get_file_url($data->image_id),
            'url' => route($attr['route_as'] . ".detail", $data->slug),
            'created_at' => $data->created_at,
            'author' => [
                'name' => $data->author->name,
                'image' => $data->author->getAvatarUrl(),
            ]
        ];

        return $result;
    }
}

if (!function_exists('get_listing_book')) {
    function get_listing_book() {
        $data = ['hotel', 'space', 'business', 'vehicle'];
        return $data;
    }
}

if (!function_exists('is_following')) {
    function is_following($following_id) {
        $data = FollowUser::where([
            'user_id' => auth()->user()->id,
            'follower_id' => $following_id
        ])->exists();

        return $data;
    }
}

if (!function_exists('hide_submenu_setting')) {
    function hide_submenu_setting() {
        $data = [
            'flight',
            // 'car',
            // 'event',
            'tour',
            'media',
        ];

        return $data;
    }
}

if (!function_exists('getMimeTypeFromExtension')) {
    function getMimeTypeFromExtension($ext) {
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'svg':
                return 'image';
            case 'mp4':
            case 'mkv':
                return 'video';
            default:
                return 'document';
        }
    }
}

if (!function_exists('get_explore_service')) {
    function get_explore_service() {
        $lists = menu_listing();

        $result  = [];
        foreach($lists as $list) {
            $data = [
                'id' => $list,
            ];

            switch ($list) {
                case 'hotel':
                    $data['title'] = __('Accomodation');
                    $data['icon'] = '<i class="fa fa-sm mr-2 fa-building"></i>';
                    break;
                case 'space':
                    $data['title'] = __('Property');
                    $data['icon'] = '<i class="fa fa-sm mr-2 fa-home"></i>';
                    break;
                case 'business':
                    $data['title'] = __('Business');
                    $data['icon'] = '<i class="fa fa-sm mr-2 fa-shopping-bag"></i>';
                    break;
                case 'boat':
                    $data['title'] = __('Vehicle');
                    $data['icon'] = '<i class="fa fa-sm mr-2 fa-ship"></i>';
                    break;
                case 'car':
                    $data['title'] = __('Car');
                    $data['icon'] = '<i class="fa fa-sm mr-2 fa-car"></i>';
                    break;
                case 'event':
                     $data['title'] = __('Event');
                     $data['icon'] = '<i class="icofont-ticket"></i>';
                    break;
                case 'natural':
                     $data['title'] = __('Natural');
                     $data['icon'] = '<i class="material-icons">landscape</i>';
                    break;
                case 'cultural':
                     $data['title'] = __('Cultural');
                     $data['icon'] = '<i class="material-icons">church</i>';
                    break;
                case 'art':
                     $data['title'] = __('Rendering');
                     $data['icon'] = '<i class="material-icons font-size-inherit">design_services</i>';
                    break;
            }

            $result[] = $data;
        }

        return $result;
    }
}

if (!function_exists('get_all_categories')) {
    function get_all_categories()
    {
        $attrs = Attributes::query()
            ->where([
                ['slug', 'like', '%type%']
            ])
            ->pluck('id')
            ->toArray();

        $terms = Terms::query()
            ->whereIn('attr_id', $attrs)
            ->groupBy('name')
            ->orderBy('name')
            ->get();

        return $terms;
    }
}

if (!function_exists('get_all_typologies')) {
    function get_all_typologies()
    {
        $attrs = Attributes::query()
            ->where([
                ['slug', 'like', '%typology%']
            ])
            ->pluck('id')
            ->toArray();

        $terms = Terms::query()
            ->whereIn('attr_id', $attrs)
            ->groupBy('name')
            ->orderBy('name')
            ->get();

        return $terms;
    }
}

if (!function_exists('is_plan_free')) {
    function is_plan_free($plan)
    {
        $result = true;

        if($plan->price != '0') $result = false;

        return $result;
    }
}


if (!function_exists('is_display_panorama_listing')) {
    function is_display_panorama_listing($data)
    {
        $result = false;

        if ($data->ipanorama && $data->ipanorama->status == 'publish' and $data->author->checkUserPlanStatus()) {
            $result = true;
        }

        return $result;
    }
}

if (!function_exists('get_file_ext')) {
    function get_file_ext($file)
    {
        $pathinfo = pathinfo($file);
        return $pathinfo['extension'];
    }
}

if (!function_exists('get_file_type')) {
    function get_file_type($file)
    {
        $ext = get_file_ext($file);

        $result = '';

        switch ($ext) {
            case 'mp4':
                $result = 'video'; 
                break;
            default:
                $result = 'image';
        }

        return $result;
    }
}

if (!function_exists('generate_user_name')) {
    function generate_user_name($first_name, $last_name = null)
    {
        $username = $first_name;

        if (!empty($last_name)) {
            $username = $first_name . ' ' . $last_name;
        }

        $username = Str::slug($username);
        $username = str_replace('-', '_', $username);

        $exist = User::where('user_name', $username)->exists();
        if($exist) {
            $username = $username . '_1';
        }

        return $username;
    }
}

if (!function_exists('find_user_by_username_or_id')) {
    function find_user_by_username_or_id($param)
    {
        $user = User::where('user_name', $param)->first();
        if(empty($user)){
            $user = User::find($param);
        }
        return $user;
    }
}

if (!function_exists('get_detail_url_referral')) {
    function get_detail_url_referral($url)
    {
        $isLoggedIn = auth()->check();
        $reference = auth()->user()->user_name ?? false;
        if (!$reference) {
            $reference = auth()->user()->id ?? false;
        }
        
        if ($isLoggedIn && $reference) {
            $url = $url."?reference=".$reference;
        }

        return $url;
    }
}

if (!function_exists('enable_referral_sell')) {
    function enable_referral_sell($row)
    {
        $result = false;

        if (setting_item('referral_enable') && $row->author->checkUserPlanStatus()) {
            $result = true;
        }

        return $result;
    }
}

if (!function_exists('api_currency_update')) {
    function api_currency_update()
    {
        try {
            $api_currency_last_update = setting_item('api_currency_last_update', Carbon::now()->startOfDay());
            $api_currency_exp = setting_item('api_currency_exp', 43200); // default 12 hours
            
            $timeDb = Carbon::parse($api_currency_last_update);
            $timeNow = Carbon::now();

            if ($timeDb->diffInSeconds($timeNow) >= $api_currency_exp) {
                $api_currency_key = setting_item('api_currency_key');
                if ($api_currency_key) {
                    $url = 'https://api.currencyapi.com/v3/latest?apikey=' . $api_currency_key;
                    $response = file_get_contents_curl($url);
                    $response = json_decode($response);

                    Storage::disk('local')->put('currency.json', json_encode($response));

                    if ($response->data) {
                        $new_extra_curreny = [];
                        $extra_curreny = setting_item_array('extra_currency');
                        foreach ($extra_curreny as $item) {
                            $code = strtoupper($item['currency_main']);
                            $cur = $response->data->$code;
                            if ($cur) {
                                $item['rate'] = (String) $cur->value;
                            }
                            $new_extra_curreny[] = $item;
                        }

                        setting_update_item('extra_currency', $new_extra_curreny);
                        setting_update_item('api_currency_last_update', $timeNow->toDateTimeString());
                    }
                }
            }
        } catch (\Exception $e){
            //
        }
    }
}

if (!function_exists('update_currency_from_file')) {
    function update_currency_from_file()
    {
        if (Storage::disk('local')->exists('currency.json')) {
            $fileContents = Storage::disk('local')->get('currency.json');
            $result = json_decode($fileContents, true);

            if ($result['data']) {
                $new_extra_curreny = [];
                $extra_curreny = setting_item_array('extra_currency');
                foreach ($extra_curreny as $item) {
                    $code = strtoupper($item['currency_main']);
                    $cur = $result['data'][$code];
                    if ($cur) {
                        $item['rate'] = (String) $cur['value'];
                    }
                    $new_extra_curreny[] = $item;
                }

                setting_update_item('extra_currency', $new_extra_curreny);
            }
        }
    }
}

if (!function_exists('get_software_lists')) {
    function get_software_lists()
    {
        $arts = Art::query()
            ->select('software')
            ->where('status', 'publish')
            ->get();
        
        $softwares = [];
        foreach($arts as $art) {
            $softwares = array_merge($softwares, $art->software ?? []);
        }
        $softwares = array_unique($softwares);

        return $softwares;
    }
}

if (!function_exists('resize_feature_image')) {
    function resize_feature_image($id)
    {
        if (config('app.env') == 'local') return $id;

        $media = MediaFile::find($id);
        if ($media) {
            $driver = $media->driver ?? 'uploads';
            $arrPath = explode('/', $media->file_path);
            array_pop($arrPath);

            $newPath = implode('/', $arrPath);

            $resizeName = $media->file_name . '-compress';
            $resizePath = $newPath .'/'. $resizeName . '.webp';
            $arrPath[] = $resizeName;

            if ($media->file_extension != 'webp') {
                $originalFile = Storage::disk($driver)->path($media->file_path);
                if ($originalFile) {
                    $img = Image::make($originalFile);
                    $img->save(Storage::disk($driver)->path($resizePath), 80);
    
                    $media->file_name = $resizeName;
                    $media->file_path = $resizePath;
                    $media->file_type = 'image/webp';
                    $media->file_extension = 'webp';
                    $media->save();
    
                    return $media->id;
                }
            }
        }

        return $id;
    }
}

if (!function_exists('view_panorama')) {
    function view_panorama($service, $row)
    {
        $data = [
            'row' => $row,
            'breadcrumbs' => [
                [
                    'name'  => __($service),
                    'url'  => route("$service.search"),
                ],
                [
                    'name'  => $row->title,
                    'url'  => route("$service.detail", $row->title),
                ],
                [
                    'name'  => $row->ipanorama->title,
                    'class'  => 'active',
                ],
            ],
        ];

        return view('app.panorama.show', $data);
    }
}

if (!function_exists('compress_view_panorama')) {
    function compress_view_panorama($panorama, $is_replace = false)
    {
        $code = json_decode($panorama['code']);
        $driver = 'uploads';
        $mainPath = 'ipanoramaBuilder';
        $width = 2000;

        foreach ($code->scenes as $key => $scen) {
            $filename = $scen->image;
            $pathinfo = pathinfo($filename);

            // if ($pathinfo['extension'] != 'webp') {
                $newName = $pathinfo['filename'].'.webp';
                $newDir = 'compress'.'/'.$panorama['user_id'];
                $newFilename = $newDir.'/'.$newName;
                $newPath = $mainPath.'/'.$newFilename;
                
                $mkdir = public_path($driver.'/'.$mainPath.'/'.$newDir);
                if (!File::isDirectory($mkdir)) {
                    File::makeDirectory($mkdir, 0777, true, true);
                }

                $exists = Storage::disk($driver)->exists($newPath);

                if (!$exists or $is_replace) {
                    $arrPath = explode('/', $filename);
                    if (!in_array($panorama['user_id'], $arrPath)) {
                        $position = count($arrPath) - 1;
                        array_splice($arrPath, $position, 0, $panorama['user_id']);
                        $filename = implode('/', $arrPath);
                    }

                    $pathStorage = $mainPath.'/'.$filename;
                    if(Storage::disk($driver)->exists($pathStorage)) {
                        $storage = Storage::disk($driver)->path($pathStorage);
                        $img = Image::make($storage);
                        if ($img->width() > $width) {
                            $img->resize($width, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                        $img->save(Storage::disk($driver)->path($newPath));
                    }
                }

                $code->scenes->$key->image = $newFilename;
            // }

            $code->scenes->$key->image = '/'.$driver.'/'.$mainPath.'/'.$code->scenes->$key->image;
        }
        $panorama['code'] = json_encode($code);

        return $panorama;
    }
}