#!/bin/bash

docker build . --tag=php-wasm
mkdir -p build
docker run --name php-wasm-tmp --rm -v `pwd`/build:/output php-wasm sh -c 'cp /root/build/* /output'
cd build
ls
echo '<!DOCTYPE html><script>window.PHPLoader = {arguments: ["-v"]}</script><script type="text/javascript" src="php.js"></script>Check dev tools' > index.html

echo 'Starting server on http://localhost:8000'
php -S localhost:8000
