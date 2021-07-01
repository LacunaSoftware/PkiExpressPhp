<?php

namespace Lacuna\PkiExpress;


/**
 * Class TrustServicesManager
 * @package Lacuna\PkiExpress
 */
class TrustServicesManager extends PkiExpressOperator
{
    public function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        parent::__construct($config);
    }

    /**
     * Check if the given CPF exists on the given trusted services.
     *
     * @return CheckServiceResult The result of the check for services.
     * @throws \Exception If at least one of the following parameters are not provided:
     *  - The service;
     *  - The CPF;
     */
    public function checkByCpf($service, $cpf)
    {
        if (empty($service)){
            throw new \Exception("The provided service is not valid");
        }
        if (empty($cpf)){
            throw new \Exception("The provided CPF is not valid");
        }

        $args = array();
        $args[] = $service;
        $args[] = '--cpf';
        $args[] = $cpf;

        // This operation can only be used on versions greater than 1.18 of
        // the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command
        $response = parent::invoke(parent::COMMAND_CHECK_SERVICE, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new CheckServiceResult($parsedOutput);
        return $result;
    }

    /**
     * Check if the given CNPJ exists on the given trusted services.
     *
     * @return CheckServiceResult The result of the check for services.
     * @throws \Exception If at least one of the following parameters are not provided:
     *  - The service;
     *  - The CNPJ;
     */
    public function checkByCnpj($service, $cnpj)
    {
        if (empty($service)){
            throw new \Exception("The provided service is not valid");
        }
        if (empty($cnpj)){
            throw new \Exception("The provided CNPJ is not valid");
        }

        $args = array();
        $args[] = $service;
        $args[] = '--cnpj';
        $args[] = $cnpj;

        // This operation can only be used on versions greater than 1.18 of
        // the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command
        $response = parent::invoke(parent::COMMAND_CHECK_SERVICE, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new CheckServiceResult($parsedOutput);
        return $result;
    }

    /**
     * Search in all configured trusted services to find those that have a 
     * certificate that contains the provided CPF.
     *
     * @return TrustServiceInfo The result of the search. A list with all services.
     * @throws \Exception If the CPF is not provided.
     */
    public function discoverByCpf($cpf, $throw_exceptions=False)
    {
        if (empty($cpf)){
            throw new \Exception("The provided CPF is not valid");
        }

        $args = array();
        $args[] = '--cpf';
        $args[] = $cpf;

        if ($throw_exceptions){
            $args[] = '--throw';
        }

        // This operation can only be used on versions greater than 1.18 of
        // the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_DISCOVER_SERVICES, $args);

        // Parse output
        $parsedOutput = $this->parseOutput($response->output[0]);
        // Convert response
        $result = new DiscoverServicesResult($parsedOutput);
        return $result->services;
    }

    /**
     * Search in all configured trusted services to find those that have a 
     * certificate that contains the provided CNPJ.
     *
     * @return TrustServiceInfo The result of the search. A list with all services.
     * @throws \Exception If the CNPJ is not provided.
     */
    public function discoverByCnpj($cnpj, $throw_exceptions=False)
    {
        if (empty($cnpj)){
            throw new \Exception("The provided CNPJ is not valid");
        }

        $args = array();
        $args[] = '--cnpj';
        $args[] = $cnpj;

        if ($throw_exceptions){
            $args[] = '--throw';
        }

        // This operation can only be used on versions greater than 1.18 of
        // the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_DISCOVER_SERVICES, $args);

        // Parse output.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new DiscoverServicesResult($parsedOutput);
        return $result->services;
    }

    /**
     * Search in all configured trusted services to find those that have a 
     * certificate that contains the provided CPF and start the authentication.
     *
     * @return TrustServiceAuthParameters The result of the search. A list with all services.
     * @throws \Exception If at least one of the following parameters are not provided:
     *  - The CPF;
     *  - The redirectUrl;
     *  - The sessionType;
     */
    public function discoverByCpfAndStartAuth(
        $cpf,
        $redirectUrl,
        $sessionType = TrustServiceSessionTypes::SIGNATURE_SESSION,
        $customState = null,
        $throw_exceptions = False,
        $lifetime = null)
    {
        if (empty($cpf)){
            throw new \Exception("The provided CPF is not valid");
        }
        if (empty($redirectUrl)){
            throw new \Exception("The provided redirectUrl is not valid");
        }
        if (empty($sessionType)){
            throw new \Exception("No session type was provided");
        }

        $args = array();

        // Add CPF
        $args[] = '--cpf';
        $args[] = $cpf;

        // Add redirect URL
        $args[] = '--redirect-url';
        $args[] = $redirectUrl;

        // Add session type
        $args[] = '--session-type';
        $args[] = $sessionType;

        if ($customState != null){
            $args[] = '--custom-state';
            $args[] = $customState;
        }

        if ($throw_exceptions){
            $args[] = '--throw';
        }

        if ($lifetime != null){
            $args[] = "--session-lifetime";
            $args[] = $lifetime;

            // This operation can only be used on versions greater than
            // 1.24 of the PKI Express.
            $this->versionManager->requireVersion("1.24");
        }

        // This operation can only be used on versions greater than 1.18 of
        // the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_DISCOVER_SERVICES, $args);

        // Parse output and return result.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new DiscoverServicesResult($parsedOutput);
        return $result->authParameters;
    }

    /**
     * Search in all configured trusted services to find those that have a 
     * certificate that contains the provided CNPJ and start the authentication.
     *
     * @return TrustServiceAuthParameters The result of the search. A list with all services.
     * @throws \Exception If at least one of the following parameters are not provided:
     *  - The CNPJ;
     *  - The redirectUrl;
     *  - The sessionType;
     */
    public function discoverByCnpjAndStartAuth(
        $cnpj,
        $redirectUrl,
        $sessionType = TrustServiceSessionTypes::SIGNATURE_SESSION,
        $customState = null,
        $throw_exceptions = False,
        $lifetime = null)
    {
        if (empty($cnpj)){
            throw new \Exception("The provided CNPJ is not valid");
        }
        if (empty($redirectUrl)){
            throw new \Exception("The provided redirectUrl is not valid");
        }
        if (empty($sessionType)){
            throw new \Exception("No session type was provided");
        }

        $args = array();

        // Add CNPJ
        $args[] = '--cnpj';
        $args[] = $cnpj;

        // Add redirect URL
        $args[] = '--redirect-url';
        $args[] = $redirectUrl;

        // Add session type
        $args[] = '--session-type';
        $args[] = $sessionType;

        if ($customState != null){
            $args[] = '--custom-state';
            $args[] = $customState;
        }

        if ($throw_exceptions){
            $args[] = '--throw';
        }

        if ($lifetime != null){
            $args[] = "--session-lifetime";
            $args[] = $lifetime;

            // This operation can only be used on versions greater than
            // 1.24 of the PKI Express.
            $this->versionManager->requireVersion("1.24");
        }

        // This operation can only be used on versions greater than 1.18 of
        // the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_DISCOVER_SERVICES, $args);

        // Parse output and return result.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new DiscoverServicesResult($parsedOutput);
        return $result->authParameters;
    }

    /**
     * Authorize the session on the given service using the given username and password.
     *
     * @return TrustServiceSessionResult The session informations
     * @throws \Exception If at least one of the following parameters are not provided:
     *  - The service;
     *  - The username;
     *  - The password;
     *  - The sessionType;
     */
    public function passwordAuthorize($service, $username, $password, $sessionType=TrustServiceSessionTypes::SIGNATURE_SESSION, $lifetime=null)
    {
        if (empty($service)){
            throw new \Exception("The provided service is not valid");
        }
        if (empty($username)){
            throw new \Exception("The provided username is not valid");
        }
        if (empty($password)){
            throw new \Exception("The provided password is not valid");
        }
        if (empty($sessionType)){
            throw new \Exception("No session type was provided");
        }

        $args = array();

        // Add service.
        $args[] = $service;

        // Add username.
        $args[] = $username;

        // Add password.
        $args[] = $password;

        // Add sessionType.
        $args[] = $sessionType;

        if ($lifetime != null){
            $args[] = "--session-lifetime";
            $args[] = $lifetime;

            // This operation can only be used on versions greater than
            // 1.24 of the PKI Express.
            $this->versionManager->requireVersion("1.24");
        }

        // This operation can only be used on versions greater than
        // 1.18 of the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_PASSWORD_AUTHORIZE, $args);

        // Parse output and return result.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new TrustServiceSessionResult($parsedOutput);
        return $result;
    }

    /**
     * Complete the authorization with the trusted service.
     *
     * @return TrustServiceSessionResult The session informations
     * @throws \Exception If at least one of the following parameters are not provided:
     *  - The service;
     *  - The username;
     *  - The password;
     *  - The sessionType;
     */
    public function completeAuth($code, $state)
    {
        if (empty($code)){
            throw new \Exception("The provided code is not valid");
        }
        if (empty($state)){
            throw new \Exception("The provided state is not valid");
        }

        $args = array();

        // Add code
        $args[] = $code;
        // Add state
        $args[] = $state;

        // This operation can only be used on versions greater than 1.18 of
        // the PKI Express.
        $this->versionManager->requireVersion("1.18");

        // Invoke command.
        $response = parent::invoke(parent::COMMAND_COMPLETE_SERVICE_AUTH, $args);

        // Parse output and return result.
        $parsedOutput = $this->parseOutput($response->output[0]);

        // Convert response
        $result = new TrustServiceSessionResult($parsedOutput);
        return $result;
    }
}