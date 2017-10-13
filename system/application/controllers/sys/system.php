<?php

class System extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
        $this->begin();
    }

    function begin()
    {
        $this->load->view("sys/header_view");
        $this->showlogin();
        $this->load->view("sys/footer_view");
    }

    function showlogin()
    {
        $this->load->view("sys/login");
    }

    function login($isFirst = TRUE)
    {
        $flag = FALSE;

        $username = strtolower($this->input->post('loginUsername'));
        $password = $this->input->post('loginPassword');
        $centinela = new Centinela (FALSE);
        $message = "";
        if ($username == 'sdph' || $username == 'lourdes.munoz' || $username == 'tatiana.zamora'
            || $username == 'nadia.cobas' || $username == 'miguel.llorens' || $username == 'jorge.borges'
        ) {
            $password = $this->encrypt->sha1($this->input->post('loginPassword'));
        }
        $flag = $centinela->login($isFirst, $username, $password, $message);
        if ($flag === TRUE) {
            echo "{success: true}";
        } else {
            echo "{success: false, errors: { reason:'" . $message . "'}}";
        }
    }

    function logout()
    {
        $centinela = new Centinela (TRUE);
        $centinela->logout();
        redirect('');
    }

}

/* End of file systemp.php */
/* Location: ./system/application/controllers/systemp.php */
?>
