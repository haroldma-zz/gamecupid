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
	public $commentContext = null;
	public $contextParent = null;

    // How many parent comments to get (parent_id = 0)
    const PARENT_SUB_LIMIT = 50;

    // How many child comments to get
    const CHILD_SUB_LIMIT = 10;

    // How much should the child limit decreased for each nest load
    const CHILD_DEPTH_SUBTRACT = 1;

	/**
	*
	* The function fetches all the comments and their child comments
	* of a given array of id's
	* See App\Models\Invite @ renderComments()
	*
	**/
    function prepareForInvite($invite, $sort, $cacheExpire)
    {
        $comments = $invite->sortParentComments($sort, 1, CommentsRenderer::PARENT_SUB_LIMIT, $cacheExpire);
        foreach ($comments as $comment)
        {
            $child   = $comment->sortChildComments($sort, CommentsRenderer::CHILD_SUB_LIMIT, $cacheExpire);

            $this->theComments[] = $comment;

            if (count($child) > 0)
            {
                foreach ($child as $c)
                {
                    $this->theComments[] = $c;
                    $this->getChildsComments($c, $sort, 1, $cacheExpire);
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

    function prepareForContext($comment, $sort, $cacheExpire, $contextDepth)
    {
        $this->commentContext = $comment;
        // first we load the requested context count
        $depth = 0;
        $context = $comment;
        while ($depth < $contextDepth) {
            if ($context->parent_id == 0)
                break;
            $context = $context->parent;
            $this->children[$context->parent_id][] = $context;
            $depth++;
        }

        if ($context != null) {
            $this->contextParent = $context;
            $this->parents[$context->id][] = $context;
            $this->children[$comment->parent_id][] = $comment;
        }
        else
            $this->parents[$comment->id][] = $comment;

        // now we load all children (the comment's thread)
        $this->loadCommentThread($comment, $sort, $cacheExpire);
    }

    private function loadCommentThread($comment, $sort, $cacheExpire)
    {
        $child = $comment->sortChildComments($sort, CommentsRenderer::CHILD_SUB_LIMIT, $cacheExpire);

        if (count($child) > 0) {
            foreach ($child as $c) {
                $this->theComments[] = $c;
                $this->getChildsComments($c, $sort, 1, $cacheExpire);
            }
        }

        foreach ($this->theComments as $comment) {
            $this->children[$comment->parent_id][] = $comment;
        }
    }

    /**
     *
     * Function to get id's of all child comments of a given comment id
     *
     **/
    private function getChildsComments($c, $sort, $limit, $cacheExpire)
    {
        $depthLimit = CommentsRenderer::CHILD_SUB_LIMIT - (CommentsRenderer::CHILD_DEPTH_SUBTRACT * $limit);

        if ($depthLimit <= 0) return;

        $child = $c->sortChildComments($sort, $depthLimit, $cacheExpire);

        if (count($child) > 0)
        {
            foreach ($child as $c)
            {
                $this->theComments[] = $c;

                $limit++;
                $this->getChildsComments($c, $sort, $limit, $cacheExpire);
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
				$output .= '<article class="comment' . ' ' . $hierachy . ' ' . ($comment->childCount() > 0 ? 'no-pad-bot' : '') . '">';
				$output .= '<div class="collapser" id="collapseComment">';
                $output .= '<span>[–]</span>';
				$output .= '</div>';
				$output .= '<div class="collapsed-content"><small><a href="">' . $comment->user->username . '</a> &middot; ' . $comment->totalVotes() . ' point' . ($comment->totalVotes() == 1 ? '' : 's') . ' <span class="comment-collapsed-child-count"></span></small></div>';
				$output .= '<header>';
				$output .= '<div class="voters">';
				$output .= '<div class="arrows">';
				$output .= '<div id="comment-upvoter" data-comment-id="' . hashId($comment->id) . '">';
				$output .= '<i class="ion-arrow-up-a ' . ($comment->isUpvoted() ? 'activated' : '') . ' " id="comment-upvoter-' . hashId($comment->id) . '"></i>';
				$output .= '</div>';
				$output .= '<div id="comment-downvoter" data-comment-id="' . hashId($comment->id) . '">';
				$output .= '<i class="ion-arrow-down-a ' . ($comment->isDownvoted() ? 'activated' : '') . ' " id="comment-downvoter-' . hashId($comment->id) . '"></i>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="img"></div>';
				$output .= '<div class="user-meta">';
				$output .= '<h6>';
				$output .= '<a href="' . url('/') . '">' . $comment->user->username . '</a>';
				$output .= '</h6>';
				$output .= '<p>';
				$output .= '<time datetime="' . $comment->created_at . '"></time>';
				$output .= '&nbsp;';
				$output .= '&middot;';
				$output .= '&nbsp;';
				$output .= '<span id="voteCountComment-' . hashId($comment->id) . '">' . $comment->totalVotes() . '</span> point' . ($comment->totalVotes() == 1 ? '' : 's');
				$output .= '</p>';
				$output .= '</div>';
				$output .= '</header>';
				$output .= '<section class="markdown-text'. ($this->commentContext != null && $this->commentContext == $comment ? ' comment-context ' : '') .'">';
				$output .= ($comment->deleted == true ? '<i>[ this comment was deleted ]</i>' : $comment->self_text);
				$output .= '</section>';
				$output .= '<footer>';
				$output .= '<a href="' . $comment->getPermalink() . '">permalink</a>';
                if ($comment->parent_id != 0) {
                    $output .= '<a>&middot;</a>';
                    $output .= '<a href="' . $comment->invite()->getPermalink() . hashId($comment->parent_id) . '">parent</a>';
                }
                $output .= '<a>&middot;</a>';
                $output .= '<a id="replyToComment" data-id="' . hashId($comment->id) . '">reply</a>';
				$output .= '</footer>';
				$output .= '<div class="comment-box" id="commentBox-' . hashId($comment->id) . '">';
				$output .= '<form method="POST" action="' . $comment->invite()->getPermalink() . '" accept-charset="UTF-8">';
				$output .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
				$output .= '<input type="hidden" name="parent_id" value="' . hashId($comment->id) . '">';
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

        if ($this->commentContext != null) {
            $output .= '<div class="infobar">you are viewing a single comment\'s thread.<p>';
            $output .= '<a href="'. $this->commentContext->invite()->getPermalink() .'">view the rest of the comments</a>&nbsp;→';
            if ($this->contextParent != null && $this->contextParent->parent_id != 0)
                $output .= '&nbsp;<a href="?context=10000">view the full context</a>&nbsp;→';
            $output .= '</p></div>';
        }

    	if (count($this->parents) > 0)
    	{
	    	foreach ($this->parents as $comment)
	    	{
	    		$comment = $comment[0];
				$output .= '<article class="comment parent ' . ($comment->childCount() > 0 ? 'no-pad-bot' : '') . '">';
				$output .= '<div class="collapser" id="collapseComment">';
				$output .= '<span>[–]</span>';
				$output .= '</div>';
				$output .= '<div class="collapsed-content"><small><a href="">' . $comment->user->username . '</a> &middot; ' . $comment->totalVotes() . ' point' . ($comment->totalVotes() == 1 ? '' : 's') . ' <span class="comment-collapsed-child-count"></span></span></small></div>';
				$output .= '<header>';
				$output .= '<div class="voters">';
				$output .= '<div class="arrows">';
				$output .= '<div id="comment-upvoter" data-comment-id="' . hashId($comment->id) . '">';
				$output .= '<i class="ion-arrow-up-a ' . ($comment->isUpvoted() ? 'activated' : '') . ' " id="comment-upvoter-' . hashId($comment->id) . '"></i>';
				$output .= '</div>';
				$output .= '<div id="comment-downvoter" data-comment-id="' . hashId($comment->id) . '">';
				$output .= '<i class="ion-arrow-down-a ' . ($comment->isDownvoted() ? 'activated' : '') . ' " id="comment-downvoter-' . hashId($comment->id) . '"></i>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="img"></div>';
				$output .= '<div class="user-meta">';
				$output .= '<h6>';
				$output .= '<a href="' . url('/') . '">' . $comment->user->username . '</a>';
				$output .= '</h6>';
				$output .= '<p>';
				$output .= '<time datetime="' . $comment->created_at . '"></time>';
				$output .= '&nbsp;';
				$output .= '&middot;';
				$output .= '&nbsp;';
                $output .= '<span id="voteCountComment-' . hashId($comment->id) . '">' . $comment->totalVotes() . '</span> point' . ($comment->totalVotes() == 1 ? '' : 's');
				$output .= '</p>';
				$output .= '</div>';
				$output .= '</header>';
				$output .= '<section class="markdown-text'. ($this->commentContext != null && $this->commentContext == $comment ? ' comment-context ' : '') .'">';
				$output .= ($comment->deleted == true ? '<i>[ this comment was deleted ]</i>' : $comment->self_text);
				$output .= '</section>';
				$output .= '<footer>';
                $output .= '<a href="' . $comment->getPermalink() . '">permalink</a>';
                if ($comment->parent_id != 0) {
                    $output .= '<a>&middot;</a>';
                    $output .= '<a href="' . $comment->invite()->getPermalink() . hashId($comment->parent_id) . '">parent</a>';
                }
                $output .= '<a>&middot;</a>';
                $output .= '<a id="replyToComment" data-id="' . hashId($comment->id) . '">reply</a>';
				$output .= '</footer>';
				$output .= '<div class="comment-box" id="commentBox-' . hashId($comment->id) . '">';
				$output .= '<form method="POST" action="' . $comment->invite()->getPermalink() . '" accept-charset="UTF-8">';
				$output .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
				$output .= '<input type="hidden" name="parent_id" value="' . hashId($comment->id) . '">';
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