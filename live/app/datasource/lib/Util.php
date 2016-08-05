<?php
namespace ITECH\Datasource\Lib;

class Util
{
    public static function ad_content($content,$paramd)
    {

        $content = explode("</p>", $content);
        $paragraphAfter = intval(count($content)/2);
        $new_content = '';
        for ($i = 0; $i < count($content); $i++) {
            if ($i == $paragraphAfter) {
                $new_content.= $paramd;
            }

            $new_content.= $content[$i] . "</p>";
        }
        return $new_content;
    }

    public static function curlGetPostJson($url, $post = array())
    {
        $url = trim($url);
        if (is_array($post) && count($post)) {
            $data = json_encode($post);
        } else {
            $data = $post;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    public static function usernameValidation($username)
    {
        $username = trim($username);

        if (preg_match('/^[a-z0-9_]+$/', $username)) {
            return true;
        } else {
            return false;
        }
    }
    // ads
    public static function itemAds($index, $premiumList) { 
        $index = $index - 1;
        if (isset($premiumList) && count($premiumList) >= $index) : ob_start(); ?>

            <a rel="nofollow" class="item-ads" href="<?php echo $premiumList[$index]['url'] ?>" target="_blank">
                <div class="thumbnail">
                    <img src="<?php echo $premiumList[$index]['default_thumbnail_url'] ?>" alt="<?php echo $premiumList[$index]['name'] ?>">
                </div>
                <div class="summary">
                    <h5 class="title"><?php echo $premiumList[$index]['name'] ?></h5>
                    <div class="price">
                        <?php
                            if ($premiumList[$index]['price'] > 0 ) {
                                echo number_format($premiumList[$index]['price']) . '<sup>VND</sup>';
                            } else {
                                echo 'Liên hệ ngay';
                            }
                        ?>
                        <span class="btn-buy-now">Xem ngay</span>
                    </div>
                    <div class="meta-post">
                        <?php if (isset($premiumList[$index]['description'])) : ?>
                            <?php echo Util::niceWordsByChars($premiumList[$index]['description'], 220); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <span class="author-by">Quảng cáo MuaBanNhanh</span>
            </a>

    <?php
        endif;
        return ob_get_clean();
    }
    // end ads

     public static function setNiceString($string = '')
    {
        $string = preg_replace('/[\t\s]+/', ' ', $string);
        $string = preg_replace('/\&nbsp\;/', ' ', $string);

        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = trim(strip_tags($string));
        $string = self::setSingleQuotes($string);
        $string = trim($string);

        return $string;
    }
    public static function setSingleQuotes($string = '')
    {
        $string = preg_replace('/"/', '\'', $string);

        $string = preg_replace('/“/', '\'', $string);
        $string = preg_replace('/”/', '\'', $string);

        $string = preg_replace('/\'\'/', '\'', $string);

        $string = preg_replace('/‘/', '\'', $string);
        $string = preg_replace('/’/', '\'', $string);

        $string = trim($string);

        return $string;
    }
    public static function setNiceWords($string = '', $limit = 45)
    {
        $string = self::setNiceString($string);
        $string = trim($string);

        $length = mb_strlen($string, 'UTF-8');
        $limit = abs(intval($limit));

        if ($limit < 1) {
            $limit = $length;
        }

        if ($limit < $length) {
            $string = mb_substr($string, 0, $limit, 'UTF-8');
            $stringarr = explode(' ', $string);
            unset($stringarr[count($stringarr) - 1]);

            $words = array();

            foreach ($stringarr as $word) {
                $word = trim($word);
                if ($word != '') {
                    $words[] = $word;
                }
            }

            $string = trim(implode(' ', $words)) . '';
        }

        return $string;
    }

    public static function dateValidation($date)
    {
        $date = trim($date);

        if (preg_match('/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/', $date)) {
            return true;
        } else {
            return false;
        }
    }

    public static function mobileValidation($mobile)
    {
        $mobile = trim($mobile);

        if (preg_match('/^[0-9]+$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    public static function numberOnly($string)
    {
        return trim(preg_replace('/[^0-9]/', '', $string));
    }

    public static function hashPassword($raw_password)
    {
        return md5(md5($raw_password));
    }

    public static function hashId($raw_id)
    {
        $raw_id = (int)$raw_id;

        $padded_id = sprintf('%08s', dechex($raw_id));
        $string_array = str_split($padded_id, 2);

        return implode(array_reverse($string_array));
    }

    public static function unhashId($hash_id)
    {
        $hash_id = trim($hash_id);

        $string_array = str_split($hash_id, 2);
        $padded_id = implode(array_reverse($string_array));

        return hexdec($padded_id);
    }

    public static function upperFirstLetter($string)
    {
        $string = trim(mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, mb_strlen($string, 'UTF-8'), 'UTF-8'));
        $string = trim(strip_tags($string));

        return $string;
    }

    public static function upperFirstLetters($string)
    {
        $string = preg_replace_callback('/([.!?])\s*(\w)/', function ($matches) {
            return mb_strtoupper($matches[1] . ' ' . $matches[2], 'UTF-8');
        }, mb_convert_case($string, MB_CASE_TITLE, 'UTF-8'));

        return $string;
    }

    public static function slug($string, $separator = '-')
    {
        $string = self::ascii($string);
        $string = trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $string));
        $string = trim(preg_replace('/[\s]+/', ' ', $string));
        $string = trim(preg_replace('/\s/', $separator, $string));

        return strtolower($string);
    }
    
    public static function formatMoney($numbers = '')
    {
        $numbers=intval($numbers);
        $string=number_format($numbers, -3, ',', '.');
            
        return $string;
    }

    public static function ascii($string)
    {
        $string = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $string);
        $string = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $string);
        $string = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $string);
        $string = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $string);
        $string = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $string);
        $string = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $string);
        $string = preg_replace('/(đ)/', 'd', $string);

        $string = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $string);
        $string = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $string);
        $string = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $string);
        $string = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $string);
        $string = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $string);
        $string = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $string);
        $string = preg_replace('/(Đ)/', 'D', $string);

        $string = trim($string);

        return $string;
    }

    public static function removeJunkSpace($string)
    {
        $words = array_filter(explode(' ', trim($string)));
        return trim(implode(' ', $words));
    }

    public static function punctuation($string)
    {
        $punctuation = '\;\:';
        $spaced_punctuation = array(
            ' ?',
            ' !',
            ' .',
            ' ,',
            ' ;',
            '( ',
            ' )'
        );
        $unspaced_punctuation = array(
            '?',
            '!',
            '.',
            ',',
            ';',
            '(',
            ')'
        );

        $string = preg_replace('/([\,\;\:])+/iS', '$1', $string);
        $string = preg_replace('/[[:space:]]+/', ' ', $string);
        $string = str_replace($spaced_punctuation, $unspaced_punctuation, $string);
        $string = preg_replace('/([' . $punctuation . '])[\s]*/', '\1 ', $string);
        $string = preg_replace('/(?<!\d),|,(?!\d{3})/', ', ', $string);
        $string = preg_replace('/(\.)([[:alpha:]]{2,})/', '$1 $2', $string);
        $string = trim($string);
        $string = preg_replace('/([\.!\?]\s+|\A)(\w)/e', '"$1" . strtoupper("$2")', $string);

        if ($string[strlen($string) - 1] == ',') {
            $string = substr($string, 0, -1) . '.';
        }

        $string = trim($string);

        return $string;
    }

    public static function niceWordsByChars($text, $max_char = 100, $end = '...')
    {
        $text = trim(strip_tags($text));
        $max_char = (int)$max_char;
        $end = trim($end);

        if ($text != '') {
            $text = self::removeJunkSpace($text);
        }

        $output = '';

        if (mb_strlen($text, 'UTF-8') > $max_char) {
            $words = explode(' ', $text);
            $i = 0;

            while (1) {
                $length = mb_strlen($output, 'UTF-8') + mb_strlen($words[$i], 'UTF-8');

                if ($length > $max_char) {
                    break;
                } else {
                    $output .= ' ' . $words[$i];
                    ++$i;
                }
            }

            $output .= $end;
        } else {
            $output = $text;
        }

        return trim($output);
    }

    public static function cutTextByChars($text, $max_char, $end = '...')
    {
        $text = trim($text);
        $max_char = (int)$max_char;
        $end = trim($end);

        $words_array = preg_split('//', $text, -1, PREG_SPLIT_NO_EMPTY);
        $string = '';

        if (count($words_array) <= $max_char) {
            return $text;
        }

        for ($i = 0; $i < $max_char; $i++) {
            if (isset($words_array[$i])) {
                $string .= $words_array[$i];
            }
        }

        if (count($words_array) <= $max_char) {
            return trim($string);
        } else {
            return trim($string) . $end;
        }
    }

    public static function currencyFormat($number)
    {
        return number_format($number, 0, '.', ',');
    }

    public static function randomPassword($count = 10)
    {
        $random = '';
        $base = explode(' ', '0 1 2 3 4 5 6 7 8 9 a b c d e f g h i j k l m n o p q r s t u v w x y z');

        for ($i = 0; $i < $count; $i++) {
            $random .= $base[mt_rand(0, 35)];
        }
        $random = trim($random);

        return $random;
    }

    public static function randomNumbers($count = 10)
    {
        $random = '';
        $base = explode(' ', '0 1 2 3 4 5 6 7 8 9');

        for ($i = 0; $i < $count; $i++) {
            $random .= $base[mt_rand(0, 9)];
        }
        $random = trim($random);

        return $random;
    }

    public static function token()
    {
        return md5(uniqid() . time());
    }

    public static function curlGet($url, $get = array(), $options = array())
    {
        $url = trim($url);
        if (!empty($get)) {
            $url .= '?' . http_build_query($get);
        }

        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function curlPost($url, $post = array(), $options = array())
    {
        $url = trim($url);
        if (!empty($post)) {
            $data = http_build_query($post);
        }
        

        $defaults = array(
            CURLOPT_POST => true,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    public static function curlPostJson($url, $post = array(), $options = array())
    {
        $url = trim($url);
        if (!empty($post)) {
            $data = json_encode($post);
        }
        

        $defaults = array(
            CURLOPT_POST => true,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HEADER => 'application/json',
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    /**
     * @author Vu.Tran
     */
    public static function getRealIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
          $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
          $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    
    /**
     * @author Vu.Tran
     */
    public static function strClearMark($str) 
    {
        $str = strip_tags(Util::htmlDecode($str, true)); 
        $str = preg_replace(array('#\s[\s]+#ui', '#[\t\n]#'), ' ', $str);
        
        return trim(strtolower(Util::strToLatin($str)));    
    }
    
    /**
     * @author Vu.Tran
     */
    public static function htmlEncode($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');    
    }
    
    /**
     * @author Vu.Tran
     */
    public static function htmlDecode($str, $entities = false)
    {
        $str = htmlspecialchars_decode($str);    

        if ($entities)
        {
            return html_entity_decode($str, HTML_ENTITIES);
        }
        return $str; 
    }

    public static function strToLatin($str)
    {    
        $chars = array( 'a' => array('à','ả','ã','á','ạ','ă','ằ','ẳ','ẵ','ắ','ặ','â','ầ','ẩ','ẫ','ấ','ậ', 'ä','å','ā','ą','ǎ','ǻ'),
                        'A' => array('À','Ả','Ã','Á','Ạ','Ă','Ằ','Ẳ','Ẵ','Ắ','Ặ','Â','Ầ','Ẩ','Ẫ','Ấ','Ậ', 'Ä','Å','Ā','Ą','Ǎ','Ǻ'),
                        'AE'=> array('Æ','Ǽ'),
                        'ae'=> array('æ','ǽ'),
                        'C' => array('Ç','Ć','Ĉ','Ċ','Č'),
                        'c' => array('ç','ć','ĉ','ċ','č'),
                        'd' => array('đ','ď'),
                        'D' => array('Đ','Ď'),
                        'e' => array('è','ẻ','ẽ','é','ẹ','ê','ề','ể','ễ','ế','ệ', 'ë','ē','ĕ','ė','ę','ě'),
                        'E' => array('È','Ẻ','Ẽ','É','Ẹ','Ê','Ề','Ể','Ễ','Ế','Ệ', 'Ë','Ē','Ĕ','Ė','Ę','Ě'),
                        'f' => array('ƒ'),
                        'g' => array('ĝ','ğ','ġ','ģ'),
                        'G' => array('Ĝ','Ğ','Ġ','Ģ'),                        
                        'H' => array('Ĥ', 'Ħ'),                        
                        'h' => array('ĥ', 'ħ'),                        
                        'i' => array('ì','ỉ','ĩ','í','ị', 'î','ï','ī','ĭ','į','ı','ǐ'),
                        'I' => array('Ì','Ỉ','Ĩ','Í','Ị', 'Î','Ï','Ī','Ĭ','Į','İ','Ǐ'),
                        'IJ'=> array('Ĳ'),
                        'ij'=> array('ĳ'),
                        'J' => array('Ĵ'),
                        'j' => array('ĵ'),
                        'K' => array('Ķ'),
                        'k' => array('ķ'),
                        'L' => array('Ĺ','Ļ','Ľ','Ŀ','Ł'),
                        'l' => array('ĺ','ļ','ľ','ŀ','ł'),
                        'N' => array('Ñ','Ń','Ņ','Ň'),
                        'n' => array('ñ','ń','ņ','ň','ŉ'),
                        'o' => array('ò','ỏ','õ','ó','ọ','ô','ồ','ổ','ỗ','ố','ộ','ơ','ờ','ở','ỡ','ớ','ợ', 'ö','ō','ŏ','ő','ǒ','ø','ǿ'),
                        'O' => array('Ò','Ỏ','Õ','Ó','Ọ','Ô','Ồ','Ổ','Ỗ','Ố','Ộ','Ơ','Ờ','Ở','Ỡ','Ớ','Ợ', 'Ö','Ō','Ŏ','Ő','Ǒ','Ø','Ǿ'),
                        'OE'=> array('Œ'),
                        'oe'=> array('œ'),
                        'R' => array('Ŕ','Ŗ','Ř'),
                        'r' => array('ŕ','ŗ','ř'),
                        'S' => array('Ś','Ŝ','Ş','Š'),
                        's' => array('ś','ŝ','ş','š','ſ','ß'),
                        'T' => array('Ţ','Ť','Ŧ'),
                        't' => array('ţ','ť','ŧ'),
                        'u' => array('ù','ủ','ũ','ú','ụ','ư','ừ','ử','ữ','ứ','ự', 'û','ü','ū','ŭ','ů','ű','ų','ǔ','ǖ','ǘ','ǚ','ǜ'),
                        'U' => array('Ù','Ủ','Ũ','Ú','Ụ','Ư','Ừ','Ử','Ữ','Ứ','Ự', 'Û','Ü','Ū','Ŭ','Ů','Ű','Ų','Ǔ','Ǖ','Ǘ','Ǚ','Ǜ'),
                        'W' => array('Ŵ'),
                        'w' => array('ŵ'),
                        'y' => array('ỳ','ỷ','ỹ','ý','ỵ', 'ÿ','ŷ'),
                        'Y' => array('Ỳ','Ỷ','Ỹ','Ý','Ỵ', 'Ŷ','Ÿ'),
                        'Z' => array('Ź','Ż','Ž'),
                        'z' => array('ź','ż','ž')
                        );

        foreach($chars as $k => $v)
        {
            $str = str_replace($v, $k, $str);     
        }
        return $str;
    }
    
    /**
     * @author Cuong.Bui
     */
    public static function rotateHotline($hotline)
    {
        $week = date('W');
        $persons = count($hotline);

        if ($week % $persons == 0) {
            $index = $persons;
        } else {
            $index = $week % $persons;
        }

        $line = array();
        $line[1] = $hotline[$index];

        if ($index < count($hotline)) {
            for ($i = $index + 1; $i <= count($hotline); $i++) {
                $line[] = $hotline[$i];
            }
        }

        for ($i = 1; $i <= $index - 1; $i++) {
            $line[] = $hotline[$i];
        }

        return $line;
    }
    
    /**
     * @author Vu.Tran
     */
    public static function fileArray($dir)
    {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {
                if (!is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }
    
    public static function filterHtmlTag($string) {
        $string = trim($string);

        $matches = array();
        preg_match_all('~<a.*>~isU', $string, $matches);

        if (count($matches) && isset($matches[0])) {
            for ($i = 0; $i <= sizeof($matches[0]); $i++) {
                if (isset($matches[0][$i]) && !preg_match('~nofollow~is', $matches[0][$i])) {
                    $result = trim($matches[0][$i], '>');
                    $result .= ' rel="nofollow">';
                    $string = str_replace($matches[0][$i], $result, $string);
                }
            }
        }

        return $string;
    }
    
}