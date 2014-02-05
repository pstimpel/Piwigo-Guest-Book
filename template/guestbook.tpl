{combine_css path=$GUESTBOOK_PATH|cat:'template/style.css'}
{combine_script id='livevalidation' load='footer' path=$GUESTBOOK_PATH|cat:'template/livevalidation.min.js'}

{footer_script require='jquery,livevalidation'}
(function() {
  {if !$comment_add.IS_LOGGED}
  var author = new LiveValidation('author', {ldelim} onlyOnSubmit: true });
  author.add(Validate.Presence, {ldelim} failureMessage: "{'Please enter your username'|translate}" });
  {/if}

  {if $comment_add.EMAIL_MANDATORY and (!$comment_add.IS_LOGGED or empty($comment_add.EMAIL))}
  var email = new LiveValidation('email', {ldelim} onlyOnSubmit: true });
  email.add(Validate.Presence, {ldelim} failureMessage: "{'Please enter your e-mail'|translate}" });
  email.add(Validate.Email, {ldelim} failureMessage: "{'mail address must be like xxx@yyy.eee (example : jack@altern.org)'|translate}" });
  {/if}

  var website = new LiveValidation('website', {ldelim} onlyOnSubmit: true });
  website.add(Validate.Format, {ldelim} pattern: /^https?:\/\/[^\s\/$.?#].[^\s]*$/i,
    failureMessage: "{'invalid website address'|translate}"});

  var content = new LiveValidation('contentid', {ldelim} onlyOnSubmit: true });
  content.add(Validate.Presence, {ldelim} failureMessage: "{'Please enter a message'|translate}" });
  
  {if $themeconf.mobile}
  var width = $(document).width()-30;
  {else}
  var width = jQuery('#guestbookAdd').parent().width();
  {/if}
  
  {if !isset($GB_OPEN)}
  jQuery('#addComment').hide();
  jQuery('#guestbookAdd').css('width', '180px');
  jQuery('#expandForm').click(function() {ldelim}
    jQuery('#guestbookAdd').animate({ 'width': Math.min(width, 580) }, function() {ldelim}
      jQuery('#expandForm').slideUp();
      jQuery('#addComment').slideDown('slow');
    });
  });
  {else}
  jQuery('#guestbookAdd').css({ 'width': Math.min(width, 580) });
  {/if}

  jQuery('#website').on('blur', function() {ldelim}
    var val = $(this).val();
    if (val.substr(0, 4) != 'http') {ldelim}
      $(this).val('http://'+ val);
    }
  });
}());
{/footer_script}

{if $comment_add.ACTIVATE_RATING}
  {combine_script id='jquery.raty' path=$GUESTBOOK_PATH|cat:'template/jquery.raty/jquery.raty.min.js'}
  {footer_script}
  jQuery('#comment_rate').raty({ldelim}
    path: '{$ROOT_URL}{$GUESTBOOK_PATH}template/jquery.raty/',
    half: true
  });
  {/footer_script}
{/if}


{if isset($comment_add)}
<div id="guestbookAdd">
  <h4 id="expandForm">{'Sign the guestbook'|translate}</h4>
  <form method="post" action="{$comment_add.F_ACTION}" id="addComment" class="contact">
  
  {if not $comment_add.IS_LOGGED or empty($comment_add.EMAIL)}
    <div class="col-50">
      <label for="author">{'Author'|translate}* :</label>
    {if $comment_add.IS_LOGGED}
      {$comment_add.AUTHOR}
      <input type="hidden" name="author" value="{$comment_add.AUTHOR}">
    {else}
      <input type="text" name="author" id="author" value="{$comment_add.AUTHOR}">
    {/if}
    </div>
    <div class="col-50">
      <label for="email">{'Email address'|translate}{if $comment_add.EMAIL_MANDATORY}*{/if} ({'not publicly visible'|translate}) :</label>
      <input type="text" name="email" id="email" value="{$comment_add.EMAIL}">
    </div>
  {/if}
  {if $comment_add.ACTIVATE_RATING}
    <div class="col-50">
      <label>{'Rate'|translate} :</label>
      <span id="comment_rate"></span>
    </div>
  {/if}
    <div class="col-50">
      <label for="website">{'Website'|translate} :</label>
      <input type="text" name="website" id="website" value="{$comment_add.WEBSITE}">
    </div>
    
    <div class="col-100">
      <label for="contentid">{'Comment'|translate}* :</label>
      <textarea name="content" id="contentid" rows="7">{$comment_add.CONTENT}</textarea>
    </div>
    
    {if isset($CRYPTO)}
      {$CRYPTO.parsed_content}
    {/if}
    {if isset($EASYCAPTCHA)}
      {$EASYCAPTCHA.parsed_content}
    {/if}
    
    <div class="col-100">
      <input type="submit" value="{'Send'|translate}"> 
      {'* : mandatory fields'|translate}
    </div>
    
    <input type="hidden" name="key" value="{$comment_add.KEY}">
  </form>
</div>
{/if}

<p class="comment_count">{'There are %d messages'|translate:$COMMENT_COUNT}</p>

{if isset($comments)}
<div id="guestbookCommentList">
  {if !empty($navbar)}
    <div id="pictureCommentNavBar">
      {include file='navigation_bar.tpl'|get_extent:'navbar'}
    </div>
  {/if}
  {include file=$ABS_GUESTBOOK_PATH|cat:'template/comment_list.tpl'}
</div>
{/if}
