FROM ghcr.io/rikudousage/lemmy-auto-tldr-bot:base

COPY . /var/task
WORKDIR /var/task

# Handle dependencies, build stuff
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-scripts && \
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
    COMPOSER_ALLOW_SUPERUSER=1 composer global clear-cache

# Lambda
RUN cp lambda/bootstrap.php /var/runtime/bootstrap && \
    cd /opt && \
    composer require guzzlehttp/guzzle && \
    chmod -R +rx /var/task

CMD [ "bin/console" ]
