<?php
class JI_Model extends SENE_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Generates encryption command
     * @param  [type] $val [description]
     * @return [type]      [description]
     */
    public function __encrypt($val)
    {
        return 'AES_ENCRYPT('.$this->db->esc($val).',"'.$this->db->enckey.'")';
    }
    
    /**
     * Generates decryption command
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function __decrypt($key)
    {
        return 'AES_DECRYPT('.$key.',"'.$this->db->enckey.'")';
    }
        
    public function trans_start()
    {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }
    public function trans_commit()
    {
        return $this->db->commit();
    }
    public function trans_rollback()
    {
        return $this->db->rollback();
    }
    public function trans_end()
    {
        return $this->db->autocommit(1);
    }
}
