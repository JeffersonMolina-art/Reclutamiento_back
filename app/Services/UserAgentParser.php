<?php

namespace App\Services;

/**
 * Detecta browser, OS y tipo de dispositivo a partir de un User-Agent string.
 * Replica el comportamiento de mobiledetect/mobiledetectlib + jenssegers/agent.
 */
class UserAgentParser
{
    private string $ua;

    // Patrones de teléfonos — mismos que MobileDetect::$phoneDevices
    private static array $phoneDevices = [
        'iPhone' => '\biPhone\b|\biPod\b',
        'BlackBerry' => 'BlackBerry|\bBB10\b|rim[0-9]+|\b(BBA100|BBB100|BBD100|BBE100|BBF100|STH100)\b-[0-9]+',
        'Pixel' => '; \bPixel\b',
        'HTC' => 'HTC|HTC.*(Sensation|Evo|Vision|Explorer|6800|8100|8900|A7272|S510e|C110e|Legend|Desire|T8282)|APX515CKT|Qtek9090|APA9292KT|HD_mini|Sensation.*Z710e|PG86100|Z715e|Desire.*(A8181|HD)|ADR6200|ADR6400L|ADR6425|001HT|Inspire 4G|Android.*\bEVO\b|T-Mobile G1|Z520m|Android [0-9.]+; Pixel',
        'Nexus' => 'Nexus One|Nexus S|Galaxy.*Nexus|Android.*Nexus.*Mobile|Nexus 4|Nexus 5|Nexus 5X|Nexus 6',
        'Dell' => 'Dell[;]? (Streak|Aero|Venue|Venue Pro|Flash|Smoke|Mini 3iX)|XCD28|XCD35|\b001DL\b|\b101DL\b|\bGS01\b',
        'Motorola' => 'Motorola|DROIDX|DROID BIONIC|\bDroid\b.*Build|Android.*Xoom|HRI39|MOT-|A1260|A1680|A555|A853|A855|A953|A955|A956|Motorola.*ELECTRIFY|Motorola.*i1|i867|i940|MB200|MB300|MB501|MB508|MB511|MB520|MB525|MB526|MB611|MB612|MB632|MB810|MB855|MB860|MB861|MB865|MB870|ME501|ME502|ME511|ME525|ME600|ME632|ME722|ME811|ME860|ME863|ME865|MT620|MT710|MT716|MT720|MT810|MT870|MT917|Motorola.*TITANIUM|WX435|WX445|XT300|XT301|XT311|XT316|XT317|XT319|XT320|XT390|XT502|XT530|XT531|XT532|XT535|XT603|XT610|XT611|XT615|XT681|XT701|XT702|XT711|XT720|XT800|XT806|XT860|XT862|XT875|XT882|XT883|XT894|XT901|XT907|XT909|XT910|XT912|XT928|XT926|XT915|XT919|XT925|XT1021|\bMoto E\b|XT1068|XT1092|XT1052',
        'Samsung' => '\bSamsung\b|SM-G950F|SM-G955F|SM-G9250|GT-19300|SGH-I337|BGT-S5230|GT-B2100|GT-B2700|GT-B2710|GT-B3210|GT-B3310|SM-F946B|SM-A127F|SM-S908E|SM-G955N|SM-S918U1|SM-G998B|SM-G970N|SM-G973U|SM-S901U|SM-A515F|SM-S901E|SM-G980F|SM-S901B',
        'LG' => '\bLG\b;|LG[- ]?(C800|C900|E400|E610|E900|E-900|F160|F180K|F180L|F180S|730|855|L160|LS740|LS840|LS970|LU6200|MS690|MS695|MS770|MS840|MS870|MS910|P500|P700|P705|VM696|AS680|AS695|AX840|C729|E970|GS505|272|C395|E739BK|E960|L55C|L75C|LS696|LS860|P769BK|P350|P509|P870|UN272|US730|VS840|VS950|LN272|LN510|LS670|LS855|LW690|MN270|MN510|P930|UN200|UN270|UN510|UN610|US670|US740|US760|UX265|UX840|VN271|VN530|VS660|VS700|VS740|VS750|VS910|VS920|VS930|VX9200|VX11000|AX840A|LW770|P506|P925|P999|E612|D955|D802|MS323|M257)|LM-G710',
        'Sony' => 'SonyST|SonyLT|SonyEricsson|SonyEricssonLT15iv|LT18i|E10i|LT28h|LT26w|SonyEricssonMT27i|C5303|C6902|C6903|C6906|C6943|D2533|SOV34|601SO|F8332',
        'Asus' => 'Asus.*Galaxy|PadFone.*Mobile|ASUS_Z01QD|ASUS_X00TD',
        'Xiaomi' => '^(?!.*\bx11\b).*xiaomi.*$|POCOPHONE F1|\bMI\b 8|\bMi\b 10|Redmi Note 9S|Redmi 5A|Redmi Note 5A Prime|Redmi Note 7 Pro|N2G47H|M2001J2G|M2001J2I|M1805E10A|M2004J11G|M1902F1G|M2002J9G|M2004J19G|M2003J6A1G|M2012K11C|M2007J1SC',
        'NokiaLumia' => 'Lumia [0-9]{3,4}',
        'Micromax' => 'Micromax.*\b(A210|A92|A88|A72|A111|A110Q|A115|A116|A110|A90S|A26|A51|A35|A54|A25|A27|A89|A68|A65|A57|A90)\b',
        'Palm' => 'PalmSource|Palm',
        'Vertu' => 'Vertu|Vertu.*Ltd|Vertu.*Ascent|Vertu.*Ayxta|Vertu.*Constellation(F|Quest)?|Vertu.*Monika|Vertu.*Signature',
        'Pantech' => 'PANTECH|IM-A850S|IM-A840S|IM-A830L|IM-A830K|IM-A830S|IM-A820L|IM-A810K|IM-A810S|IM-A800S|IM-T100K|IM-A725L|IM-A780L|IM-A775C|IM-A770K|IM-A760S|IM-A750K|IM-A740S|IM-A730S|IM-A720L|IM-A710K|IM-A690L|IM-A690S|IM-A650S|IM-A630K|IM-A600S|VEGA PTL21|PT003|P8010|ADR910L|P6030|P6020|P9070|P4100|P9060|P5000|CDM8992|TXT8045|ADR8995|IS11PT|P2030|P6010|P8000|PT002|IS06|CDM8999|P9050|PT001|TXT8040|P2020|P9020|P2000|P7040|P7000|C790',
        'Fly' => 'IQ230|IQ444|IQ450|IQ440|IQ442|IQ441|IQ245|IQ256|IQ236|IQ255|IQ235|IQ275|IQ240|IQ285|IQ280|IQ270|IQ260|IQ250',
        'Wiko' => 'KITE 4G|HIGHWAY|GETAWAY|STAIRWAY|DARKSIDE|DARKFULL|DARKNIGHT|DARKMOON|SLIDE|WAX 4G|RAINBOW|BLOOM|SUNSET|GOA(?!nna)|LENNY|BARRY|IGGY|OZZY|CINK FIVE|CINK PEAX|CINK PEAX 2|CINK SLIM|CINK SLIM 2|CINK \+|CINK KING|CINK PEAX|CINK SLIM|SUBLIM',
        'iMobile' => 'i-mobile (IQ|i-STYLE|idea|ZAA|Hitz)',
        'SimValley' => '\b(SP-80|XT-930|SX-340|XT-930|SX-310|SP-360|SP60|SPT-800|SP-120|SPT-800|SP-140|SPX-5|SPX-8|SP-100|SPX-8|SPX-12)\b',
        'Wolfgang' => 'AT-B24D|AT-AS50HD|AT-AS40W|AT-AS55HD|AT-AS45q2|AT-B26D|AT-AS50Q',
        'Alcatel' => 'Alcatel',
        'Nintendo' => 'Nintendo (3DS|Switch)',
        'Amoi' => 'Amoi',
        'INQ' => 'INQ',
        'OnePlus' => 'ONEPLUS|CPH2663',
        'Huawei' => 'HMSCore|Huawei',
        'WindowsPhone' => 'IEMobile|Windows Phone',
        'GenericPhone' => 'UCWEB.*Adr|Dalvik/.*\bAndroid\b|Tapatalk|PDA;|SAGEM|\bmmp\b|pocket|\bpsp\b|symbian|Smartphone|smartfon|treo|up\.browser|up\.link|vodafone|\bwap\b|nokia|Series40|Series60|S60|SonyEricsson|N900|MAUI.*WAP.*Browser|Android.*Mobile|Mobile.*Android',
    ];

