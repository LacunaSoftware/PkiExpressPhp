<?php

namespace Lacuna\PkiExpress;


abstract class PkiExpressOperator
{
    private $tempFiles;
    private $fileReferences;

    /** @var PkiExpressConfig */
    protected $config;
    protected $trustedRoots;

    public $trustLacunaTestRoot;


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


    protected function __construct($config)
    {
        $this->config = $config;
        $this->trustedRoots = array();
        $this->tempFiles = array();
        $this->fileReferences = array();
        $this->trustLacunaTestRoot = true;
    }

    protected function invoke($command, array $args = array())
    {
        if (empty($this->config->getLicensePath())) {
            throw new \Exception("The license's path was not set");
        }

        // Add PKI Express invocation arguments
        $cmdArgs = array();
        foreach ($this->getPkiExpressInvocation() as $invocationArg) {
            $cmdArgs[] = $invocationArg;
        }

        // Add PKI Express command
        $cmdArgs[] = $command;

        // Add PKI Express arguments
        $cmdArgs = array_merge($cmdArgs, $args);

        // Add the license path
        $cmdArgs[] = "-lf";
        $cmdArgs[] = $this->config->getLicensePath();

        // Add file references if added
        if (!empty($this->fileReferences)) {

            foreach ($this->fileReferences as $key => $value) {
                $cmdArgs[] = "-fr";
                $cmdArgs[] = "{$key}={$value}";
            }
        }

        // Add trusted roots if added
        if (empty($this->trustedRoots)) {

            foreach ($this->trustedRoots as $root) {
                $cmdArgs[] = '-tr';
                $cmdArgs[] = $root;
            }
        }

        // Add trust Lacuna test root if set
        if ($this->trustLacunaTestRoot) {
            $cmdArgs[] = '-tt';
        }

        // Escape arguments
        $escapedArgs = array();
        foreach ($cmdArgs as $arg) {
            array_push($escapedArgs, escapeshellarg($arg));
        }

        // Perform the "dotnet" command
        $cmd = implode(' ', $escapedArgs);
        exec($cmd, $output, $return);
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

            if ($os == "linux") {

                if (file_exists('/usr/local/share/pkie/pkie.dll')) {

                    $home = '/usr/local/share/pkie';

                } else {
                    if (file_exists('/usr/share/pkie/pkie.dll')) {

                        $home = '/usr/share/pkie';

                    }
                }

            } else {

                if (file_exists(getenv('ProgramW6432') . '\\Lacuna Software\\PKI Express\\pkie.exe')) {
                    $home = getenv('ProgramW6432') . '\\Lacuna Software\\PKI Express';
                } else if (file_exists(getenv('ProgramFiles(x86)') . '\\Lacuna Software\\PKI Express\\pkie.exe')) {
                    $home = getenv('ProgramFiles(x86)') . '\\Lacuna Software\\PKI Express';
                } else if (file_exists(getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express\\pkie.exe')) {
                    $home = getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express';
                } else if (file_exists(getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express (x86)\\pkie.exe')) {
                    $home = getenv('LOCALAPPDATA') . '\\Lacuna Software\\PKI Express (x86)';
                }

            }

            if (empty($home)) {
                throw new \Exception("Could not determine the installation folder of PKI Express. If you installed PKI Express on a custom folder, make sure you are specifying it on the PkiExpressConfig object.");
            }

        }

        if ($os == 'linux') {
            return array('dotnet', $home . '/pkie.dll');
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
}