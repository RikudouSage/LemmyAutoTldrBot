# Lemmy AutoTL;DR bot

This bot reads content from a supported site and reports back the summary.

## Interesting parts

- [summarizer.py](python/source/summarizer.py) - the script that does the summarization itself
- [site handlers](src/SiteHandler) - directory with classes that extract the text from the site
- [handler command](src/Command/ReplyToPostsCommand.php) - the class that handles the bot loop itself, meaning it ties
  all the parts together and is the entry point
- all the stuff for building and deploying the project:
  - [Dockerfile](Dockerfile) - creates the docker image that runs the bot
    - You can also use it as an always-up-to-date reference on how to make the bot work on your local computer
  - [serverless.yaml](serverless.yaml) - contains configuration for deploying the docker image to AWS Lambda
  - [publish.yaml](.github/workflows/publish.yaml) - publishes the source code to AWS Lambda on every push 

## Libraries used

*Only the important ones are listed*

- [sumy](https://pypi.org/project/sumy/)
- [rikudou/lemmy-api](https://packagist.org/packages/rikudou/lemmy-api)
- [Symfony](https://symfony.com/)
