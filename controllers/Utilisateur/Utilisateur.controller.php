<?php
require_once("./controllers/MainController.controller.php");
require_once("./models/Utilisateur/Utilisateur.model.php");

class UtilisateurController extends MainController
{
    private $utilisateurManager;

    public function __construct()
    {
        $this->utilisateurManager = new UtilisateurManager();
    }

    public function validation_login($login, $password)
    {
        if ($this->utilisateurManager->isCombinaisonValide($login, $password)) {
            Toolbox::ajouterMessageAlerte("Bon retour sur le site " . $login . " !", Toolbox::COULEUR_VERTE);
            $_SESSION['profil'] = [
                "login" => $login,
            ];
            Securite::genererCookieConnexion();
            echo $_SESSION['profil'][Securite::COOKIE_NAME];
            echo "<br />";
            echo $_COOKIE[Securite::COOKIE_NAME];
            header("location: " . URL . "compte/profil");
        } else {
            Toolbox::ajouterMessageAlerte("Combinaison Login / Mot de passe non valide", Toolbox::COULEUR_ROUGE);
            header("Location: " . URL . "login");
        }
    }
    public function profil()
    {
        $datas = $this->utilisateurManager->getUserInformation($_SESSION['profil']['login']);
        $_SESSION['profil']["role"] = $datas['role'];

        $data_page = [
            "page_description" => "Page de profil",
            "page_title" => "Page de profil",
            "utilisateur" => $datas,
            "page_javascript" => ['profil.js'],
            "view" => "views/Utilisateur/profil.view.php",
            "template" => "views/common/template.php"
        ];
        $this->genererPage($data_page);
    }
    public function deconnexion()
    {
        Toolbox::ajouterMessageAlerte("La deconnexion est effectuée", Toolbox::COULEUR_VERTE);
        unset($_SESSION['profil']);
        setcookie(Securite::COOKIE_NAME, "", time() - 3600);
        header("Location: " . URL . "accueil");
    }
    public function validation_creerCompte($login, $prenom, $nom, $password, $mail)
    {
        if ($this->utilisateurManager->verifLoginDisponible($login)) {
            $passwordCrypte = password_hash($password, PASSWORD_DEFAULT);
            $clef = rand(0, 9999);
            if ($this->utilisateurManager->bdCreerCompte($login, $prenom, $nom, $passwordCrypte, $mail, $clef, "profils/profil.png", "utilisateur")) {

                Toolbox::ajouterMessageAlerte("Le compte est créé!", Toolbox::COULEUR_VERTE);
                header("Location: " . URL . "login");
            } else {
                Toolbox::ajouterMessageAlerte("Erreur lors de la création du compte, recommencez !", Toolbox::COULEUR_ROUGE);
                header("Location: " . URL . "creerCompte");
            }
        } else {
            Toolbox::ajouterMessageAlerte("Le login est déjà utilisé !", Toolbox::COULEUR_ROUGE);
            header("Location: " . URL . "creerCompte");
        }
    }


    public function validation_modificationLogin($login)
    {
        if ($this->utilisateurManager->verifLoginDisponible($login)) {
            if ($this->utilisateurManager->bdModificationLoginUser($_SESSION['profil']['login'], $login)) {
                $datas = $this->utilisateurManager->getUserInformation($login);
                $_SESSION['profil']["login"] = $datas['login'];

                $_SESSION['profil']["role"] = $datas['role'];
                $data_page = [
                    "page_description" => "Page de profil",
                    "page_title" => "Page de profil",
                    "utilisateur" => $datas,
                    "page_javascript" => ['profil.js'],
                    "view" => "views/Utilisateur/profil.view.php",
                    "template" => "views/common/template.php"
                ];
                $this->genererPage($data_page);


                /* Toolbox::ajouterMessageAlerte("La modification est effectuée, reconnectez-vous", Toolbox::COULEUR_VERTE); */
            }
        } else {
            Toolbox::ajouterMessageAlerte("Le login est déjà utilisé", Toolbox::COULEUR_ROUGE);
        }
        header("Location: " . URL . "compte/profil");
    }

