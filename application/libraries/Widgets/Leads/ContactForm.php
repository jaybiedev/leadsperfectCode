<?php
namespace Library\Widgets\Leads;

class ContactForm extends \Library\Widgets\WidgetsAbstract {
    
    protected $CI;
    protected $db;
    
    private $Spol;
    
    public $Site;
    
	public function __construct () {

	    $this->CI =& get_instance();
	    $this->db = $this->CI->db;

	    $this->elementID = 'Spol';
	    $this->Spol = new \Library\Logic\Leads\Spol();	    
	}
	
	public function render() {
	    echo $this->getContent();
	}
	
	/**
	 * This widget requires, /js/system.js
	 */
	public function getContent($form_id='contact-form') {	    
	    $now = time();
	    $token = \Library\Helper\Utils::generateToken($this->Site, $now);
	    $guid = $this->Site->guid;

        $html =<<<HTML
                          <form class="contact-form-simple" name="contact-form" action="" method="POST" sid="{$token}">
                              <input type="hidden" readOnly="readOnly" name="timestamp"  value="{$now}" />
                              <input type="hidden" readOnly="readOnly" name="guid"  value="{$guid}" />
                              <!--Grid row-->
                              <div class="row">
                                  <!--Grid column-->
                                  <div class="col-md-12">
                                      <div class="md-form">
                                          <input type="text" id="name" name="name" class="form-control" placeholder="Your name" />
                                      </div>
                                  </div>
                                  <!--Grid column-->

                                  <!--Grid column-->
                                  <div class="col-md-12">
                                      <div class="md-form">
                                          <input type="email" id="email" name="email" class="form-control" placeholder="Your email address" />
                                      </div>
                                  </div>
                                  <!--Grid column-->
                              </div>
                              <!--Grid row-->

                              <!--Grid row-->
                              <div class="row">
                                  <div class="col-md-12">
                                      <div class="md-form">
                                          <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject" />
                                      </div>
                                  </div>
                              </div>
                              <!--Grid row-->

                              <!--Grid row-->
                              <div class="row">

                                  <!--Grid column-->
                                  <div class="col-md-12">

                                      <div class="md-form">
                                          <textarea type="text" id="message" name="message" rows="2" class="form-control md-textarea"></textarea>
                                      </div>

                                  </div>
                              </div>
                              <!--Grid row-->
                              <div>
                                  <a href="javascript:void(0);" class="btn btn-primary contact-form-simple-widget">Send</a>
                              </div>

                          </form>
HTML;
        return $html;
	}
	
	
}

