<?php
require(APPPATH.'/libraries/RESTController.php');


class Site extends REST_Controller
{
    function __construct()
    {
        // header("Access-Control-Allow-Origin: *");

        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        /*
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        */
    }


    function index_get()
    {

        if(!$this->get('id'))
        {
            $account_id = 2;
            $Site = Library\Logic\Leads\Site::getByAccount($account_id);
        }
        elseif ((int)$this->get('id') > 0)
        {
            $Site = Library\Logic\Leads\Site::get($this->get('id'));
        }

        if ($Site) {
            $this->set_response($Site->getArray(), REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Account could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
     * used for insert update
     */
    function index_post()
    {
        $result  = false;

        $meta = $this->input->post();
        $result = $Account = Library\Logic\Account::update($meta);

        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
        else
        {
            return $this->set_response($result, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

    }

    /**
     * used for insert update
     */
    function index_put()
    {
        $first_name = $this->input->put('first_name');

        $data = array('first_name'=>$first_name);
        $result = true;

        /*
        $result = $this->category_model->update( $this->post('id'), array(
            'name' => $this->post('name'),
            'email' => $this->post('email')
        ));
        */

        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
        else
        {
            return $this->set_response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

    }

}