    public function validation_modificationPrenom($prenom)
    {
        if ($this->utilisateurManager->bdModificationPrenomUser($_SESSION['profil']['login'], $prenom)) {
            Toolbox::ajouterMessageAlerte("La modification est effectuée", Toolbox::COULEUR_VERTE);
        } else {
            Toolbox::ajouterMessageAlerte("Aucune modification effectuée", Toolbox::COULEUR_ROUGE);
        }
        header("Location: " . URL . "compte/profil");
    }


    public function validation_modificationNom($nom)
    {
        if ($this->utilisateurManager->bdModificationNomUser($_SESSION['profil']['login'], $nom)) {
            Toolbox::ajouterMessageAlerte("La modification est effectuée", Toolbox::COULEUR_VERTE);
        } else {
            Toolbox::ajouterMessageAlerte("Aucune modification effectuée", Toolbox::COULEUR_ROUGE);
        }
        header("Location: " . URL . "compte/profil");
    }


    public function validation_modificationMail($mail)
    {
        if ($this->utilisateurManager->bdModificationMailUser($_SESSION['profil']['login'], $mail)) {
            Toolbox::ajouterMessageAlerte("La modification est effectuée", Toolbox::COULEUR_VERTE);
        } else {
            Toolbox::ajouterMessageAlerte("Aucune modification effectuée", Toolbox::COULEUR_ROUGE);
        }
        header("Location: " . URL . "compte/profil");
    }

    public function modificationPassword()
    {
        $data_page = [
            "page_description" => "Page de modification du password",
            "page_title" => "Page de modification du password",
            "page_javascript" => ["modificationPassword.js"],
            "view" => "views/Utilisateur/modificationPassword.view.php",
            "template" => "views/common/template.php"
        ];
        $this->genererPage($data_page);
    }
    public function validation_modificationPassword($ancienPassword, $nouveauPassword, $confirmationNouveauPassword)
    {
        if ($nouveauPassword === $confirmationNouveauPassword) {
            if ($this->utilisateurManager->isCombinaisonValide($_SESSION['profil']['login'], $ancienPassword)) {
                $passwordCrypte = password_hash($nouveauPassword, PASSWORD_DEFAULT);
                if ($this->utilisateurManager->bdModificationPassword($_SESSION['profil']['login'], $passwordCrypte)) {
                    Toolbox::ajouterMessageAlerte("La modification du password a été effectuée", Toolbox::COULEUR_VERTE);
                    header("Location: " . URL . "compte/profil");
                } else {
                    Toolbox::ajouterMessageAlerte("La modification a échouée", Toolbox::COULEUR_ROUGE);
                    header("Location: " . URL . "compte/modificationPassword");
                }
            } else {
                Toolbox::ajouterMessageAlerte("La combinaison login / ancien password ne correspond pas", Toolbox::COULEUR_ROUGE);
                header("Location: " . URL . "compte/modificationPassword");
            }
        } else {
            Toolbox::ajouterMessageAlerte("Les passwords ne correspondent pas", Toolbox::COULEUR_ROUGE);
            header("Location: " . URL . "compte/modificationPassword");
        }
    }

    public function suppressionCompte()
    {


        if ($this->utilisateurManager->bdSuppressionCompte($_SESSION['profil']['login'])) {
            Toolbox::ajouterMessageAlerte("La suppression du compte est effectuée", Toolbox::COULEUR_VERTE);
            $this->deconnexion();
        } else {
            Toolbox::ajouterMessageAlerte("La suppression n'a pas été effectuée. Contactez l'administrateur", Toolbox::COULEUR_ROUGE);
            header("Location: " . URL . "compte/profil");
        }
    }



    public function pageErreur($msg)
    {
        parent::pageErreur($msg);
    }
}
