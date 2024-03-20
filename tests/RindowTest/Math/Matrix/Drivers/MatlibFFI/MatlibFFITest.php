<?php
namespace RindowTest\Math\Matrix\Drivers\MatlibFFI\MatlibFFITest;

use PHPUnit\Framework\TestCase;
use Rindow\Math\Matrix\Drivers\MatlibFFI\MatlibFFI;

use Rindow\Math\Buffer\FFI\Buffer;
use Rindow\OpenBLAS\FFI\Blas;
use Rindow\OpenBLAS\FFI\Lapack;
use Rindow\Matlib\FFI\Matlib as Math;

use Rindow\Math\Matrix\Drivers\MatlibPHP\PhpBuffer;
use Rindow\Math\Matrix\Drivers\MatlibPHP\PhpBlas;
use Rindow\Math\Matrix\Drivers\MatlibPHP\PhpLapack;
use Rindow\Math\Matrix\Drivers\MatlibPHP\PhpMath;

use Rindow\Math\Matrix\Drivers\Service;

use Interop\Polite\Math\Matrix\NDArray;
use Interop\Polite\Math\Matrix\OpenCL;

//use FFI\Env\Runtime as FFIEnvRuntime;
//use FFI\Env\Status as FFIEnvStatus;
//use FFI\Location\Locator as FFIEnvLocator;
use FFI;
use FFI\Exception as FFIException;
use InvalidArgumentException;

class MatlibFFITest extends TestCase
{
    public function newService()
    {
        return new MatlibFFI();
    }

    public function isAvailable(array $libs) : bool
    {
        $found = false;
        $code = '#define FFI_SCOPE "Rindow\\Matlib\\FFI"'."\n";
        foreach ($libs as $filename) {
            try {
                $ffi = FFI::cdef($code,$filename);
            } catch(FFIException $e) {
                continue;
            }
            $found = true;
            break;
        }
        return $found;
    }

    public function testName()
    {
        $service = $this->newService();
        $this->assertEquals('matlib_ffi',$service->name());
    }

    public function testServiceLevel()
    {
        $service = $this->newService();
        if($this->isAvailable(['libopenblas.dll','libopenblas.so','libopenblas.so.0'])&&
            $this->isAvailable(['rindowmatlib.dll','librindowmatlib.so'])&&
            $this->isAvailable(['OpenCL.dll','libOpenCL.so.1'])&&
            $this->isAvailable(['clblast.dll','libclblast.so'])&&
            class_exists('Rindow\Matlib\FFI\MatlibFactory')) {
            $this->assertEquals(Service::LV_ACCELERATED,$service->serviceLevel());
        } elseif(
            $this->isAvailable(['libopenblas.dll','libopenblas.so','libopenblas.so.0'])&&
            $this->isAvailable(['rindowmatlib.dll','librindowmatlib.so']) &&
            class_exists('Rindow\Matlib\FFI\MatlibFactory') &&
            (!$this->isAvailable(['OpenCL.dll','libOpenCL.so.1'])||
             !$this->isAvailable(['clblast.dll','libclblast.so'])||
             !class_exists('Rindow\OpenCL\FFI\OpenCLFactory'))) {
            $this->assertEquals(Service::LV_ADVANCED,$service->serviceLevel());
        } elseif(
            !$this->isAvailable(['libopenblas.dll','libopenblas.so','libopenblas.so.0'])||
            !$this->isAvailable(['rindowmatlib.dll','librindowmatlib.so'])||
            !class_exists('Rindow\Matlib\FFI\MatlibFactory')) {
            $this->assertEquals(Service::LV_BASIC,$service->serviceLevel());
        } else {
            $this->assertTrue(false);
        }
    }

    public function testBlas()
    {
        $service = $this->newService();
        if($service->serviceLevel()>=Service::LV_ADVANCED) {
            $this->assertInstanceOf(Blas::class,$service->blas());
        } else {
            $this->assertInstanceOf(PhpBlas::class,$service->blas());
        }
        $this->assertInstanceOf(PhpBlas::class,$service->blas(Service::LV_BASIC));
    }

    public function testLapack()
    {
        $service = $this->newService();
        if($service->serviceLevel()>=Service::LV_ADVANCED) {
            $this->assertInstanceOf(Lapack::class,$service->lapack());
        } else {
            $this->assertInstanceOf(PhpLapack::class,$service->lapack());
        }
        $this->assertInstanceOf(PhpLapack::class,$service->lapack(Service::LV_BASIC));
    }

    public function testMath()
    {
        $service = $this->newService();
        if($service->serviceLevel()>=Service::LV_ADVANCED) {
            $this->assertInstanceOf(Math::class,$service->math());
        } else {
            $this->assertInstanceOf(PhpMath::class,$service->math());
        }
        $this->assertInstanceOf(PhpMath::class,$service->math(Service::LV_BASIC));
    }

    public function testBuffer()
    {
        $service = $this->newService();
        $size = 2;
        $dtype = NDArray::float32;
        if($service->serviceLevel()>=Service::LV_ADVANCED) {
            $this->assertInstanceOf(Buffer::class,$service->buffer()->Buffer($size,$dtype));
        } else {
            $this->assertInstanceOf(PhpBuffer::class,$service->buffer()->Buffer($size,$dtype));
        }
        $this->assertInstanceOf(PhpBuffer::class,$service->buffer(Service::LV_BASIC)->Buffer($size,$dtype));
    }

    public function testCreateQueuebyDeviceType()
    {
        $service = $this->newService();
        if($service->serviceLevel()<Service::LV_ACCELERATED) {
            $this->markTestSkipped("The service is not Accelerated.");
            return;
        }
        try {
            $queue = $service->createQueue(['deviceType'=>OpenCL::CL_DEVICE_TYPE_GPU]);
        } catch(InvalidArgumentException $e) {
            $queue = $service->createQueue(['deviceType'=>OpenCL::CL_DEVICE_TYPE_CPU]);
        }
        $this->assertInstanceOf(\Rindow\OpenCL\FFI\CommandQueue::class,$queue);
        $this->assertInstanceOf(\Rindow\CLBlast\FFI\Blas::class,$service->blasCL($queue));
        $this->assertInstanceOf(\Rindow\Math\Matrix\Drivers\MatlibCL\OpenCLMath::class,$service->mathCL($queue));
        $this->assertInstanceOf(\Rindow\CLBlast\FFI\Math::class,$service->mathCLBlast($queue));
    }

    public function testCreateQueuebyDeviceId()
    {
        $service = $this->newService();
        if($service->serviceLevel()<Service::LV_ACCELERATED) {
            $this->markTestSkipped("The service is not Accelerated.");
            return;
        }
        $queue = $service->createQueue(['device'=>"0,0"]);
        $this->assertInstanceOf(\Rindow\OpenCL\FFI\CommandQueue::class,$queue);
        $this->assertInstanceOf(\Rindow\CLBlast\FFI\Blas::class,$service->blasCL($queue));
        $this->assertInstanceOf(\Rindow\Math\Matrix\Drivers\MatlibCL\OpenCLMath::class,$service->mathCL($queue));
        $this->assertInstanceOf(\Rindow\CLBlast\FFI\Math::class,$service->mathCLBlast($queue));
    }
}
