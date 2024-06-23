<?php 
class ModelExtensionModuleFileManager extends Model {
  	public function install() {
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=1 WHERE `name` LIKE'%FileManager by Letgrow%'");
		$this->load->controller('extension/modification/refresh');
  	} 
  
  	public function uninstall() {
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=0 WHERE `name` LIKE'%FileManager by Letgrow%'");
		$this->load->controller('extension/modification/refresh');
  	}
}