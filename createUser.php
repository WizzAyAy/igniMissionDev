<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use DB;

class createUser extends Command
{
 /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-users {--N= : nom de l\'utilisateur}
                                         {--E= : email de l\'utilisateur}
                                         {--P= : password de l\'utilisateur}
                                         {--h : help}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commande ajoutant un user à notre table users de la base TEST (--h) pour afficher l\'aide';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //je pars du principe que la table users exite ansi que la base de données TEST
        //username de mysql est root donc la commande ne va marcher que si vous utilisez sudo php artisan ...

        // exemple appel qui marche :
        //  sudo php artisan create-users --h
        //  sudo php artisan create-users --N testName --E laravel@laravel.fr --P password
        //  sudo php artisan create-users --N testName --E laravel@laravel.com --P password
        //  sudo php artisan create-users --N testName2 --E laravel@laravel.com --P password
        //  sudo php artisan create-users --N testName3 --E laravel@laravel.com --P password

        /* affichage de mysql apres ces 5 lignes :
            mysql> select * from users;
            +-----------+---------------------+--------------------------------------------------------------+
            | name      | email               | password                                                     |
            +-----------+---------------------+--------------------------------------------------------------+
            | testName  | laravel@laravel.com | $2y$10$lrdXLME.jUegsEqlZ1pG6eeiHY56FrXD11ed2VnYhHucXgpk8KJOW |
            | testName2 | laravel@laravel.com | $2y$10$bF0tXW/0XnFh7wH5SgGvNexoT.18AzcLJ0ndgNNuAqp88Y/Q18qui |
            | testName3 | laravel@laravel.com | $2y$10$AkSHOwU01ggD6jfMmv6NDu7IRXqTbhCx4KgV/Cl99BLVuKBU0jviK |
            +-----------+---------------------+--------------------------------------------------------------+
            3 rows in set (0.00 sec)
        */
        
        // exemple appel qui ne marche pas et provoque une erreur
        //  sudo php artisan create-users --N testName --E fakemail --P password
        //  sudo php artisan create-users --E laravel@laravel.fr --P password
        //  sudo php artisan create-users --N testName --E laravel@laravel.fr 
        //  sudo php artisan create-users --N testName 
        //  sudo php artisan create-users 

        
        /*
        CREATE TABLE IF NOT EXISTS users (
            name varchar(30) NOT NULL,
            email varchar(50) NOT NULL,
            password varchar(200) NOT NULL,
            PRIMARY KEY (name)
            );
        */

        //dans le fichier /app/config/database.php 
        /*'mysql' => [
            'read' => [
                'host' => 'localhost',
            ],
            'write' => [
                'host' => 'localhost'
            ],
            'driver'    => 'mysql',
            'database'  => 'TEST',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        */

        //option true / false
        $help = $this->option('h');

        if($help == true){
            //option help activée : affichage de l'aide
            $this->info('utiliser la commande comme ceci : php artisan create-users <name> <email> <password>');
            return 0;
        }

        //recuperation des champs name, email et password
        $userName = strval($this->option('N'));
        $userEmail = strval($this->option('E'));
        $userPassword = strval($this->option('P'));

        //on regarde si tous les champs sont presents
        if(empty($userName) || empty($userEmail) || empty($userPassword)){
            //attention tous les champs ne sont pas remplis !
            $this->error('attention tous les champs ne sont pas remplis ! --h pour voir l\'aide');
            return 1;
        }

        //check si l'adresse mail est valide 
        if(filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            //attention FILTER_VALIDATE_EMAIL ne verifie pas que l'adresse mail existe vraiment.
            //addresse valide donc on ne fait rien 
            //$this->info('le champs mail correspond a une addresse email valide !');
        }
        else {
            // addresse invalide donc on arrete et on affiche une erreur 
            $this->error('attention, adresse mail non valide !');
            return 1;
        }      
        
        //recupere toute la table user dans users ou le name == username
        $users = array();
        try{
            $users = DB::table('users')->select('*')->where('name', $userName)->get();
        }
        catch (QueryException $e){
            $this->error('Access denied vous devez etre en mode sudo pour acceder a la base TEST');
            $this->error('Exception reçue : '.  $e->getMessage());
            return 1;
        }

        //check si le select nous a renvoyé une ligne
        //on aurait pu faire un insert or update, mais je veux laisser le choix a l'utilisateur de confirmer l'update que lorsqu'il le veut vraiment
        if (sizeof($users) != 0){
            //il y a deja un user avec ce nom dans la base de donnée
            $this->error('Attention! '.$userName. ' est déja present dans la base ...');
            if(!$this->confirm('Voulez vous effacer les anciennes entrées de '. $userName)){
                
                //l'utilisateur a choisi de conserver les anciennes données
                $this->error('Abandon !');
                return 0;
            }
            //l'utilisateur a confirmé la suppression des anciennes données
            //update du mot de passe et de l'email de l'user

            $this->info('update de l\'user : ' . $userName . ', dans la base de données');
            DB::table('users')
            ->where('name', $userName)
            ->update(['email' => $userEmail, 'password' => password_hash($userPassword,PASSWORD_DEFAULT)]);
            $this->info('table update!');
        }

        else {
            //aucun user avec ce nom dans la base de données
            //on hash le password et on ajoute le user dans la base de données
            $this->info('ajout de l\'user : ' . $userName . ', dans la base de données');
            DB::table('users')->insert([
                'name' => $userName,
                'email' => $userEmail,
                'password' => password_hash($userPassword,PASSWORD_DEFAULT)
            ]);
            $this->info('values inserted!');
        } 
        
        return 0;
    }
}
