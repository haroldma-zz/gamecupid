<?php namespace App\Models;

use Auth;
use App\Models\Comment;
use App\Models\User;
use Vinkla\Hashids\Facades\Hashids;

class CommentsRenderer {

	/**
	*
	* Arrays to hold the comments' id's
	*
	**/
	public $parents     = [];
	public $children    = [];
	public $theComments = [];

    // How many parent comments to get (parent_id = 0)
    const PARENT_SUB_LIMIT = 100;

    // How many child comments to get
    const CHILD_SUB_LIMIT = 10;

    // How much should the child limit decreased for each nest load
    const CHILD_NEST_SUBTRACT = 1;

	/**
	*
	* The constructer function fetches all the comments and their child comments
	* of a given array of id's
	* See App\Models\Invite @ renderComments()
	*
	**/
    function __construct($comments, $sort)
    {
        foreach ($comments as $comment)
        {
            $limit = CommentsRenderer::PARENT_SUB_LIMIT;
            $child   = $comment->sortChildComments($sort, 1, CommentsRenderer::CHILD_SUB_LIMIT);

        	$this->theComments[] = $comment;

            if (count($child) > 0)
            {
                foreach ($child as $c)
                {
                    if ($limit == 0) break;
                    $limit--;

                    $this->theComments[] = $c;
                    $this->getChildsComments($c, $sort, 1, 1);
                }
            }

        }

        foreach ($this->theComments as $comment)
        {
            if ($comment->parent_id == 0)
            {
            	$this->parents[$comment->id][] = $comment;
            }
            else
            {
                $this->children[$comment->parent_id][] = $comment;
            }
        }
    }

    /**
     *
     * Function to get id's of all child comments of a given comment id
     *
     **/
    private function getChildsComments($c, $sort, $page, $nesting)
    {
        $nestingLimit = CommentsRenderer::CHILD_SUB_LIMIT - (CommentsRenderer::CHILD_NEST_SUBTRACT * $nesting);

        if ($nestingLimit <= 0) return;

        $child = $c->sortChildComments($sort, $page, $nestingLimit);

        if (count($child) > 0)
        {
            foreach ($child as $c)
            {
                $this->theComments[] = $c;

                $nesting++;
                $this->getChildsComments($c, $sort, 1, $nesting);
            }
        }
    }


    /**
    *
    * This function builds the markup for child comments
    *
    **/
    private function print_children($parent, $hierachy)
    {
		$output = '';
    	if (isset($this->children[$parent]))
    	{
    		foreach($this->children[$parent] as $comment)
    		{
	    		$comment = $comment;
				$user    = User::find($comment->user_id);
				$output .= '<article class="comment ' . $hierachy . ' ' . ($comment->children->count() > 0 ? 'no-pad-bot' : '') . '">';
				$output .= '<header>';
				$output .= '<div class="voters">';
				$output .= '<div class="arrows">';
				$output .= '<div id="comment-upvoter" data-comment-id="' . $comment->id . '">';
				$output .= '<i class="ion-arrow-up-a ' . ($comment->isUpvoted() ? 'activated' : '') . ' " id="comment-upvoter-' . $comment->id . '"></i>';
				$output .= '</div>';
				$output .= '<div id="comment-downvoter" data-comment-id="' . $comment->id . '">';
				$output .= '<i class="ion-arrow-down-a ' . ($comment->isDownvoted() ? 'activated' : '') . ' " id="comment-downvoter-' . $comment->id . '"></i>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="img"></div>';
				$output .= '<div class="user-meta">';
				$output .= '<h6>';
				$output .= '<a href="' . url('/') . '">' . $comment->user->username . '</a>';
				$output .= '</h6>';
				$output .= '<p>';
				$output .= Timeago::convert($comment->created_at);
				$output .= '&nbsp;';
				$output .= '&middot;';
				$output .= '&nbsp;';
				$output .= '<span id="voteCountComment-' . $comment->id . '">' . $comment->upvoteCount() - $comment->downvoteCount() . '</span> points';
				$output .= '</p>';
				$output .= '</div>';
				$output .= '</header>';
				$output .= '<section class="markdown-text">';
				$output .= ($comment->deleted == true ? '<i>[ this comment was deleted ]</i>' : $comment->self_text);
				$output .= '</section>';
				$output .= '<footer>';
				$output .= '<a id="replyToComment" data-id="' . $comment->id . '">reply</a>';
				$output .= '<a>&middot;</a>';
				$output .= '<a>' . $comment->children->count() . ' comments</a>';
				$output .= '</footer>';
				$output .= '<div class="comment-box" id="commentBox-' . $comment->id . '">';
				$output .= '<form method="POST" action="' . url('/invite/' . Hashids::encode($comment->invite->id) . '/' . $comment->invite->slug) . '" accept-charset="UTF-8">';
				$output .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
				$output .= '<input type="hidden" name="parent_id" value="' . $comment->id . '">';
				$output .= '<input type="hidden" name="invite_id" value="' . $comment->invite->id . '">';
				$output .= '<label for="self_text">You can use Markdown to write comments.</label>';
				$output .= '<textarea name="self_text" class="form-control" placeholder="Write a comment"></textarea>';
				$output .= '<button type="submit" class="btn primary medium">Reply</button>';
				$output .= '</form>';
				$output .= '</div>';
				$output .= '<div class="children">';
		    	$output .= $this->print_children($comment->id, ($hierachy == 'child' ? 'parent' : 'child'));
				$output .= '</div>';
				$output .= '</article>';
    		}
    	}
    	return $output;
    }


