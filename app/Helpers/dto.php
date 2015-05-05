<?php

use Kumuwai\DataTransferObject\Laravel5DTO;
use Illuminate\Database\Eloquent\Collection;

function userToDto($user)
{
    return new Laravel5DTO([
        'id' => hashId($user->id),
        'username' => $user->username,
    ]);
}

function gameToDto($game)
{
    return new Laravel5DTO([
        'id'           => hashId($game->id),
        'title'        => $game->title,
        'description'  => $game->description,
        'poster'       => $game->poster,
        'series'       => $game->series,
        'trailer'      => $game->trailer,
        'release_date' => $game->release_date,
    ]);
}

function gameSearchResultsToDto($games)
{
    $dto = [];

    foreach ($games as $game)
    {
        $dto[] = [
            'id'    => hashId($game->id),
            'title' => $game->title
        ];
    }

    return new Laravel5DTO($dto);
}

function consoleToDto($console)
{
    return new Laravel5DTO([
        'id'           => hashId($console->id),
        'name'         => $console->name,
        'description'  => $console->description,
        'logo_url'     => $console->logo_url,
        'release_date' => $console->release_date
    ]);
}

function postToDto($post)
{
    return new Laravel5DTO([
        'id'           => hashId($post->id),
        'title'        => $post->title,
        'selfText'     => $post->self_text,
        'tagText'      => $post->tag_text,
        'maxPlayer'    => $post->max_players,
        'verifiedOnly' => $post->verified_only,
        'isUpvoted'    => $post->isUpvoted(),
        'isDownvoted'  => $post->isDownvoted(),
        'commentCount' => $post->commentCount(),
        'totalVotes'   => $post->totalVotes(),
        'ups'          => $post->upvoteCount(),
        'downs'        => $post->downvoteCount(),
        'permalink'    => $post->getPermalink(),
        'featured'     => $post->featured,
        'user'         => userToDto($post->user),
        'game'         => gameToDto($post->game()),
        'console'      => consoleToDto($post->console()),
        'createdAt'    => $post->created_at->format('Y-m-d H:i:s')
    ]);
}

function postsToDtos($posts) {
    $dtos = [];

    foreach ($posts as $post) {
        $dtos[] = postToDto($post);
    }

    return new Collection($dtos);
}