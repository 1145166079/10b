<?php
/*
 * 非对称加密类
 */
class OpensslClass {

    private $config = [
        'config' => 'D:\phpStudy\Apache\conf\openssl.cnf',
    ];
    
    private $path = './key/';

    private $private_key;
    private $public_key;

    /*
     * 生成公钥与私钥并保存为文件
     */
    public function setKey() {
        //创建一个证书，传入openssl.cnf配置文件的路径
        $openssl = openssl_pkey_new($this->config);
        //生成一个私钥
        openssl_pkey_export($openssl, $private_key, null, $this->config);
        //将私钥存为文件
        file_put_contents($this->path . 'private.key', $private_key);
        //获取证书信息
        $data = openssl_pkey_get_details($openssl);
        //获取公钥
        $public_key = $data['key'];
        //将公钥存为文件
        file_put_contents($this->path . 'public.key', $public_key);
    }

    /*
     * 加载公钥与私钥
     */
    public function loadKey(){
        $private_key_str = file_get_contents($this->path . 'private.key');
        $public_key_str = file_get_contents($this->path . 'public.key');
        //获取私钥句柄
        $this->private_key = openssl_pkey_get_private($private_key_str);
        //获取公钥句柄
        $this->public_key = openssl_pkey_get_public($public_key_str);
    }
    
    /*
     * 私钥加密的方法
     */
    public function privateEncrypt($data){
        openssl_private_encrypt($data, $encryptData, $this->private_key);
        return base64_encode($encryptData);
    }
    
    /*
     * 公钥解密的方法
     */
    public function publicDecrypt($data){
        $encryptData = base64_decode($data);
        openssl_public_decrypt($encryptData, $decryptData, $this->public_key);
        return $decryptData;
    }
    
    /*
     * 公钥加密的方法
     */
    public function publicEncrypt($data){
        openssl_public_encrypt($data, $encryptData, $this->public_key);
        return base64_encode($encryptData);
    }
    
    /*
     * 私钥解密的方法
     */
    public function privateDecrypt($data){
        $encryptData = base64_decode($data);
        openssl_private_decrypt($encryptData, $decryptData, $this->private_key);
        return $decryptData;
    }
}