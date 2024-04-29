<?php
namespace Rindow\Math\Matrix\Drivers\MatlibFFI;

use Rindow\Math\Buffer\FFI\BufferFactory;
use Rindow\OpenBLAS\FFI\OpenBLASFactory;
use Rindow\Matlib\FFI\MatlibFactory;
use Rindow\OpenCL\FFI\OpenCLFactory;
use Rindow\CLBlast\FFI\CLBlastFactory;
use Rindow\Math\Matrix\Drivers\MatlibCL\MatlibCLFactory;
use Rindow\Math\Matrix\Drivers\AbstractMatlibService;

class MatlibFFI extends AbstractMatlibService
{
    protected string $name = 'matlib_ffi';

    protected function injectDefaultFactories() : void
    {
        if($this->bufferFactory===null) {
            if(class_exists(BufferFactory::class)) {
                $this->bufferFactory = new BufferFactory();
            } else {
                $this->logging(0,BufferFactory::class.' ** not found.');
            }
        }
        if($this->openblasFactory===null) {
            if(class_exists(OpenBLASFactory::class)) {
                $this->openblasFactory = new OpenBLASFactory();
            } else {
                $this->logging(0,OpenBLASFactory::class.' ** not found **.');
            }
        }
        if($this->mathFactory===null) {
            if(class_exists(MatlibFactory::class)) {
                $this->mathFactory = new MatlibFactory();
            } else {
                $this->logging(0,MatlibFactory::class.' ** not found **.');
            }
        }
        if($this->openclFactory===null) {
            if(class_exists(OpenCLFactory::class)) {
                $this->openclFactory = new OpenCLFactory();
            } else {
                $this->logging(0,OpenCLFactory::class.' ** not found **.');
            } 
        }
        $this->bufferCLFactory ??= $this->openclFactory;
        if($this->clblastFactory===null) { 
            if(class_exists(CLBlastFactory::class)) {
               $this->clblastFactory = new CLBlastFactory();
            } else {
                $this->logging(0,CLBlastFactory::class.' ** not found **.');
            }
        }
        $this->blasCLFactory ??= $this->clblastFactory;
        if($this->mathCLFactory===null) {
            if(class_exists(MatlibCLFactory::class)) {
                $this->mathCLFactory = new MatlibCLFactory();
            } else {
                $this->logging(0,MatlibCLFactory::class.' ** not found **.');
            }
        }
    }
}