<?php namespace App\Models;

use Auth;
use App\Models\Comment;
use App\Models\User;

class CommentsRenderer {

	public $parents     = [];
	public $children    = [];
	public $theComments = [];

    function __construct($comments)
    {
        foreach ($comments as $c)
        {
			$comment = Comment::find($c);
			$child   = Comment::where('parent_id', '=', $c)->get();

        	$this->theComments[] = $comment->id;

        	if (count($child) > 0)
        	{
        		foreach ($child as $c)
        		{
        			$this->theComments[] = $c->id;

        			$this->getChildsComments($c->id);
        		}
        	}
        }

        foreach ($this->theComments as $c)
        {
        	$comment = Comment::find($c);
            if ($comment->parent_id === '0')
            {
            	$this->parents[$comment->id][] = $comment;
            }
            else
            {
                $this->children[$comment->parent_id][] = $comment;
            }
        }
    }

    private function getChildsComments($c)
    {
			$child = Comment::where('parent_id', '=', $c)->get();

        	if (count($child) > 0)
        	{
        		foreach ($child as $c)
        		{
        			$this->theComments[] = $c->id;
        			$this->getChildsComments($c->id);
        		}
        	}
    }

    public function print_comments()
    {
    	$output  = '';

    	if (count($this->parents) > 0)
    	{
	    	foreach ($this->parents as $comment)
	    	{
	    		$comment = $comment[0];
				$user    = User::find($comment->user_id);
				$output .= '<div class="comment-container">';
				$output .= '<div class="comment parent"><div class="links"">';

		    	if (Auth::check())
		    	{
			    	if (Auth::user()->upvotes()->where('comment_id', '=', $comment->id)->count() !== 0)
			    	{
						$output .= '<a onclick="upvoteComment(this, '. $comment->id .')" class="comment-to-upvote-'. $comment->id .' color-upvoted upvoted-'. $comment->id .'"><i class="ion-arrow-up-a"></i></a>';
			    	}
			    	else
			    	{
			    		$output .= '<a onclick="upvoteComment(this, '. $comment->id .')" class="comment-to-upvote-'. $comment->id .'"><i class="ion-arrow-up-a"></i></a>';
			    	}

			    	if (Auth::user()->downvotes()->where('comment_id', '=', $comment->id)->count() !== 0)
			    	{
			    		$output .= '<a onclick="downvoteComment(this, '. $comment->id .')" class="comment-to-downvote-'. $comment->id .' color-downvoted downvoted-'. $comment->id .'"><i class="ion-arrow-down-a"></i></a>';
			    	}
			    	else
			    	{
			    		$output .= '<a onclick="downvoteComment(this, '. $comment->id .')" class="comment-to-downvote-'. $comment->id .'"><i class="ion-arrow-down-a"></i></a>';
			    	}
		    	}
		    	else
		    	{
		    		$output .= '<a href="/login"><i class="ion-arrow-up-a"></i></a><a href="/login"><i class="ion-arrow-down-a"></i></a>';
		    	}

				$output .= ' <span id="pointsCountComment-'. $comment->id .'">'. $comment->points .'</span> <span><small>points</small></span> ';
				$output .= Timeago::convert($comment->created_at) . ' by';
				$output .= '<a href="/user/' . $user->username . '">' . $user->username . '</a>';

		    	$output .= '</div>';
		    	$output .= '<div class="content">'. ($comment->deleted == true ? '<p><i>[ this comment was deleted ]</i></p>' : $comment->content);
				$output .= '<div class="actions">';

				if ($comment->deleted == false)
				{
					$output .= '<a onclick="replyToComment('. $comment->id .')">reply</a>';
				}
				else
				{
					$output .= '<div class="divider"></div>';
				}

		    	if (Auth::check() === true && $user->id == Auth::user()->id && $comment->deleted == false)
		    	{
		    		$output .= '&nbsp;&nbsp;&nbsp;<a onclick="editComment('. $comment->id .')">edit</a>';
		    		$output .= '&nbsp;&nbsp;&nbsp;<a onclick="deleteComment('. $comment->id .')">delete</a>';
		    	}

		    	$output .= '</div></div>';
				$output .= '<form action="/comment/' . $comment->post_id . '/' . $comment->id . '" method="post" class="hide" id="reply-box-' . $comment->id . '" onsubmit="showLoader(' . $comment->id . ')">';
				$output .= '<input type="hidden" value="' . csrf_token() . '" name="_token">';
				$output .= '<div class="row"><div class="small-12 medium-6 columns"><p style="margin:0;margin-bottom:2.5px;font-size:0.7em;">you can use <a href="" target="_blank">GitHub Flavored Markdown</a></p>';
				$output .= '<textarea name="content" class="form-control comment-textarea"></textarea></div></div>';
				$output .= '<input type="submit" class="btn btn-blue" id="submitCommentBtn-' . $comment->id . '" value="Comment"> <img id="loader-' . $comment->id . '" src="/img/icons/loader.svg" class="hide">';
				$output .= '<br><br></form>';

				$output .= '<form action="/edit/comment/' . $comment->id . '" method="post" class="hide" id="edit-box-' . $comment->id . '" onsubmit="showEditLoader(' . $comment->id . ')">';
				$output .= '<input type="hidden" value="' . csrf_token() . '" name="_token">';
				$output .= '<div class="row"><div class="small-12 medium-6 columns"><p style="margin:0;margin-bottom:2.5px;font-size:0.7em;">you can use <a href="" target="_blank">GitHub Flavored Markdown</a></p>';
				$output .= '<textarea name="content" class="form-control comment-textarea">' . $comment->markdown . '</textarea></div></div>';
				$output .= '<input type="submit" class="btn btn-blue" id="editCommentBtn-' . $comment->id . '" value="Save"> <img id="editLoader-' . $comment->id . '" src="/img/icons/loader.svg" class="hide">';
				$output .= '<br><br></form>';

		    	$output .= $this->print_children($comment->id, 'child');
		    	$output .= '</div></div>';
		    }
    	}
    	else
    	{
    		$output = '<p>There are no comments in this thread yet.</p>';
    	}

    	echo $output;
    }

