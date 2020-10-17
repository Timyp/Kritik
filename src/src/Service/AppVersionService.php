<?php


namespace App\Service;


/**
 * Classe d'entrainement sur les services et la configuration
 */
class AppVersionService
{

    private string $appVersion;
    private string $env;

    /**
     * On peut utiliser l'autowiring dans les constructeurs
     * @param string $appVersion = paramètre défini en "bind" dans le services.yaml
     * @param string $env = argument spécifique dans la déclaration de service dans le services.yaml
     */
    public function __construct(string $appVersion, string $env)
    {
        $this->appVersion = $appVersion;
        $this->env = $env;
    }


    public function getAppVersion(): string
    {
        return $this->appVersion;
    }


    public function getEnv(): string
    {
        return $this->env;
    }
}