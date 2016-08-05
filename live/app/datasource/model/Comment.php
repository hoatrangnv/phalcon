<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Comment extends BaseModel
{
    public $id;
    public $article_id;
    public $user_id; 		
    public $description; 	
    public $status; 
    public $created_at;	
    public $updated_at;	
    public $created_ip;
    public $user_agent;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('comment');
    }
}