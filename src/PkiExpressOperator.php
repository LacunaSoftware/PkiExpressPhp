<?php

namespace Lacuna\PkiExpress;

/**
 * Class PkiExpressOperator
 * @package Lacuna\PkiExpress
 *
 * @property $offline bool
 * @property $trustLacunaTestRoot bool
 */
abstract class PkiExpressOperator
{
    private $tempFiles;
    private $fileReferences;

    /** @var PkiExpressConfig */
    protected $config;
    /** @var VersionManager */
    protected $versionManager;
    protected $trustedRoots;

    protected $_offline = false;
    protected $_trustLacunaTestRoot = false;
    protected $_signaturePolicy;
    /** @var TimestampAuthority */
    protected $_timestampAuthority;
    protected $_culture;
    protected $_timeZone;


    /** @protected */
    const COMMAND_SIGN_CADES = "sign-cades";

    /** @protected */
    const COMMAND_SIGN_CADES2 = "sign-cades2";

    /** @protected */
    const COMMAND_SIGN_PADES = "sign-pades";

    /** @protected */
    const COMMAND_SIGN_XML = "sign-xml";

    /** @protected */
    const COMMAND_START_CADES = "start-cades";

    /** @protected */
    const COMMAND_START_CADES2 = "start-cades2";

    /** @protected */
    const COMMAND_START_PADES = "start-pades";

    /** @protected */
    const COMMAND_START_XML = "start-xml";

    /** @protected */
    const COMMAND_COMPLETE_SIG = "complete-sig";

    /** @protected */
    const COMMAND_COMPLETE_CADES = "complete-cades";

    /** @protected */
    const COMMAND_OPEN_PADES = "open-pades";

    /** @protected */
    const COMMAND_OPEN_CADES = "open-cades";

    /** @protected */
    const COMMAND_OPEN_XML = "open-xml";

    /** @protected */
    const COMMAND_OPEN_CERT = "open-cert";

    /** @protected */
    const COMMAND_EDIT_PDF = "edit-pdf";

    /** @protected */
    const COMMAND_START_AUTH = "start-auth";

    /** @protected */
    const COMMAND_COMPLETE_AUTH = "complete-auth";

    /** @protected */
    const COMMAND_GEN_KEY = "gen-key";

    /** @protected */
    const COMMAND_CREATE_PFX = "create-pfx";

    /** @protected */
    const COMMAND_STAMP_PDF = "stamp-pdf";

    /** @protected */
    const COMMAND_MERGE_CMS = "merge-cms";

    /** @protected */
    const COMMAND_READ_CERT = "read-cert";

    /** @protected */
    const COMMAND_CHECK_SERVICE = "check-service";

    /** @protected */
    const COMMAND_DISCOVER_SERVICES = "discover-services";

    /** @protected */
    const COMMAND_PASSWORD_AUTHORIZE = "pwd-auth";

    /** @protected */
    const COMMAND_COMPLETE_SERVICE_AUTH = "complete-service-auth";

    protected function __construct($config = null)
    {
        if (!isset($config)) {
            $config = new PkiExpressConfig();
        }
        $this->config = $config;
        $this->versionManager = new VersionManager();
        $this->trustedRoots = array();
        $this->tempFiles = array();
        $this->fileReferences = array();
    }

    protected function invokePlain($command, array $args = array())
    {
        $response = $this->invoke($command, $args, true);
        return $response->output;
    }

