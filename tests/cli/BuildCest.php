<?php
// @group core

use Codeception\Scenario;

class BuildCest
{
    /** @var string */
    private $originalCliHelperContents;

    public function _before()
    {
        $this->originalCliHelperContents = file_get_contents(codecept_root_dir('tests/support/CliHelper.php'));
    }

    public function _after()
    {
        file_put_contents(codecept_root_dir('tests/support/CliHelper.php'), $this->originalCliHelperContents);
    }

    public function buildsActionsForAClass(CliGuy $I)
    {
        $I->wantToTest('build command');
        $I->runShellCommand('php codecept build');
        $I->seeInShellOutput('generated successfully');
        $I->seeInSupportDir('CodeGuy.php');
        $I->seeInSupportDir('CliGuy.php');
        $I->seeInThisFile('class CliGuy extends \Codeception\Actor');
        $I->seeInThisFile('use _generated\CliGuyActions');
        $I->seeFileFound('CliGuyActions.php', 'tests/support/_generated');
        $I->seeInThisFile('seeFileFound(');
        if (PHP_VERSION_ID < 70400) {
            $I->seeInThisFile('public function assertEquals($expected, $actual, $message = "") {');
        } else {
            $I->seeInThisFile('public function assertEquals($expected, $actual, string $message = "") {');
        }
    }

    public function usesTypehintsWherePossible(CliGuy $I, Scenario $scenario)
    {
        if (PHP_MAJOR_VERSION < 7) {
            $scenario->skip('Does not work in PHP < 7');
        }

        $I->wantToTest('generate typehints with generated actions');

        $cliHelperContents = file_get_contents(codecept_root_dir('tests/support/CliHelper.php'));
        $cliHelperContents = str_replace('public function grabFromOutput($regex)', 'public function grabFromOutput(string $regex): int', $cliHelperContents);
        file_put_contents(codecept_root_dir('tests/support/CliHelper.php'), $cliHelperContents);

        $I->runShellCommand('php codecept build');
        $I->seeInShellOutput('generated successfully');
        $I->seeInSupportDir('CliGuy.php');
        $I->seeInThisFile('class CliGuy extends \Codeception\Actor');
        $I->seeInThisFile('use _generated\CliGuyActions');
        $I->seeFileFound('CliGuyActions.php', 'tests/support/_generated');
        $I->seeInThisFile('public function grabFromOutput(string $regex): int');
    }

    public function generatedUnionReturnTypeOnPhp8(CliGuy $I, Scenario $scenario)
    {
        if (PHP_MAJOR_VERSION < 8) {
            $scenario->skip('Does not work in PHP < 8');
        }

        $I->wantToTest('generate action with union return type');

        $cliHelperContents = file_get_contents(codecept_root_dir('tests/support/CliHelper.php'));
        $cliHelperContents = str_replace('public function grabFromOutput($regex)', 'public function grabFromOutput(array|string $param): int|string', $cliHelperContents);
        file_put_contents(codecept_root_dir('tests/support/CliHelper.php'), $cliHelperContents);

        $I->runShellCommand('php codecept build');
        $I->seeInShellOutput('generated successfully');
        $I->seeInSupportDir('CliGuy.php');
        $I->seeInThisFile('class CliGuy extends \Codeception\Actor');
        $I->seeInThisFile('use _generated\CliGuyActions');
        $I->seeFileFound('CliGuyActions.php', 'tests/support/_generated');
        $I->seeInThisFile('public function grabFromOutput(array|string $param): string|int');
    }
    
