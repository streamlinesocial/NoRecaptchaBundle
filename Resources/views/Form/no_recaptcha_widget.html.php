<?php if ($no_recaptcha_enabled): ?>
    <div class="g-recaptcha" data-sitekey="<?php echo $public_key ?>"></div>
    <script src='<?php echo $url_api ?>'></script>
<?php endif ?>
