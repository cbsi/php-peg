<?php
namespace hafriendlander\Peg\Bundle\Command;

use hafriedlander\Peg\Compiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for compiling a PEG PHP script.
 */
class CompileCommand extends Command
{
    /**
     * Re-implements `Command::configure()`.
     */
    protected function configure()
    {
        $this->setName('peg:compile')->setDescription('Compiles a PEG PHP grammar into a PHP file');
        $this->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'Input file');
        $this->addOption('output', 'i', InputOption::VALUE_OPTINAL, 'Output file ("-" for standard output)', null);
    }

    /**
     * Executes the compiler.
     *
     * @param InputInterface  $input  Input object.
     * @param OutputInterface $output Output object.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = $input->getOption('input');
        $outputFile = $input->getOption('output');
        $isToStandardOutput = $outputFile === '-';

        if (!$outputFile) {
            $outputFile = strchr($inputFile, '.', true).'.php';
        }

        if (!$isToStandardOutput && strpos($outputFile, DIRECTORY_SEPARATOR) === false) {
            $outputFile = '.'.DIRECTORY_SEPARATOR.$outputFile;
        }

        $peg = file_get_contents($input);
        $code = Compiler::compile($peg);

        if ($isToStandardOutput) {
            fputs(STDOUT, $code);
            return;
        }

        $ret = @file_put_contents($outputFile, $code, LOCK_EX);

        if ($ret === false) {
            $output->writeln(sprintf('<error>Unable to write to "%s". Verify permissions.</error>', $outputFile));
        }

        return $ret;
    }
}