    private function print_children($parent, $hierachy)
    {
		$output = '';
    	if (isset($this->children[$parent]))
    	{
    		foreach($this->children[$parent] as $comment)
    		{
				$user    = User::find($comment->user_id);
				$output .= '<div class="comment-container" style="padding-left:1.25em">';
				$output .= '<div class="comment ' . $hierachy . '">';
				$output .= '<div class="links"">';

		    	if (Auth::check())
		    	{
			    	if (Auth::user()->upvotes()->where('comment_id', '=', $comment->id)->count() !== 0)
			    	{
						$output .= '<a onclick="upvoteComment(this, '. $comment->id .')" class="comment-to-upvote-'. $comment->id .' color-upvoted upvoted-'. $comment->id .'"><i class="ion-arrow-up-a"></i></a>';
			    	}
			    	else
			    	{
			    		$output .= '<a onclick="upvoteComment(this, '. $comment->id .')" class="comment-to-upvote-'. $comment->id .'"><i class="ion-arrow-up-a"></i></a>';
			    	}

			    	if (Auth::user()->downvotes()->where('comment_id', '=', $comment->id)->count() !== 0)
			    	{
			    		$output .= '<a onclick="downvoteComment(this, '. $comment->id .')" class="comment-to-downvote-'. $comment->id .' color-downvoted downvoted-'. $comment->id .'"><i class="ion-arrow-down-a"></i></a>';
			    	}
			    	else
			    	{
			    		$output .= '<a onclick="downvoteComment(this, '. $comment->id .')" class="comment-to-downvote-'. $comment->id .'"><i class="ion-arrow-down-a"></i></a>';
			    	}
		    	}
		    	else
		    	{
		    		$output .= '<a href="/login"><i class="ion-arrow-up-a"></i></a><a href="/login"><i class="ion-arrow-down-a"></i></a>';
		    	}

				$output .= ' <span id="pointsCountComment-'. $comment->id .'">'. $comment->points .'</span> <span><small>points</small></span> ';
				$output .= Timeago::convert($comment->created_at) . ' by';
				$output .= '<a href="/user/' . $user->username . '">' . $user->username . '</a>';

		    	$output .= '</div>';
		    	$output .= '<div class="content">'. ($comment->deleted == true ? '<p><i>[ this comment was deleted ]</i></p>' : $comment->content);
				$output .= '<div class="actions">';

				if ($comment->deleted == false)
				{
					if (Auth::check())
					{
						$output .= '<a onclick="replyToComment('. $comment->id .')">reply</a>';
					}
					else
					{
						$output .= '<a href="/login">reply</a>';
					}
				}
				else
				{
					$output .= '<div class="divider"></div>';
				}

		    	if (Auth::check() === true && $user->id == Auth::user()->id && $comment->deleted == false)
		    	{
		    		$output .= '&nbsp;&nbsp;&nbsp;<a onclick="editComment('. $comment->id .')">edit</a>';
		    		$output .= '&nbsp;&nbsp;&nbsp;<a onclick="deleteComment('. $comment->id .')">delete</a>';
		    	}

		    	$output .= '</div></div>';

				$output .= '<form action="/comment/' . $comment->post_id . '/' . $comment->id . '" method="post" class="hide" id="reply-box-' . $comment->id . '" onsubmit="showLoader(' . $comment->id . ')">';
				$output .= '<input type="hidden" value="' . csrf_token() . '" name="_token">';
				$output .= '<div class="row"><div class="small-12 medium-6 columns"><p style="margin:0;margin-bottom:2.5px;font-size:0.7em;">you can use <a href="" target="_blank">GitHub Flavored Markdown</a></p>';
				$output .= '<textarea name="content" class="form-control comment-textarea"></textarea></div></div>';
				$output .= '<input type="submit" class="btn btn-blue" id="submitCommentBtn-' . $comment->id . '" value="Comment"> <img id="loader-' . $comment->id . '" src="/img/icons/loader.svg" class="hide">';
				$output .= '<br><br></form>';

				$output .= '<form action="/edit/comment/' . $comment->id . '" method="post" class="hide" id="edit-box-' . $comment->id . '" onsubmit="showEditLoader(' . $comment->id . ')">';
				$output .= '<input type="hidden" value="' . csrf_token() . '" name="_token">';
				$output .= '<div class="row"><div class="small-12 medium-6 columns"><p style="margin:0;margin-bottom:2.5px;font-size:0.7em;">you can use <a href="" target="_blank">GitHub Flavored Markdown</a></p>';
				$output .= '<textarea name="content" class="form-control comment-textarea">' . $comment->markdown . '</textarea></div></div>';
				$output .= '<input type="submit" class="btn btn-blue" id="editCommentBtn-' . $comment->id . '" value="Save"> <img id="editLoader-' . $comment->id . '" src="/img/icons/loader.svg" class="hide">';
				$output .= '<br><br></form>';

				if ($hierachy == 'child')
				{
					$hierachy = 'parent';
				}
				else
				{
					$hierachy = 'child';
				}

		    	$output .= $this->print_children($comment->id, $hierachy);
		    	$output .= '</div></div>';
    		}
    	}
    	return $output;
    }

}