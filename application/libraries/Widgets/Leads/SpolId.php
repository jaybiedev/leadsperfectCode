<?php
namespace Library\Widgets\Leads;

class SpolId extends \Library\Widgets\WidgetsAbstract {
    
    protected $CI;
    protected $db;
    
    private $Spol;
    
	public function __construct () {

	    $this->CI =& get_instance();
	    $this->db = $this->CI->db;
	    
	    $this->elementID = 'Spol';
	    $this->Spol = new \Library\Logic\Leads\Spol();	    
	}
	
	public function render() {
	    echo $this->getContent();
	}
	
	public function getContent() {	 
	    $Spol = $this->Spol->getNext();
	    if (empty($Spol))
	        return null;
	    
        return $Spol->id;
	}

	
}