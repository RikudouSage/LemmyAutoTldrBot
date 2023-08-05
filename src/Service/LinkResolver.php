<?php

namespace App\Service;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Rikudou\LemmyApi\DefaultLemmyApi;
use Rikudou\LemmyApi\Enum\LemmyApiVersion;
use Rikudou\LemmyApi\Exception\LemmyApiException;
use Rikudou\LemmyApi\Response\Model\Comment;
use Rikudou\LemmyApi\Response\Model\Post;

final readonly class LinkResolver
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    public function getCommentLink(Comment $comment, ?string $instance = null, ?bool &$error = null): string
    {
        $instance ??= parse_url($comment->apId, PHP_URL_HOST);
        $commentInstance = parse_url($comment->apId, PHP_URL_HOST);

        if ($instance === $commentInstance) {
            $error = false;

            return $comment->apId;
        }

        $targetInstanceApi = new DefaultLemmyApi(
            instanceUrl: "https://{$instance}",
            version: LemmyApiVersion::Version3,
            httpClient: $this->httpClient,
            requestFactory: $this->requestFactory,
        );

        try {
            $resolved = $targetInstanceApi->miscellaneous()->resolveObject($comment->apId);
            assert($resolved->comment !== null);
            $error = false;

            return "https://{$instance}/comment/{$resolved->comment->comment->id}";
        } catch (LemmyApiException) {
            $error = true;

            return $comment->apId;
        }
    }

    public function getPostLink(Post $post, ?string $instance = null): string
    {
        $instance ??= parse_url($post->apId, PHP_URL_HOST);
        $postInstance = parse_url($post->apId, PHP_URL_HOST);
        if ($instance === $postInstance) {
            return $post->apId;
        }

        $targetInstanceApi = new DefaultLemmyApi(
            instanceUrl: "https://{$instance}",
            version: LemmyApiVersion::Version3,
            httpClient: $this->httpClient,
            requestFactory: $this->requestFactory,
        );

        try {
            $resolved = $targetInstanceApi->miscellaneous()->resolveObject($post->apId);
            assert($resolved->post !== null);

            return "https://{$instance}/post/{$resolved->post->post->id}";
        } catch (LemmyApiException) {
            return $post->apId;
        }
    }
}
