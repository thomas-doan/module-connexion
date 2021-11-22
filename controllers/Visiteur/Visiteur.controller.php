<?php
require_once("./controllers/MainController.controller.php");
require_once("./models/Visiteur/Visiteur.model.php");
require_once("./controllers/Securite.class.php");

class VisiteurController extends MainController
{
    private $visiteurManager;

    public function __construct()
    {
        $this->visiteurManager = new VisiteurManager();
    }

    public function accueil()
    {

        $data_page = [
            "page_description" => "Nos bouquets Zen",
            "page_title" => "Flower power",
            "page_css" => ["main_home.css"],
            "view" => "views/Visiteur/accueil.view.php",
            "template" => "views/common/template.php"
        ];
        $this->genererPage($data_page);
    }

    public function login()
    {
        $data_page = [
            "page_description" => "Page de connexion Flower power",
            "page_title" => "Page de connexion flower power",
            "page_css" => ["login.css", "main_home.css"],
            "view" => "views/Visiteur/login.view.php",
            "template" => "views/common/template.php"
        ];
        $this->genererPage($data_page);
    }

    public function creerCompte()
    {
        $data_page = [
            "page_description" => "Page de création de compte",
            "page_title" => "Page de création de compte flower power",
            "page_css" => ["login.css", "main_home.css"],
            "view" => "views/Visiteur/creerCompte.view.php",
            "template" => "views/common/template.php"
        ];
        $this->genererPage($data_page);
    }

    public function pageErreur($msg)
    {
        parent::pageErreur($msg);
    }
}