    // Patrones de tablets — mismos que MobileDetect::$tabletDevices
    private static array $tabletDevices = [
        'iPad' => 'iPad|iPad.*Mobile',
        'NexusTablet' => 'Android.*Nexus[\s]+(7|9|10)',
        'GoogleTablet' => 'Android.*Pixel C',
        'SamsungTablet' => 'SM-X926B|SM-X706B|SM-X700|SM-X620|SM-X526B|SM-X520|SM-X626B|SM-X920|SM-X820|SM-X826B|SM-P625|SM-P620|SM-X306B|SM-T730|SM-T976B|SM-T875|SM-T575|SM-T545|SM-X210R|SM-X216R|SM-X356B|SM-T860X|SM-T636B|SM-T509|SM-T503|SM-T720X|SM-T570|SM-T540|SM-T510X|SM-T830X|SM-T820X|SM-T710X|SM-T810X|SM-T365|SM-T550X|SM-T116|SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|GT-P1000',
        'Kindle' => 'Kindle|Silk.*Accelerated|Android.*\b(KFOT|KFTT|KFJWI|KFJWA|KFOTE|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|WFJWAE|KFSAWA|KFSAWI|KFASWI|KFARWI|KFFOWI|KFGIWI|KFMEWI)\b|Android.*Silk/[0-9.]+ like Chrome/[0-9.]+ (?!Mobile)',
        'SurfaceTablet' => 'Windows NT [0-9.]+; ARM;.*(Tablet|ARMBJS)',
        'HPTablet' => 'HP Slate (7|8|10)|HP ElitePad 900|hp-tablet|EliteBook.*Touch|HP 8|Slate 21|HP SlateBook 10',
        'AsusTablet' => 'ME181C|P01Y|PO1MA|P01Z|\bP027\b|\bP024\b|\bP00C\b|\bK00C\b|\bK00E\b|\bK00L\b|TX201LA|ME176C|ME102A|\bM80TA\b|ME372CL|ME560CG|ME372CG|ME302KL|ME572C|ME103K|ME170C|ME171C|\bME70C\b|ME581C|ME581CL|ME8510C|^.*PadFone((?!Mobile).)*$|Transformer|TF101|TF101G|TF300T|TF300TG|TF300TL|TF700T|TF700KL|TF701T|TF810C|ME171|ME301T|ME302C|ME371MG|ME370T|ME372MG|ME172V|ME173X|ME400C|Slider SL101|\bK00F\b',
        'BlackBerryTablet' => 'PlayBook|RIM Tablet',
        'HTCtablet' => 'HTC_Flyer_P512|HTC Flyer|HTC Jetstream|HTC-P715a|HTC EVO View 4G|PG41200|PG09410',
        'MotorolaTablet' => 'xoom|sholest|MZ615|MZ605|MZ505|MZ601|MZ602|MZ603|MZ604|MZ606|MZ607|MZ608|MZ609|MZ615|MZ616|MZ617',
        'NookTablet' => 'Android.*Nook|NookColor|nook browser|BNRV200|BNRV200A|BNTV250|BNTV250A|BNTV400|BNTV600|LogicPD Zoom2',
        'AcerTablet' => 'Android.*; \b(A100|A101|A110|A200|A210|A211|A500|A501|A510|A511|A700|A701|W500|W500P|W501|W501P|W510|W511|W700|G100|G100W|B1-A71|B1-710|B1-711|A1-810|A1-811|A1-830)\b|W3-810|\bA3-A10\b|\bA3-A11\b|\bA3-A20\b|\bA3-A30|A3-A40',
        'ToshibaTablet' => 'Android.*(AT100|AT105|AT200|AT205|AT270|AT275|AT300|AT305|AT1S5|AT500|AT570|AT700|AT830)|TOSHIBA.*FOLIO',
        'LGTablet' => '\bL-06C|LG-V909|LG-V900|LG-V700|LG-V510|LG-V500|LG-V410|LG-V400|LG-VK810\b',
        'FujitsuTablet' => 'Android.*\b(F-01D|F-02F|F-05E|F-10D|M532|Q572)\b',
        'LenovoTablet' => 'TB-X704L|TB-J606F|TB-X606F|TB-X306X|YT-J706X|TB128FU|YT3-X50M|YT-X705F|YT-X703F|YT-X703L|YT-X705L|YT-X705X|TB2-X30F|TB2-X30L|TB2-X30M|A2107A-F|A2107A-H|TB3-730F|TB3-730M|TB3-730X|Lenovo TB|Lenovo TAB|Idea(Tab|Pad)|ThinkPad([ ]+)?Tablet',
        'DellTablet' => 'Venue 11|Venue 8|Venue 7|Dell Streak 10|Dell Streak 7',
        'XiaomiTablet' => '21051182G',
        'HuaweiTablet' => 'MediaPad|MediaPad 7 Youth|IDEOS S7|S7-201c|S7-202u|S7-101|S7-103|S7-104|S7-105|S7-106|S7-201|S7-Slim|M2-A01L|BAH-L09|BAH-W09|AGS-L09|CMR-AL19|KOB2-L09|BG2-U01|BG2-W09|BG2-U03|AGS-W09',
        'NecTablet' => '\bN-06D|\bN-08D',
        'SonyTablet' => 'EBRD1101|EBRD1102|EBRD1201|SGP351|SGP341|SGP511|SGP512|SGP521|SGP541|SGP551|SGP621|SGP641|SGP612|SOT31|SGP771|SGP611|SGP612|SGP712|Sony.*Tablet|Xperia Tablet|Sony Tablet S|SO-03E|SGPT12|SGPT13|SGPT114|SGPT121|SGPT122|SGPT123|SGPT111|SGPT112|SGPT113|SGPT131|SGPT132|SGPT133|SGPT211|SGPT212|SGPT213|SGP311|SGP312|SGP321',
        'GenericTablet' => 'Android.*\b97D\b|Tablet(?!.*PC)|BNTV250A|MID-WCDMA|LogicPD Zoom2|\bA7EB\b|CatNova8|A1_07|CT704|CT1002|\bM721\b|rk30sdk|\bEVOTAB\b|M758A|ET904|ALUMIUM10|Smartfren Tab|Endeavour 1010|Tablet-PC-4|Tagi Tab|\bM6pro\b|CT1020W|arc 10HD|\bTP750\b|\bQTAQZ3\b|WVT101|TM1088|KT107',
    ];

