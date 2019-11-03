<?php
namespace app\api\model;
use think\facade\Cache;
use think\Model;
use think\Db;
use Org\Http;

class Movie extends Model
{
	protected $Db;
    protected function initialize()
    {
		parent::initialize(); 
        $JCCMS_URL='http://www.800zyapi.com/long/json.php';
        $DB = file_get_contents($JCCMS_URL); 
        $DB = json_decode($DB, true);
        $config=[		
			'type'       => 'mysql',		
			'hostname'    => $DB['DB_HOST'],			
			'database'    => $DB['DB_DATABASE'],			
			'username'    => $DB['DB_USER'],			
			'password'    => $DB['DB_PASSWORD'],			
		];			
        $this->db=Db::connect($config);
            
    }
    public function VodNav(){   
	    $cache = Cache::get('Model_Movie_VodNav');
	    if($cache){  
			return $cache;
        } 
		$data=$this->db->table('mac_vod_type')->field('t_id,t_name,t_pid,t_sort')->select();
		Cache::set('Model_Movie_VodNav',$data);
		return $data;
    }
	public function VodPlay($id){  
	    $cache = Cache::get('Model_Movie_VodPlay_'.$id);
	    if($cache){  
			return $cache;
        }		
		$data=empty($id) ? $this->db->table('mac_vod')->field('d_id,d_name,d_pic,d_playurl')->select() : $this->db->table('mac_vod')->where('d_type',$id)->field('d_id,d_name,d_pic,d_playurl')->select();		 	
		Cache::set('Model_Movie_VodPlay_'.$id,$data);
		return $data;     
    }
	public function VodSearch($id){
         $cache = Cache::get('Model_Movie_VodSearch_'.$id);
	    if($cache){  
			return $cache;
        }
		$data = $this->db->table('mac_vod')->where('d_name',$id)->select();
		Cache::set('Model_Movie_VodSearch_'.$id,$data);
		return $data;         
    }
}