FROM public.ecr.aws/lambda/provided:al2

ENV LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/lib

ARG PHP_VERSION=8.2.8
ARG PYTHON_VERSION=3.9.17
SHELL ["/bin/bash", "-c"]

RUN yum clean all && \
    yum install -y autoconf bison bzip2-devel gcc gcc-c++ git gzip libcurl-devel libffi-devel libxml2-devel make \
                   oniguruma-devel openssl-devel re2c sqlite-devel tar unzip zip && \
    yum clean all && \
    rm -rf /var/cache/yum

# PHP & Composer
RUN curl -sL https://github.com/php/php-src/archive/php-${PHP_VERSION}.tar.gz | tar -xz && \
    cd php-src-php-${PHP_VERSION} && \
    ./buildconf --force && \
    ./configure --prefix=/usr --with-openssl --with-curl --with-zlib --without-pear --enable-bcmath --with-bz2 --enable-mbstring && \
    make -j $(nproc) && \
    make install && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
    cd .. && rm -rf php-src-php-${PHP_VERSION}

# Python
RUN curl -sL https://www.python.org/ftp/python/${PYTHON_VERSION}/Python-${PYTHON_VERSION}.tgz | tar -xz && \
    cd Python-${PYTHON_VERSION} && \
    ./configure --prefix=/usr --enable-optimizations --with-system-ffi --with-computed-gotos --enable-loadable-sqlite-extensions --enable-shared --disable-test-modules && \
    make -j $(nproc) && \
    make altinstall && \
    cd .. && rm -rf Python-${PYTHON_VERSION}

COPY . /var/task
WORKDIR /var/task

# Handle dependencies, build stuff
RUN composer install --no-dev --no-scripts && \
    APP_ENV=prod php bin/console cache:warmup && \
    cd python/source && \
    python3.9 -m venv venv && \
    source venv/bin/activate && \
    pip install -r requirements.txt && \
    pyinstaller -F summarizer.py && \
    mv dist/summarizer ../ && \
    rm -rf dist build && \
    cd .. && \
    python -m nltk.downloader punkt -d . && \
    composer global clear-cache

# Lambda
RUN cp lambda/bootstrap.php /var/runtime/bootstrap && \
    cd /opt && \
    composer require guzzlehttp/guzzle && \
    chmod -R +rx /var/task

CMD [ "bin/console" ]
