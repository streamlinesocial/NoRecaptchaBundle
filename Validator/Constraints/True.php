<?php

namespace StrSocial\Bundle\NoRecaptchaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class True extends Constraint
{
    public $message = 'This value is not a valid captcha.';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'no_recaptcha.true';
    }
}