    // Browsers — orden de precedencia: más específicos primero para evitar falsos positivos
    private static array $browsers = [
        'Opera Mini' => ['detect' => 'Opera Mini', 'version' => 'Opera Mini/([^;\s]+)'],
        'Opera' => ['detect' => 'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+$|Coast/[0-9.]+|Opera|OPR', 'version' => '(?:OPR|Opera)[/ ]([^\s]+)'],
        'Edge' => ['detect' => 'EdgiOS|EdgA|Mobile Safari/[.0-9]* Edge|Edge|Edg/', 'version' => 'Edg[a-z]*/([^\s]+)'],
        'UCBrowser' => ['detect' => 'UC.*Browser|UCWEB', 'version' => '(?:UCBrowser|UCWEB)[/ ]([^\s]+)'],
        'Vivaldi' => ['detect' => 'Vivaldi', 'version' => 'Vivaldi/([^\s]+)'],
        'Samsung Internet' => ['detect' => 'SamsungBrowser', 'version' => 'SamsungBrowser/([^\s]+)'],
        'WeChat' => ['detect' => '\bMicroMessenger\b', 'version' => 'MicroMessenger/([^\s]+)'],
        'Chrome' => ['detect' => '\bCrMo\b|CriOS.*Mobile|Android.*Chrome/[.0-9]* Mobile|Chrome', 'version' => '(?:Chrome|CriOS|CrMo)/([^\s]+)'],
        'Firefox' => ['detect' => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS.*Mobile|Firefox', 'version' => '(?:Firefox|FxiOS)/([^\s]+)'],
        'Safari' => ['detect' => 'Version((?!\bEdgiOS\b).)*Mobile.*Safari|Safari.*Mobile|MobileSafari|Safari', 'version' => 'Version/([^\s]+)'],
        'IE' => ['detect' => 'IEMobile|MSIEMobile|MSIE|Trident/[.0-9]+', 'version' => '(?:IEMobile[/ ]|MSIE )([^\s;]+)|Trident.*rv:([0-9.]+)'],
        'Netscape' => ['detect' => 'Netscape', 'version' => 'Netscape/([^\s]+)'],
    ];

    // OS — orden crítico: iOS y Android antes de macOS/Linux para evitar falsos positivos
    private static array $osList = [
        'Windows Phone' => 'Windows Phone [0-9.]+|Windows Phone OS',
        'Windows' => 'Windows NT',
        'Android' => 'Android',
        'iOS' => 'iPhone|iPad|iPod|AppleCoreMedia|\biPhone.*Mobile|\biPod',
        'ChromeOS' => 'CrOS',
        'macOS' => 'Mac OS X',
        'BlackBerryOS' => 'blackberry|\bBB10\b|rim tablet os',
        'Symbian' => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
        'webOS' => 'webOS|hpwOS',
        'HarmonyOS' => 'HarmonyOS',
        'Ubuntu' => 'Ubuntu',
        'Debian' => 'Debian',
        'Linux' => 'Linux',
    ];

    public function __construct(string $userAgent)
    {
        $this->ua = $userAgent;
    }

    public function isMobile(): bool
    {
        return $this->matchesAny(self::$phoneDevices);
    }

    public function isTablet(): bool
    {
        return $this->matchesAny(self::$tabletDevices);
    }

    public function isDesktop(): bool
    {
        return !$this->isMobile() && !$this->isTablet();
    }

    public function deviceType(): string
    {
        if ($this->isTablet()) return 'tablet';
        if ($this->isMobile()) return 'phone';
        return 'desktop';
    }

    public function browser(): ?string
    {
        foreach (self::$browsers as $name => $patterns) {
            if (preg_match('#' . $patterns['detect'] . '#i', $this->ua)) {
                return $name;
            }
        }
        return null;
    }

    public function browserVersion(): ?string
    {
        $browser = $this->browser();
        if (!$browser || !isset(self::$browsers[$browser])) {
            return null;
        }
        if (preg_match('#' . self::$browsers[$browser]['version'] . '#i', $this->ua, $match)) {
            // Devuelve el primer grupo capturado no vacío (soporta patrones con alternativas)
            return $match[1] !== '' ? $match[1] : ($match[2] ?? null);
        }
        return null;
    }

    public function os(): ?string
    {
        foreach (self::$osList as $name => $pattern) {
            if (preg_match('#' . $pattern . '#i', $this->ua)) {
                return $name;
            }
        }
        return null;
    }

    private function matchesAny(array $devices): bool
    {
        foreach ($devices as $pattern) {
            if (preg_match('#' . $pattern . '#i', $this->ua)) {
                return true;
            }
        }
        return false;
    }
}