    public function noReturnForVoidType(CliGuy $I, Scenario $scenario)
    {
        if (PHP_VERSION_ID < 70100) {
            $scenario->skip('Does not work in PHP < 7.1');
        }

        $I->wantToTest('no return keyword generated for void typehint');

        $cliHelperContents = file_get_contents(codecept_root_dir('tests/support/CliHelper.php'));
        $cliHelperContents = str_replace('public function seeDirFound($dir)', 'public function seeDirFound($dir): void', $cliHelperContents);
        file_put_contents(codecept_root_dir('tests/support/CliHelper.php'), $cliHelperContents);

        $I->runShellCommand('php codecept build');
        $I->seeInShellOutput('generated successfully');
        $I->seeInSupportDir('CliGuy.php');
        $I->seeInThisFile('class CliGuy extends \Codeception\Actor');
        $I->seeInThisFile('use _generated\CliGuyActions');
        $I->seeFileFound('CliGuyActions.php', 'tests/support/_generated');
        $I->seeInThisFile('public function seeDirFound($dir): void');
    }

    public function generateNullableParametersOnPHP70(CliGuy $I, Scenario $scenario)
    {
        if (PHP_VERSION_ID < 70000 || PHP_VERSION_ID >= 70100) {
            $scenario->skip('For PHP 7.0 only');
        }

        $cliHelperContents = file_get_contents(codecept_root_dir('tests/support/CliHelper.php'));
        $cliHelperContents = str_replace('public function seeDirFound($dir)', 'public function seeDirFound(\Directory $dir = null)', $cliHelperContents);
        file_put_contents(codecept_root_dir('tests/support/CliHelper.php'), $cliHelperContents);

        $I->runShellCommand('php codecept build');
        $I->seeInShellOutput('generated successfully');
        $I->seeInSupportDir('CliGuy.php');
        $I->seeInThisFile('class CliGuy extends \Codeception\Actor');
        $I->seeInThisFile('use _generated\CliGuyActions');
        $I->seeFileFound('CliGuyActions.php', 'tests/support/_generated');
        $I->seeInThisFile('public function seeDirFound(\Directory $dir = NULL)');
    }

    public function generateNullableParametersOnPHP71AndLater(CliGuy $I, Scenario $scenario)
    {
        if (PHP_VERSION_ID < 70100) {
            $scenario->skip('Does not work in PHP < 7.1');
        }
        $cliHelperContents = file_get_contents(codecept_root_dir('tests/support/CliHelper.php'));
        $cliHelperContents = str_replace('public function seeDirFound($dir)', 'public function seeDirFound(\Directory $dir = null): ?bool', $cliHelperContents);
        file_put_contents(codecept_root_dir('tests/support/CliHelper.php'), $cliHelperContents);

        $I->runShellCommand('php codecept build');
        $I->seeInShellOutput('generated successfully');
        $I->seeInSupportDir('CliGuy.php');
        $I->seeInThisFile('class CliGuy extends \Codeception\Actor');
        $I->seeInThisFile('use _generated\CliGuyActions');
        $I->seeFileFound('CliGuyActions.php', 'tests/support/_generated');
        $I->seeInThisFile('public function seeDirFound(?\Directory $dir = NULL): ?bool');
    }

    public function generateMixedParametersOnPHP80AndLater(CliGuy $I, Scenario $scenario)
    {
        if (PHP_VERSION_ID < 80000) {
            $scenario->skip('Does not work in PHP < 8.0');
        }
        $cliHelperContents = file_get_contents(codecept_root_dir('tests/support/CliHelper.php'));
        $cliHelperContents = str_replace('public function seeDirFound($dir)', 'public function seeDirFound(mixed $dir = null): mixed', $cliHelperContents);
        file_put_contents(codecept_root_dir('tests/support/CliHelper.php'), $cliHelperContents);

        $I->runShellCommand('php codecept build');
        $I->seeInShellOutput('generated successfully');
        $I->seeInSupportDir('CliGuy.php');
        $I->seeInThisFile('class CliGuy extends \Codeception\Actor');
        $I->seeInThisFile('use _generated\CliGuyActions');
        $I->seeFileFound('CliGuyActions.php', 'tests/support/_generated');
        $I->seeInThisFile('public function seeDirFound(mixed $dir = NULL): mixed');
    }
}
