<?php


namespace AleeDhillon\MetaFive\Lib;


class CMT5Request
{
    private $m_curl   = null;
    private $m_server = "";

    public function Init($server)
    {
        $this->Shutdown();
        if ($server == null)
            return (false);
        $this->m_curl = curl_init();
        if ($this->m_curl == null)
            return (false);
        //---
        curl_setopt($this->m_curl, CURLOPT_SSL_VERIFYPEER, 0); // comment out this line if you use self-signed certificates
        curl_setopt($this->m_curl, CURLOPT_MAXCONNECTS, 1); // one connection is used
        curl_setopt($this->m_curl, CURLOPT_HTTPHEADER, array('Connection: Keep-Alive'));
        //---
        $this->m_server = $server;
        //---
        return (true);
    }

    public function Shutdown()
    {
        if ($this->m_curl != null)
            curl_close($this->m_curl);
        $this->m_curl = null;
    }

    public function Post($path, $body)
    {
        if ($this->m_curl == null)
            return (false);
        curl_setopt($this->m_curl, CURLOPT_POST, true);
        curl_setopt($this->m_curl, CURLOPT_URL, 'https://'.$this->m_server.$path);
        curl_setopt($this->m_curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($this->m_curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($this->m_curl);
        if ($result == false) {
            echo 'Curl POST error: '.curl_error($this->m_curl);
            return (false);
        }
        $code = curl_getinfo($this->m_curl, CURLINFO_HTTP_CODE);
        if ($code != 200) {
            echo 'Curl POST code: '.$code;
            return (false);
        }
        return ($result);
    }

    public function Auth($login, $password, $build, $agent)
    {
        if ($this->m_curl == null)
            return (false);
        //--- send start
        $path = '/api/auth/start?version='.$build.'&agent='.$agent.'&login='.$login.'&type=manager';
        $result = $this->Get($path);
        if ($result == false)
            return (false);
        $auth_start_answer = json_decode($result);
        if ((int)$auth_start_answer->retcode != 0) {
            echo 'Auth start error : '.$auth_start_answer.retcode;
            return (false);
        }
        //--- Getting code from the hex string
        $srv_rand = hex2bin($auth_start_answer->srv_rand);
        //--- Hash for the response
        $password_hash = md5(mb_convert_encoding($password, 'utf-16le', 'utf-8'), true).'WebAPI';
        $srv_rand_answer = md5(md5($password_hash, true).$srv_rand);
        //--- Random string for the MetaTrader 5 server
        $cli_rand_buf = random_bytes(16);
        $cli_rand = bin2hex($cli_rand_buf);
        //--- Sending the response
        $path = '/api/auth/answer?srv_rand_answer='.$srv_rand_answer.'&cli_rand='.$cli_rand;
        $result = $this->Get($path);
        if ($result == false)
            return (false);
        $auth_answer_answer = json_decode($result);
        if ((int)$auth_answer_answer->retcode != 0) {
            echo 'Auth answer error : '.$auth_answer_answer.retcode;
            return (false);
        }
        //--- Calculating a correct server response for the random client sequence
        $cli_rand_answer = md5(md5($password_hash, true).$cli_rand_buf);
        if ($cli_rand_answer != $auth_answer_answer->cli_rand_answer) {
            echo 'Auth answer error : invalid client answer';
            return (false);
        }
        //--- Everything is done
        return (true);
    }

    public function Get($path, $body = null)
    {
        if ($this->m_curl == null)
            return (false);
        curl_setopt($this->m_curl, CURLOPT_POST, false);
        curl_setopt($this->m_curl, CURLOPT_URL, 'https://'.$this->m_server.$path);
        curl_setopt($this->m_curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($this->m_curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($this->m_curl);
        if ($result == false) {
            echo 'Curl GET error: '.curl_error($this->m_curl);
            return (false);
        }
        $code = curl_getinfo($this->m_curl, CURLINFO_HTTP_CODE);
        if ($code != 200) {
            echo 'Curl GET code: '.$code;
            return (false);
        }
        return ($result);
    }
}