    protected function invoke($command, array $args = array(), $plainOutput = false)
    {
        // Add PKI Express invocation arguments
        $cmdArgs = array();
        foreach ($this->getPkiExpressInvocation() as $invocationArg) {
            $cmdArgs[] = $invocationArg;
        }

        // Add PKI Express command
        $cmdArgs[] = $command;

        // Add PKI Express arguments
        $cmdArgs = array_merge($cmdArgs, $args);

        // Add file references if added
        if (!empty($this->fileReferences)) {

            foreach ($this->fileReferences as $key => $value) {
                $cmdArgs[] = "--file-reference";
                $cmdArgs[] = "{$key}={$value}";
            }
        }

        // Add trusted roots if added
        if (!empty($this->trustedRoots)) {

            foreach ($this->trustedRoots as $root) {
                $cmdArgs[] = '--trust-root';
                $cmdArgs[] = $root;
            }
        }

        // Add trust Lacuna test root if set
        if ($this->_trustLacunaTestRoot) {
            $cmdArgs[] = '--trust-test';
        }

        // Add offline option if provided.
        if ($this->_offline) {
            $cmdArgs[] = '--offline';
            // This option can only be used on versions greater than 1.2 of the PKI Express.
            $this->versionManager->requireVersion("1.2");
        }

        // Add base64 output option.
        if (!$plainOutput) {
            $cmdArgs[] = '--base64';
            $this->versionManager->requireVersion("1.3");
        }

        // Add culture information.
        if ($this->_culture) {
            $cmdArgs[] = '--culture';
            $cmdArgs[] = $this->_culture;
            // This option can only be used on versions greater than 1.10 of the PKI Express.
            $this->versionManager->requireVersion('1.10');
        }

        // Add timezone option.
        if ($this->_timeZone) {
            $cmdArgs[] = '--timezone';
            $cmdArgs[] = $this->_timeZone;
            // This opration can only be used on version greater than 1.10 of the PKI Express.
            $this->versionManager->requireVersion('1.10');
        }

        // Verify the necessity of using the --min-version flag.
        if ($this->versionManager->requireMinVersionFlag()) {
            $cmdArgs[] = '--min-version';
            $cmdArgs[] = $this->versionManager->minVersion;
        }

        // Escape arguments
        $escapedArgs = array();
        foreach ($cmdArgs as $arg) {
            array_push($escapedArgs, $this->escapeCmdArg($arg));

        }

        // Perform the "dotnet" command
        $cmd = implode(' ', $escapedArgs);
        try {
            exec($cmd, $output, $return);
        } catch (\Exception $ex) {
            throw new InstallationNotFoundException('Could not find PKI Express\' installation', $ex);
        }
        if ($return != 0) {
            $implodedOutput = implode(PHP_EOL, $output);
            if ($return == 1 && version_compare($this->versionManager->minVersion, '1.0') > 0) {
                throw new \Exception($implodedOutput . PHP_EOL . ">>>>> TIP: This operation requires PKI Express {$this->versionManager->minVersion}, please check your PKI Express version.");
            }
            throw new \CommandException($return, $cmd, $implodedOutput);
        }

        return (object)array(
            'return' => $return,
            'output' => $output
        );
    }

    protected function getPkiExpressInvocation()
    {
        $os = null;

        // Identify OS
        if (PHP_OS == "Linux") {
            $os = "linux";
        } else {
            if (PHP_OS == "WIN32" || PHP_OS == "WINNT" || PHP_OS == "Windows") {
                $os = "win";
            } else {
                throw new \Exception("Unsupported OS: " . PHP_OS);
            }
        }

        // Verify if the PKI Express home is set on configuration
        $home = $this->config->getPkiExpressHome();
        if (!empty($home)) {

            if ($os == "linux") {
                if (!file_exists($home . '/pkie.dll')) {
                    throw new InstallationNotFoundException('The file pkie.dll could not be found on directory ' . $home);
                }
            } else {
                if (!file_exists($home . '\\pkie.exe')) {
                    throw new InstallationNotFoundException('The file pkie.exe could not be found on directory ' . $home);
                }
            }

        } else {

            if ($os == "win") {

                if (file_exists(getenv('ProgramW6432') . '\\Lacuna Software\\PKI Express\\pkie.exe')) {
                    $home = getenv('ProgramW6432') . '\\Lacuna Software\\PKI Express';
                } else {
                    if (file_exists(getenv('ProgramFiles(x86)') . '\\Lacuna Software\\PKI Express\\pkie.exe')) {
                        $home = getenv('ProgramFiles(x86)') . '\\Lacuna Software\\PKI Express';
                    } else {
                        if (file_exists(getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express\\pkie.exe')) {
                            $home = getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express';
                        } else {
                            if (file_exists(getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express (x86)\\pkie.exe')) {
                                $home = getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express (x86)';
                            }
                        }
                    }
                }

                if (empty($home)) {
                    throw new InstallationNotFoundException("Could not determine the installation folder of PKI Express. If you installed PKI Express on a custom folder, make sure you are specifying it on the PkiExpressConfig object.");
                }
            }
        }

        if ($os == 'linux') {

            if ($home != null) {
                return array('dotnet', $home . '/pkie.dll');
            } else {
                return array('pkie');
            }

        } else {
            return array($home . '\\pkie.exe');
        }
    }

    protected function createTempFile()
    {
        $tempFile = tempnam($this->config->getTempFolder(), 'pkie');
        array_push($this->tempFiles, $tempFile);
        return $tempFile;
    }

    protected function getTransferFileName()
    {
        $nBytes = 16;
        $transferFile = '';
        for ($i = 0; $i < $nBytes; $i++) {
            $transferFile .= dechex(rand(0, 255));
        }
        return $transferFile;
    }

    protected function parseOutput($dataBase64)
    {
        $content = base64_decode($dataBase64);
        $objArray = json_decode($content);
        return (object)$objArray;
    }

    public function __destruct()
    {
        foreach ($this->tempFiles as $tempFile) {
            try {
                unlink($tempFile);
            } catch (\Exception $e) {
                // TODO: log
            }
        }
    }

    private function escapeCmdArg($arg) {
        $firstLetter = $arg[0];
        $lastLetter = $arg[strlen($arg) - 1];

        // Verify the argument already has quotes. Remove temporarily these quotes.
        $content = null;
        if ($firstLetter == "\"" && $lastLetter == "\"") {
            $content = substr($arg, 1, strlen($arg) - 2);
        } else {
            $content = $arg;
        }

        // Perform the characters \" and \\ escaping on the argument's content.
        $escaped = str_replace("\\", "\\\\", $content);
        $escaped = str_replace("\"", "\\\"", $escaped);

        // Return quotes outside the argument's content when it was removed.
        return "\"{$escaped}\"";
    }

    /**
     * Adds an alias for a path to a file.
     *
     * @param $alias string An alias to the $path parameter.
     * @param $path string The path to a file.
     * @throws \Exception If the provided path is not found.
     */
    public function addFileReference($alias, $path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file path was not found");
        }

        $this->fileReferences[$alias] = $path;
    }

    /**
     * Sets the path for a trusted root certificate to PKI Express trust in.
     *
     * @param $path string The path for a trusted root certificate.
     * @throws \Exception If the provided path is not found.
     */
    public function addTrustedRoot($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided trusted root was not found");
        }

        array_push($this->trustedRoots, $path);
    }

