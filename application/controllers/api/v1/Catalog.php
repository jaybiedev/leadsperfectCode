<?php
require(APPPATH.'/libraries/RESTController.php');


class Catalog extends REST_Controller
{
    function __construct()
    {
        header("Access-Control-Allow-Origin: *");

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
            $Catalog = Library\Logic\Catalog::get();
        }
        elseif ((int)$this->get('id' > 0))
        {
            $Catalog = Library\Logic\Catalog::get($this->get('id'));
        }

        if ($Catalog) {
            $this->set_response($Catalog->getArray(), REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Catalog could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    function index_post()
    {
        $result = $this->category_model->update( $this->post('id'), array(
            'name' => $this->post('name'),
            'email' => $this->post('email')
        ));

        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }

        else
        {
            $this->response(array('status' => 'success'));
        }

    }


}