    /**
    *
    * Finally print out the markup of all the comments (parents & children)
    *
    **/
    public function print_comments()
    {
    	$output  = '';

    	if (count($this->parents) > 0)
    	{
	    	foreach ($this->parents as $comment)
	    	{
	    		$comment = $comment[0];
				$user    = User::find($comment->user_id);
				$output .= '<article class="comment parent ' . ($comment->children->count() > 0 ? 'no-pad-bot' : '') . '">';
				$output .= '<header>';
				$output .= '<div class="voters">';
				$output .= '<div class="arrows">';
				$output .= '<div id="comment-upvoter" data-comment-id="' . $comment->id . '">';
				$output .= '<i class="ion-arrow-up-a ' . ($comment->isUpvoted() ? 'activated' : '') . ' " id="comment-upvoter-' . $comment->id . '"></i>';
				$output .= '</div>';
				$output .= '<div id="comment-downvoter" data-comment-id="' . $comment->id . '">';
				$output .= '<i class="ion-arrow-down-a ' . ($comment->isDownvoted() ? 'activated' : '') . ' " id="comment-downvoter-' . $comment->id . '"></i>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="img"></div>';
				$output .= '<div class="user-meta">';
				$output .= '<h6>';
				$output .= '<a href="' . url('/') . '">' . $comment->user->username . '</a>';
				$output .= '</h6>';
				$output .= '<p>';
				$output .= Timeago::convert($comment->created_at);
				$output .= '&nbsp;';
				$output .= '&middot;';
				$output .= '&nbsp;';
				$output .= '<span id="voteCountComment-' . $comment->id . '">' . $comment->upvoteCount() - $comment->downvoteCount() . '</span> points';
				$output .= '</p>';
				$output .= '</div>';
				$output .= '</header>';
				$output .= '<section class="markdown-text">';
				$output .= ($comment->deleted == true ? '<i>[ this comment was deleted ]</i>' : $comment->self_text);
				$output .= '</section>';
				$output .= '<footer>';
				$output .= '<a id="replyToComment" data-id="' . $comment->id . '">reply</a>';
				$output .= '<a>&middot;</a>';
				$output .= '<a>' . $comment->children->count() . ' comments</a>';
				$output .= '</footer>';
				$output .= '<div class="comment-box" id="commentBox-' . $comment->id . '">';
				$output .= '<form method="POST" action="' . url('/invite/' . Hashids::encode($comment->invite->id) . '/' . $comment->invite->slug) . '" accept-charset="UTF-8">';
				$output .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
				$output .= '<input type="hidden" name="parent_id" value="' . $comment->id . '">';
				$output .= '<input type="hidden" name="invite_id" value="' . $comment->invite->id . '">';
				$output .= '<label for="self_text">You can use Markdown to write comments.</label>';
				$output .= '<textarea name="self_text" class="form-control" placeholder="Write a comment"></textarea>';
				$output .= '<button type="submit" class="btn primary medium">Reply</button>';
				$output .= '</form>';
				$output .= '</div>';
				$output .= '<div class="children">';
				$output .= $this->print_children($comment->id, 'child');
				$output .= '</div>';
				$output .= '</article>';
		    }
    	}
    	else
    	{
    		$output = '<p>There are no comments on this invite yet.</p>';
    	}

    	echo $output;
    }
}