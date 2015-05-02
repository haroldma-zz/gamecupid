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
        'id' => hashId($game->id),
        'title' => $game->title,
        'description' => $game->description,
        'poster' => $game->poster,
        'series' => $game->series,
        'trailer' => $game->trailer,
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
        'id' => hashId($console->id),
        'name' => $console->name,
        'description' => $console->description,
        'logo_url' => $console->logo_url,
        'release_date' => $console->release_date
    ]);
}

function inviteToDto($invite)
{
    return new Laravel5DTO([
        'id' => hashId($invite->id),
        'title' => $invite->title,
        'selfText' => $invite->self_text,
        'tagText' => $invite->tag_text,
        'maxPlayer' => $invite->max_players,
        'verifiedOnly' => $invite->verified_only,
        'isUpvoted' => $invite->isUpvoted(),
        'isDownvoted' => $invite->isDownvoted(),
        'commentCount' => $invite->commentCount(),
        'totalVotes' => $invite->totalVotes(),
        'ups' => $invite->upvoteCount(),
        'downs' => $invite->downvoteCount(),
        'permalink' => $invite->getPermalink(),
        'featured' => $invite->featured,
        'user' => userToDto($invite->user),
        'game' => gameToDto($invite->game()),
        'console' => consoleToDto($invite->console()),
        'createdAt' => $invite->created_at->format('Y-m-d H:i:s')
    ]);
}

function invitesToDtos($invites) {
    $dtos = [];

    foreach ($invites as $invite) {
        $dtos[] = inviteToDto($invite);
    }

    return new Collection($dtos);
}