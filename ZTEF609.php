<?php


/**
 * ZteF609
 *
 * CATATAN FENTING DEK:
 * - Library ini dibuat tidak untuk kejahatan ataupun kegiatan yang merugikan orang lain,
 *   apalagi untuk usil ke teman atau sanak saudara, itu dosa dek. Mending langsung gelut aja.
 * 
 * @author walangkaji (https://github.com/walangkaji)
 */
class ZTEF609
{

    function __construct($ipModem, $username, $password, $debug = false, $proxy = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->proxy    = $proxy;

        if ($debug) {
            $this->debug = true;
        }

        $this->modemUrl = "http://$ipModem";
    }

    /**
     * Fungsi untuk login
     * 
     * @return boolean
     */
    public function login()
    {
        $cekLogin = $this->cekLogin();

        if ($cekLogin) {
            $this->debug(__FUNCTION__, 'Login session masih aktif.');

            return true;
        }

        $get  = $this->request($this->modemUrl);
        $rand = rand(10000000, 99999999);

        $postdata = [
            'action'         => 'login',
            'Username'       => $this->username,
            'Password'       => hash('sha256', $this->password . $rand),
            'Frm_Logintoken' => $this->GetBetween($get, '"Frm_Logintoken", "', '"),'),
            'UserRandomNum'  => $rand,
        ];

        $options = [
            'method'   => 'post',
            'postdata' => $postdata,
        ];

        $this->request($this->modemUrl, $options);
        $cekLogin = $this->cekLogin();

        if ($cekLogin) {
            $this->debug(__FUNCTION__, 'Berhasil login dengan user ' . $this->username);
        }else{
            $this->debug(__FUNCTION__, 'Gagal login dengan user ' . $this->username);
        }

        return $cekLogin;
    }

    /**
     * Fungsi untuk reboot modem
     * 
     * @return boolean
     */
    public function reboot()
    {
        $url = $this->modemUrl . '/getpage.gch?pid=1002&nextpage=manager_dev_conf_t.gch';
        $get = $this->request($url);

        $postdata = [
            'IF_ACTION'      => 'devrestart',
            'IF_ERRORSTR'    => 'SUCC',
            'IF_ERRORPARAM'  => 'SUCC',
            'IF_ERRORTYPE'   => -1,
            'flag'           => 1,
            '_SESSION_TOKEN' => $this->GetBetween($get, 'session_token = "', '";'),
        ];

        $options = [
            'method'   => 'post',
            'postdata' => $postdata,
        ];

        $request = $this->request($url, $options);
        $cek     = $this->GetBetween($request, "Transfer_meaning('flag','", "');");

        if ($cek == 1) {
            $this->debug(__FUNCTION__, 'Berhasil Reboot modem.');

            return true;
        }

        $this->debug(__FUNCTION__, 'Gagal Reboot modem.');

        return false;
    }

    /**
     * Fungsi untuk cek login
     * 
     * @return boolean
     */
    private function cekLogin()
    {
        $url      = $this->modemUrl . '/template.gch';
        $response = $this->request($url);

        if ($this->httpCode != 200) {
            return false;
        }

        return true;
    }

    /**
     * Untuk cari string diantara string
     * 
     * @param string $content contentnya
     * @param string $start   awalan
     * @param string $end     akhiran
     */
    private function GetBetween($content, $start, $end)
    {
        $r = explode($start, $content);
        if (isset($r[1])){
            $r = explode($end, $r[1]);
            return $r[0];
        }

        return '';
    }

    /**
     * Untuk debug proses
     */
    private function debug($function, $text = '')
    {
        $space = 10 - strlen($function);
        $space = ($space < 0) ? 0 : $space;

        if ($this->debug) {
            echo "[" . date('h:i:s A') . "]: $function" . str_repeat(' ', $space);
            echo (empty($text) ? '' : ': ' . $text) . PHP_EOL;
            // echo $text . PHP_EOL;
        }
    }

    /**
     * Curl request
     * 
     * @param  string $url     url request
     * @param  array  $options options yang akan digunakan
     *                         array  header    untuk setting headernya
     *                         string useragent untuk set useragent
     *                         string method    'post', 'put', 'delete'
     */
    public function request($url, $options = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt'); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0');

        if ($this->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }

        if (!empty($options)) {
            if (isset($options['header'])) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
            }
            elseif (isset($options['useragent'])) {
                curl_setopt($ch, CURLOPT_USERAGENT, $options['useragent']);
            }
            elseif (isset($options['method'])) {

                if (strtolower($options['method']) == 'post') {
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['postdata']));
                }
                elseif (strtolower($options['method']) == 'delete') {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                }
                elseif (strtolower($options['method']) == 'put') {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['postdata']));
                }

            }
        }

        curl_setopt($ch, CURLOPT_HEADER, 0);           
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $html = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $html;
    }
}