    /**
     * Gets the option to execute the operation on "OFFLINE" mode.
     *
     * @return bool The option to execute on "OFFLINE" mode.
     */
    public function getOffline()
    {
        return $this->_offline;
    }

    /**
     * Sets the option to execute the operation on "OFFLINE" mode.
     *
     * @param $offline bool The option to execute on "OFFLINE" mode.
     */
    public function setOffline($offline)
    {
        $this->_offline = $offline;
    }

    /**
     * Gets the option to make PKI Express to trust on Lacuna's Root Test.
     *
     * @return bool The option to trust on Lacuna's Root Test.
     */
    public function getTrustLacunaTestRoot()
    {
        return $this->_trustLacunaTestRoot;
    }

    /**
     * Sets the signature policy for the signature.
     *
     * @param $policy string The signature policy fo the signature.
     */
    public function setSignaturePolicy($policy)
    {
        $this->_signaturePolicy = $policy;
    }

    /**
     * Gets the signature policy for the signature.
     *
     * @return string The signature policy fo the signature.
     */
    public function getSignaturePolicy()
    {
        return $this->_signaturePolicy;
    }

    /**
     * Sets the option to make PKI Express to trust on Lacuna's Root Test.
     *
     * @param $value bool The option to trust on Lacuna's Root Test.
     */
    public function setTrustLacunaTestRoot($value)
    {
        $this->_trustLacunaTestRoot = $value;
    }

    /**
     * Gets the timestamp authority.
     *
     * @return TimestampAuthority The timestamp authority.
     */
    public function getTimestampAuthority()
    {
        return $this->_timestampAuthority;
    }

    /**
     * Sets the timestamp authority.
     *
     * @param $value TimestampAuthority The timestamp authority.
     */
    public function setTimestampAuthority($value)
    {
        $this->_timestampAuthority = $value;
    }

    /**
     * Gets the culture used on date formatting.
     *
     * @return string The culture used on date formatting.
     */
    public function getCulture()
    {
        return $this->_culture;
    }

    /**
     * Sets the culture used on date formatting.
     *
     * @param $value string The culture used on date formatting.
     */
    public function setCulture($value)
    {
        $this->_culture = $value;
    }

    /**
     * Gets the timezone used on date formatting.
     *
     * @return string The timezone used on date formatting.
     */
    public function getTimeZone()
    {
        return $this->_timeZone;
    }

    /**
     * Sets the timezone used on date formatting.
     *
     * @param $value string The timezone used on date formatting.
     */
    public function setTimeZone($value)
    {
        $this->_timeZone = $value;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "trustLacunaTestRoot":
                return $this->getTrustLacunaTestRoot();
            case "offline":
                return $this->getOffline();
            case "signaturePolicy":
                return $this->getSignaturePolicy();
            case "timestampAuthority":
                return $this->getTimestampAuthority();
            case "culture":
                return $this->getCulture();
            case 'timeZone':
                return $this->getTimeZone();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $attr);
                return null;
        }
    }

    public function __set($prop, $value)
    {
        switch ($prop) {
            case "trustLacunaTestRoot":
                $this->setTrustLacunaTestRoot($value);
                break;
            case "offline":
                $this->setOffline($value);
                break;
            case "signaturePolicy":
                $this->setSignaturePolicy($value);
                break;
            case "timestampAuthority":
                $this->setTimestampAuthority($value);
                break;
            case "culture":
                $this->setCulture($value);
                break;
            case "timeZone":
                $this->setTimeZone($value);
                break;
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $prop);
        }
    }
}
