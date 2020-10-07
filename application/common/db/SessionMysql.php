<?php
/**
 * Created by PhpStorm.
 * User: 84770
 * Date: 2020/9/15
 * Time: 8:33
 */

//defined('IN_QIAN') or exit('Access Denied');


class SessionMysql
{

    public $lifetime = 60 * 10; // 有效期，单位：秒（s），默认30分钟
    public $db;
    public $table;

    public $isUse = false;

    private $instance;

    /**
     * 构造函数
     */
    public function __construct($db) {
        $this->db = $db;
        if($this->isUse){
            session_set_save_handler(
                array(&$this, 'open'),      // 在运行session_start()时执行
                array(&$this, 'close'),     // 在脚本执行完成 或 调用session_write_close() 或 session_destroy()时被执行，即在所有session操作完后被执行
                array(&$this, 'read'),      // 在运行session_start()时执行，因为在session_start时，会去read当前session数据
                array(&$this, 'write'),     // 此方法在脚本结束和使用session_write_close()强制提交SESSION数据时执行
                array(&$this, 'destroy'),   // 在运行session_destroy()时执行
                array(&$this, 'gc')         // 执行概率由session.gc_probability 和 session.gc_divisor的值决定，时机是在open，read之后，session_start会相继执行open，read和gc
            );
        }
        session_start(); // 这也是必须的，打开session，必须在session_set_save_handler后面执行
        setcookie(session_name(),session_id(),time()+ $this-> lifetime,'/');
    }

    public static function getInstance($db){
        if(isset($instance)){
            return $instance;
        }
        return new SessionMysql($db);
    }

    /**
     * session_set_save_handler open方法
     *
     * @param $savePath
     * @param $sessionName
     * @return true
     */
    public function open($savePath, $sessionName) {
        return true;
    }


    /**
     * session_set_save_handler close方法
     *
     * @return bool
     */
    public function close() {
        return $this->gc($this->lifetime);
    }

    /**
     * 读取session_id
     *
     * session_set_save_handler read方法
     * @return string 读取session_id
     */
    public function read($sessionId) {

        $sql = "SELECT data FROM session WHERE id = '" . $sessionId . "'" ;

        $row = $this->db->getRow($sql);
        return $row ? $row['data']: '';
    }


    /**
     * 写入session_id 的值
     *
     * @param $sessionId 会话ID
     * @param $data 值
     * @return mixed query 执行结果
     */
    public function write($sessionId, $data) {
        $sql = "SELECT * FROM session WHERE id = '" .$sessionId  . "'";
        $result = $this->db->getAll($sql);
        if(sizeof($result) > 0){
            $dateTime = strtotime('now');
            $last_visit = date('Y-m-d H:i:s', $dateTime);
            $sql = "UPDATE session SET data = '" . $data . "', last_visit = '" . $last_visit . "'
               WHERE id = '" .$sessionId  . "'
            ";
            return  $this->db->query($sql);
        } else {
            $dateTime = strtotime('now');
            $last_visit = date('Y-m-d H:i:s', $dateTime);
            $data = array('id'=>$sessionId,'data'=>$data,'last_visit'=>$last_visit);
            return $this->db->insert('session',$data);
        }
    }

    /**
     * 删除指定的session_id
     *
     * @param string $sessionId 会话ID
     * @return bool
     */
    public function destroy($sessionId) {
        return $this->db->deleteOne('session',array('id' => $sessionId));
    }

    /**
     * 删除过期的 session
     *
     * @param $lifetime session有效期（单位：秒）
     * @return bool
     */
    public function gc($lifetime) {
        $dateTime = strtotime('-' .( $lifetime / 60 ) . ' minute');
        $expireTime = date('Y-m-d H:i:s', $dateTime);
        $where = ' last_visit < \'' . $expireTime .'\'';
        return $this->db->deleteOne('session',$where);
    }


}