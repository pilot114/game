# Emscripten has an official image, but it's super slow on Apple Silicon.
# Ubuntu is much faster:
FROM ubuntu:lunar as emscripten
SHELL ["/bin/bash", "-c"]
WORKDIR /root

# Install Emscripten and build dependencies {{{
RUN apt-get update && \
	apt-get --no-install-recommends -y install \
	build-essential automake autoconf libtool pkgconf flex make re2c gdb git \
	pv ca-certificates curl wget unzip cmake python3 bison

RUN ln -s /usr/bin/python3 /usr/bin/python
RUN git clone https://github.com/emscripten-core/emsdk.git && \
	/root/emsdk/emsdk install latest && \
	/root/emsdk/emsdk activate latest
# }}}

# Download PHP source code
RUN git clone https://github.com/php/php-src.git php-src \
	--branch PHP-8.0 --single-branch --depth 1
WORKDIR /root/php-src

# Build PHP
RUN ./buildconf --force
RUN source /root/emsdk/emsdk_env.sh && \
	emconfigure ./configure \
	--disable-all     \
	--enable-cli      \
	--enable-cgi      \
	--without-pcre-jit

RUN echo '#define ZEND_MM_ERROR 0' >> /root/php-src/main/php_config.h

RUN source /root/emsdk/emsdk_env.sh && \
	EMCC_CFLAGS='-sERROR_ON_UNDEFINED_SYMBOLS=0 -sEXPORT_NAME="PHPLoader"' emmake make -j8

# Confirm it all worked
RUN source /root/emsdk/emsdk_env.sh && \
	node /root/php-src/sapi/cli/php -v

# Copy the built files somewhere we can extact them from in build.sh
RUN mkdir /root/build
RUN cp /root/php-src/sapi/cli/php.wasm /root/build
RUN cp /root/php-src/sapi/cli/php /root/build/php.js
# Yay, that's it!
