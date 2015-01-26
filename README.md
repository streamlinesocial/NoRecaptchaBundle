# NoRecaptchaBundle

Adds Symfony2 form widget for Googles no CAPTCHA reCAPTCHA

Requred config
==============

app/config/config.yml

    str_social_no_recaptcha:
        enabled:      true
        public_key:   ...
        private_key:  ...
        locale_key:   %kernel.default_locale%

Example usage
=============

    <?php
    
    use StrSocial\Bundle\NoRecaptchaBundle\Validator\Constraints\True;
    
    $form = $this->createFormBuilder()
        ->add('recaptcha', 'no_recaptcha', array(
            'attr' => array(
                'options' => array(
                    'theme' => 'light',   // optional, light or dark
                    'type' => 'image',    // optional, image or audio
                    'callback' => 'onCaptchaComplete' // optional, function name to callback

                )
            ),
            'mapped' => false,
            'constraints' => array(
                new True()
            )
        ))
        ->add('save', 'submit', array('label' => 'SUBMIT'))
        ->getForm();

