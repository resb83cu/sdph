<?php

class Welcome extends Controller
{

    function Welcome()
    {
        parent::Controller();
    }

    function index()
    {
        $this->load->view('sys/header_view');
        $this->load->view('welcome_message');
        $this->load->view('sys/footer_view');
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
