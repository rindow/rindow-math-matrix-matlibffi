Rindow Math Matrix's Drivers for Matlib with PHP extensions
===========================================================
Status:
[![Build Status](https://github.com/rindow/rindow-math-matrix-matlibffi/workflows/tests/badge.svg)](https://github.com/rindow/rindow-math-matrix-matlibffi/actions)
[![Downloads](https://img.shields.io/packagist/dt/rindow/rindow-math-matrix-matlibffi)](https://packagist.org/packages/rindow/rindow-math-matrix-matlibffi)
[![Latest Stable Version](https://img.shields.io/packagist/v/rindow/rindow-math-matrix-matlibffi)](https://packagist.org/packages/rindow/rindow-math-matrix-matlibffi)
[![License](https://img.shields.io/packagist/l/rindow/rindow-math-matrix-matlibffi)](https://packagist.org/packages/rindow/rindow-math-matrix-matlibffi)

This package is matlib drivers for Rindow-math-matrix. These drivers act as adapters to drive PHP FFI. Each PHP C Libraries requires a separate download and installation of a binary file appropriate for your environment's PHP version and OS version.

Rindow Math Matrix is the fundamental package for scientific matrix operation

- A powerful N-dimensional array object
- Sophisticated (broadcasting) functions
- Tools for integrating C/C++ through the FFI (or PHP extensions)
- Useful linear algebra and random number capabilities

Please see the documents on [Rindow mathematics projects](https://rindow.github.io/mathematics/) web pages.

Rindow Math Matrix's repository is [here](https://github.com/rindow/rindow-math-matrix/).

Requirements
============

- PHP 8.1 or PHP8.2 or PHP8.3
- Rindow Math Matrix v2.0.0
- Rindow Matlib 1.0.0 or later
- OpenBLAS 0.3.20 or later
- OpenCL 1.1 or later
- CLBlast 1.5.2 or later
- Windows 10/11 or Ubuntu 20.04 LTS or later

### Download pre-build binaries from each projects

You can perform very fast N-dimensional array operations in conjunction.
Download the pre-build binary files from each project's release page.

- Pre-build binaries
  - [Rindow Matlib](https://github.com/rindow/rindow-matlib/releases)
  - [OpenBLAS](https://github.com/xianyi/OpenBLAS/releases)
  - [CLBlast](https://github.com/CNugteren/CLBlast/releases)

### Setup for Windows

Download the binary file, unzip it, and copy it to the execution directory.

- rindow-matlib-X.X.X-win64.zip
- OpenBLAS-X.X.X-x64.zip
- CLBlast-X.X.X-windows-x64.zip


Add FFI extension to php.ini

```shell
C:\TMP> cd \path\to\php\directory
C:\PHP> notepad php.ini

extension=ffi
```

```shell
C:\TMP> PATH %PATH%;\path\to\binary\directories\bin
C:\TMP> cd \your\progject\directory
C:\PRJ> composer require rindow/rindow-math-matrix
C:\PRJ> composer require rindow/rindow-math-matrix-matlibffi
```

The OpenCL 1.2 environment is already set up if you are using the Windows standard driver.


### Setup for Ubuntu

Install each library using the apt command.

Make sure FFI extension is enabled.
```shell
$ php -m | grep FFI
FFI
```

Install the fast matrix calculation library.
And then set the rindow-matlib to serial mode for use with PHP.
```shell
$ mkdir -p /your/project/directory
$ cd /your/project/directory
$ sudo apt install libopenblas-base liblapacke
$ wget https://github.com/rindow/rindow-matlib/releases/download/X.X.X/rindow-matlib_X.X.X_amd64.deb
$ sudo apt install ./rindow-matlib_X.X.X_amd64.deb
$ sudo update-alternatives --config librindowmatlib.so
There are 2 choices for the alternative librindowmatlib.so (providing /usr/lib/librindowmatlib.so).

  Selection    Path                                             Priority   Status
------------------------------------------------------------
* 0            /usr/lib/rindowmatlib-openmp/librindowmatlib.so   95        auto mode
  1            /usr/lib/rindowmatlib-openmp/librindowmatlib.so   95        manual mode
  2            /usr/lib/rindowmatlib-serial/librindowmatlib.so   90        manual mode

Press <enter> to keep the current choice[*], or type selection number: 2
```

If you want to use GPU, install the OpenCL environment.
In addition, there are the following drivers.

- mesa-opencl-icd
- beignet-opencl-icd
- intel-opencl-icd
- nvidia-opencl-icd-xxx
- pocl-opencl-icd

```shell
$ sudo apt install clinfo
$ sudo apt install mesa-opencl-icd
$ sudo mkdir -p /usr/local/usr/lib
$ sudo ln -s /usr/lib/clc /usr/local/usr/lib/clc
```

And then, Install the fast matrix calculation library for OpenCL.
Please download the CLBlast installation script from the rindow-clblast-ffi release page.

```shell
$ wget https://github.com/rindow/rindow-clblast-ffi/releases/download/X.X.X/clblast-packdeb.zip
$ unzip clblast-packdeb.zip
$ sh clblast-packdeb.sh
$ sudo apt install ./clblast_X.X.X-1+ubuntuXX.XX_amd64.deb
```

Install the rindow-math-matrix on your project directory.
```shell
$ composer require rindow/rindow-math-matrix
$ composer require rindow/rindow-math-matrix-matlibffi
```

### Check driver status
You can check the driver settings by running the sample below.
```php
<?php
// status.php
include_once __DIR__.'/vendor/autoload.php';
use Rindow\Math\Matrix\MatrixOperator;

$mo = new MatrixOperator();

echo $mo->service()->info();
```

```shell
$ php status.php
Service Level: Accelerated
Buffer Factory: Rindow\Math\Buffer\FFI\BufferFactory
BLAS Driver: Rindow\OpenBLAS\FFI\Blas
LAPACK Driver: Rindow\OpenBLAS\FFI\Lapack
Math Driver: Rindow\Matlib\FFI\Matlib
```

### Acceleration with GPU

You can use GPU acceleration on OpenCL.

*Note:*

This OpenCL support extension works better in your environment and helps speed up your laptop environment without n-NVIDIA.

Tested on Ivy-bridge and AMD's Bobcat architecture APU.

In the Windows environment, Integrated GPU usage was more effective than CPU, and it worked comfortably.

However, OLD AMD APU on Linux, libclc used in linux standard mesa-opencl-icd is very buggy and slow.
If you have testable hardware, please test using the proprietary driver.

On the other hand, I tested with Ivy-bridge of Intel CPU and Integrated GPU.

Windows 10 standard OpenCL driver worked fine, but it was very slow and occasionally crashed.

And it worked fine and fast in Ubuntu 20.04 + beignet-opencl-icd environment.
