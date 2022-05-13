<?

/** 
 * Класс рекапчи для bitrix
 * 
 * Не забыть указать секретный и публичный ключи
 * 
 * В случае ошибки стоит проверить CURL
 * 
 * */
class recaptcha
{
    private static $recaptcha_secret = '123456789';
    public static $recaptcha_public = '123456789';
    public static $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

    /** Использовал для быстрой проверки CURL */
    private static function url_get_contents($Url)
    {
        if (!function_exists('curl_init')) {
            /*logs::add('CURL is not installed');*/
            echo 'CURL is not installed!';
            die('CURL is not installed!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function valid($response)
    {
        $session = \Bitrix\Main\Application::getInstance()->getSession();

        if (!$session->has('recaptcha')) {
            $recaptcha_response = $response;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$recaptcha_url . '?secret=' . self::$recaptcha_secret . '&response=' . $recaptcha_response);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $recaptcha = json_decode(curl_exec($ch));
            if ($recaptcha->score >= 0.5) {
                $session->set('recaptcha', 'Y');
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
?>