<ul class="commentsList">
{foreach from=$comments item=comment name=comment_loop}
<li class="commentElement {if $smarty.foreach.comment_loop.index is odd}odd{else}even{/if}">
  <div class="description"{if isset($comment.IN_EDIT)} style="height:200px"{/if}>
    {if isset($comment.U_DELETE) or isset($comment.U_VALIDATE) or isset($comment.U_EDIT)}
    <div class="actions" style="float:right;font-size:90%">
    {if isset($comment.U_DELETE)}
      <a href="{$comment.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">
        {'Delete'|@translate}
      </a>{if isset($comment.U_VALIDATE) or isset($comment.U_EDIT) or isset($comment.U_CANCEL)} | {/if}
    {/if}
    {if isset($comment.U_CANCEL)}
      <a href="{$comment.U_CANCEL}">
        {'Cancel'|@translate}
      </a>{if isset($comment.U_VALIDATE) or isset($comment.U_EDIT)} | {/if}
    {/if}
    {if isset($comment.U_EDIT) and !isset($comment.IN_EDIT)}
      <a class="editComment" href="{$comment.U_EDIT}#edit_comment">
        {'Edit'|@translate}
      </a>{if isset($comment.U_VALIDATE)} | {/if}
    {/if}
    {if isset($comment.U_VALIDATE)}
      <a href="{$comment.U_VALIDATE}">
        {'Validate'|@translate}
      </a>
    {/if}&nbsp;
    </div>
    {/if}
    
    {if $comment.WEBSITE}
      {assign var="author" value='<span class="commentAuthor"><a href="'|@cat:$comment.WEBSITE|@cat:'">'|@cat:$comment.AUTHOR|@cat:'</a></span>'}
    {else}
      {assign var="author" value='<span class="commentAuthor">'|@cat:$comment.AUTHOR|@cat:'</span>'}
    {/if}
    {assign var="date" value='<span class="commentDate">'|@cat:$comment.DATE|@cat:'</span>'}
    

    <div class="commentHeader">
      {'%s says on %s :'|@translate|@sprintf:$author:$date}<br>
      {if $comment.STARS}{$comment.STARS}{/if}
      {if $comment.EMAIL} <a href="mailto:{$comment.EMAIL}">{$comment.EMAIL}</a>{/if}
    </div>
    {if isset($comment.IN_EDIT)}
    <a name="edit_comment"></a>
    <form method="post" action="{$comment.U_EDIT}" id="editComment">
      <p><label>{'Edit a comment'|@translate} :</label></p>
      <p><textarea name="content" id="contenteditid" rows="5" cols="80">{$comment.CONTENT|@escape}</textarea></p>
      <p><input type="hidden" name="key" value="{$comment.KEY}">
        <input type="hidden" name="pwg_token" value="{$comment.PWG_TOKEN}">
        <input type="hidden" name="image_id" value="{$comment.IMAGE_ID|@default:$current.id}">
        <input type="submit" value="{'Submit'|@translate}">
      </p>
    </form>
    {else}
    <blockquote><div>{$comment.CONTENT}</div></blockquote>
    {/if}
  </div>
</li>
{/foreach}
</ul>
