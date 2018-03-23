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


    /** @protected */
    const COMMAND_SIGN_CADES = "sign-cades";

    /** @protected */
    const COMMAND_SIGN_PADES = "sign-pades";

    /** @protected */
    const COMMAND_SIGN_XML = "sign-xml";

    /** @protected */
    const COMMAND_START_CADES = "start-cades";

    /** @protected */
    const COMMAND_START_PADES = "start-pades";

    /** @protected */
    const COMMAND_START_XML = "start-xml";

    /** @protected */
    const COMMAND_COMPLETE_SIG = "complete-sig";

    /** @protected */
    const COMMAND_OPEN_PADES = "open-pades";

    /** @protected */
    const COMMAND_OPEN_CADES = "open-cades";

    /** @protected */
    const COMMAND_EDIT_PDF = "edit-pdf";


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
        if (empty($this->trustedRoots)) {

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

        // Verify the necessity of using the --min-version flag.
        if ($this->versionManager->requireMinVersionFlag()) {
            $cmdArgs[] = '--min-version';
            $cmdArgs[] = $this->versionManager->minVersion;
        }

        // Escape arguments
        $escapedArgs = array();
        foreach ($cmdArgs as $arg) {
            array_push($escapedArgs, escapeshellarg($arg));
        }

        // Perform the "dotnet" command
        $cmd = implode(' ', $escapedArgs);
        exec($cmd, $output, $return);
        if ($return != 0) {
            $implodedOutput = implode(PHP_EOL, $output);
            if ($return == 1 && version_compare($this->versionManager->minVersion, '1.0') > 0) {
                throw new \Exception($implodedOutput . PHP_EOL . ">>>>> TIP: This operation requires PKI Express {$this->versionManager->minVersion}, please check your PKI Express version.");
            }
            throw new \Exception($implodedOutput);
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
                    throw new \Exception('The file pkie.dll could not be found on directory ' . $home);
                }
            } else {
                if (!file_exists($home . '\\pkie.exe')) {
                    throw new \Exception('The file pkie.exe could not be found on directory ' . $home);
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
                    throw new \Exception("Could not determine the installation folder of PKI Express. If you installed PKI Express on a custom folder, make sure you are specifying it on the PkiExpressConfig object.");
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

    public function addFileReference($alias, $path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided file path was not found");
        }

        $this->fileReferences[$alias] = $path;
    }

    public function addTrustedRoot($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("The provided trusted root was not found");
        }

        array_push($this->trustedRoots, $path);
    }

    public function getOffline()
    {
        return $this->_offline;
    }

    public function setOffline($offline)
    {
        $this->_offline = $offline;
    }

    public function getTrustLacunaTestRoot()
    {
        return $this->_trustLacunaTestRoot;
    }

    public function setTrustLacunaTestRoot($value)
    {
        $this->_trustLacunaTestRoot = $value;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case "trustLacunaTestRoot":
                return $this->getTrustLacunaTestRoot();
            case "offline":
                return $this->getOffline();
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
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $prop);
        }
    }
}