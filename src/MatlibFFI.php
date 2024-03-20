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
    protected $name = 'matlib_ffi';

    public function __construct(
        object $bufferFactory=null,
        object $mathFactory=null,
        object $openblasFactory=null,
        object $openclFactory=null,
        object $clblastFactory=null,
        object $blasCLFactory=null,
        object $mathCLFactory=null,
        object $bufferCLFactory=null,
        )
    {
        if($bufferFactory===null && class_exists(BufferFactory::class)) {
            $bufferFactory = new BufferFactory();
        }
        if($openblasFactory===null && class_exists(OpenBLASFactory::class)) {
            $openblasFactory = new OpenBLASFactory();
        }
        if($mathFactory===null && class_exists(MatlibFactory::class)) {
            $mathFactory = new MatlibFactory();
        }
        if($openclFactory===null && class_exists(OpenCLFactory::class)) {
            $openclFactory = new OpenCLFactory();
        }
        $bufferCLFactory = $bufferCLFactory ?? $openclFactory;
        if($clblastFactory===null && class_exists(CLBlastFactory::class)) {
            $clblastFactory = new CLBlastFactory();
        }
        $blasCLFactory = $blasCLFactory ?? $clblastFactory;
        if($mathCLFactory===null && class_exists(MatlibCLFactory::class)) {
            $mathCLFactory = new MatlibCLFactory();
        }

        parent::__construct(
            bufferFactory:$bufferFactory,
            openblasFactory:$openblasFactory,
            mathFactory:$mathFactory,
            openclFactory:$openclFactory,
            clblastFactory:$clblastFactory,
            blasCLFactory:$blasCLFactory,
            mathCLFactory:$mathCLFactory,
            bufferCLFactory:$bufferCLFactory,
        );
    }
}