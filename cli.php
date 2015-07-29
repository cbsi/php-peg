<?php

require_once 'vendor/autoload.php';


use hafriedlander\Peg\Compiler;


if ($argc < 2 || $argc > 3) {

  print <<<EOS
Parser Compiler: A compiler for PEG parsers in PHP
(C) 2009 SilverStripe. See COPYING for redistribution rights.

Usage: {$argv[0]} infile [ outfile ]

EOS;

} else {

  $fname = ($argv[1] == '-' ? 'php://stdin' : $argv[1]);
  $peg = file_get_contents($fname);
  $code = Compiler::compile($peg);

  if (!empty($argv[2]) && $argv[2] !== '-') {
    file_put_contents($argv[2], $code);
  } else {
    print $code;
  }

}
