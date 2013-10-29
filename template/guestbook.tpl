{combine_css path=$GUESTBOOK_PATH|@cat:"template/style.css"}
{combine_script id="livevalidation" load="footer" path=$GUESTBOOK_PATH|@cat:"template/livevalidation.min.js"}

{footer_script require='jquery,livevalidation'}
{if !$comment_add.IS_LOGGED}
var author = new LiveValidation('author', {ldelim} onlyOnSubmit: true });
author.add(Validate.Presence, {ldelim} failureMessage: "{'Please enter your username'|@translate}" });
{/if}

{if $add_comment.EMAIL_MANDATORY and (!$add_comment.IS_LOGGED or empty($add_comment.EMAIL))}
var email = new LiveValidation('email', {ldelim} onlyOnSubmit: true });
email.add(Validate.Presence, {ldelim} failureMessage: "{'Please enter your e-mail'|@translate}" });
email.add(Validate.Email, {ldelim} failureMessage: "{'mail address must be like xxx@yyy.eee (example : jack@altern.org)'|@translate}" });
{/if}

var website = new LiveValidation('website', {ldelim} onlyOnSubmit: true });
website.add(Validate.Format, {ldelim} pattern: /^https?:\/\/[^\s\/$.?#].[^\s]*$/i,
  failureMessage: "{'invalid website address'|@translate}"});

var content = new LiveValidation('contentid', {ldelim} onlyOnSubmit: true });
content.add(Validate.Presence, {ldelim} failureMessage: "{'Please enter a message'|@translate}" });
  
jQuery("#addComment").hide();
jQuery("#guestbookAdd").css('width','180px');
jQuery("#expandForm").click(function() {ldelim}
  jQuery("#guestbookAdd").animate({ldelim}"width": "550px"}, function() {ldelim}
    jQuery("#expandForm").slideUp();
    jQuery("#addComment").slideDown("slow");
  });
});

jQuery("#website").on('blur', function() {
  var val = $(this).val();
  if (val.substr(0, 4) != 'http') {
    $(this).val('http://'+ val);
  }
});
{/footer_script}

{if $comment_add.ACTIVATE_RATING}
  {combine_script id="jquery.raty" path=$GUESTBOOK_PATH|@cat:"template/jquery.raty/jquery.raty.min.js"}
  {footer_script}
  jQuery("#comment_rate").raty({ldelim}
    path: "{$ROOT_URL}{$GUESTBOOK_PATH}template/jquery.raty/",
    half: true
  });
  {/footer_script}
{/if}


{* <!-- Menubar & titrePage --> *}
{if $themeconf.name == "stripped" or $themeconf.parent == "stripped"}
  {include file=$ABS_GUESTBOOK_PATH|@cat:'template/themes/stripped.tpl'}
  {assign var="clear" value="true"}
{elseif $themeconf.name == "simple-grey" or $themeconf.parent == "simple"}
  {include file=$ABS_GUESTBOOK_PATH|@cat:'template/themes/simple.tpl'}
  {assign var="clear" value="true"}
{else}
  {include file=$ABS_GUESTBOOK_PATH|@cat:'template/themes/default.tpl'}
{/if}

{if isset($errors) or not empty($infos)}
{include file='infos_errors.tpl'}
{/if}


{if isset($comment_add)}
<div id="guestbookAdd">
  <h4 id="expandForm">{'Sign the guestbook'|@translate}</h4>
  <form method="post" action="{$comment_add.F_ACTION}" id="addComment" class="contact">
    <table>
    {if not $comment_add.IS_LOGGED or empty($comment_add.EMAIL)}
      <tr>
        <td>
          <label for="author">{'Author'|@translate}* :</label>
        {if $comment_add.IS_LOGGED}
          {$comment_add.AUTHOR}
          <input type="hidden" name="author" value="{$comment_add.AUTHOR}">
        {else}
          <input type="text" name="author" id="author" value="{$comment_add.AUTHOR}">
        {/if}
        </td>
        <td>
          <label for="email">{'Email address'|@translate}{if $comment_add.EMAIL_MANDATORY}*{/if} ({'not publicly visible'|@translate}) :</label>
          <input type="text" name="email" id="email" value="{$comment_add.EMAIL}">
        </td>
      </tr>
    {/if}
      <tr>
      {if $comment_add.ACTIVATE_RATING}
        <td>
          <label>{'Rate'|@translate} :</label>
          <span id="comment_rate"></span>
        </td>
      {/if}
        <td>
          <label for="website">{'Website'|@translate} :</label>
          <input type="text" name="website" id="website" value="{$comment_add.WEBSITE}">
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <label for="contentid">{'Comment'|@translate}* :</label>
          <textarea name="content" id="contentid" rows="7">{$comment_add.CONTENT}</textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="submit" value="{'Send'|@translate}"> 
          {'* : mandatory fields'|@translate}
        </td>
      </tr>
    </table>
    
    <input type="hidden" name="key" value="{$comment_add.KEY}">
  </form>
</div>
{/if}

<p class="comment_count">{'There are %d messages'|@translate|@sprintf:$COMMENT_COUNT}</p>

{if isset($comments)}
<div id="guestbookCommentList">
  {if !empty($navbar)}
    <div id="pictureCommentNavBar">
      {include file='navigation_bar.tpl'|@get_extent:'navbar'}
    </div>
  {/if}
  {include file=$ABS_GUESTBOOK_PATH|@cat:'template/comment_list.tpl'}
</div>
{/if}

{if $clear}<div style="clear: both;"></div>
</div>{/if}
</div>{* <!-- content --> *}
