<?php

namespace App\Service;

use Rikudou\LemmyApi\Enum\ListingType;
use Rikudou\LemmyApi\Enum\SortType;
use Rikudou\LemmyApi\LemmyApi;
use Rikudou\LemmyApi\Response\View\PostView;

final readonly class PostService
{
    public function __construct(
        private LemmyApi $api,
    ) {
    }

    /**
     * @return iterable<PostView>
     */
    public function getPosts(int $untilId, int $limit = 300): iterable
    {
        $community = null;
        //        $community = $this->api->community()->get('bot_playground');

        $i = 0;
        $page = 1;
        while (true) {
            ++$i;
            $posts = $this->api->post()->getPosts(
                community: $community,
                page: $page,
                sort: SortType::New,
                listingType: ListingType::All,
            );
            foreach ($posts as $post) {
                if ($post->post->id > $untilId && $i < $limit) {
                    yield $post;
                } else {
                    break 2;
                }
            }
            ++$page;
        }
    }
}
