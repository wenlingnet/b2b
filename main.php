<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Main extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('sell_model');
		$this->load->model('ex_category_model');
		$this->load->model('comm_model','comm');
		$this->load->test();
		$this->load->cache();
		$this->load->wenlingnet2008();
		$this->load->wenlinngetnew();
	}
	
	public function index(){
		$this->config->set_item("compress_output",TRUE);
		$hot_pros = $this->comm->findAll("sell","","hits desc","","0,5");
		if(!$hot_pros){
			$hot_pros = array();
		}
		
		$data['hot_pros'] = $hot_pros;
		
		
		$top_cates = $this->comm->findAll("category",array("parentid"=>0),"letter asc","","0,20");
		$data['top_cates'] = $top_cates;
		foreach($top_cates as $k=>$v){
			$sub_cate[$v['catid']] = $this->comm->findAll("category",array("parentid"=>$v['catid']),"","","0,3");
		}
		$data['sub_cate'] = $sub_cate;
		
		
		//获取该sell表中$catid下的最新供销信息列表
		$data['selllist'] = $this->comm->findAll("sell",array("status"=>3),"addtime desc","","0,5");
		//获取sell表中$catid下的最新公司列表
		$comlist = $this->comm->linker()->findAll("member","vmail=1 and company!=''","regtime desc","",'0,5');
		foreach($comlist as $k =>$v){
			$area = $this->comm->find("area",array("areaid"=>$v['mcompany']['areaid']));
			$comlist[$k]['abbr'] = $area['abbr'];
		}
		$data['comlist'] = $comlist;
		
		//获得关键词
		$keywords=$this->comm->findAll("tagindex",array("collect"=>1),"","","0,10");
		for ($i=0;$i<=2;$i++){
			$keywords1[]=array_shift($keywords);
		}
		$data['keywords1']=$keywords1;
		$data['keywords2']=$keywords;
		
		$site = $this->config->item("site");
		$data['site'] = $site;
		$data['title'] = $site['site_name'];
		
		$letter=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$data['letter'] = $letter;
		
		$this->load->library('encrypt');
		$username = $this->input->cookie('username', TRUE);
		$hash_1 = $this->input->cookie('hash_1', TRUE);
		$data['username'] = $this->encrypt->decode($username,$hash_1);
		
		header('Content-Language:en');
		$this->load->view('main_index',$data);
		
	}	
}