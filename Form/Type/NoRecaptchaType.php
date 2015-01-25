<?php

namespace StrSocial\Bundle\NoRecaptchaBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * A field for entering a recaptcha text.
 */
class NoRecaptchaType extends AbstractType
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_API_JS_SERVER     = '//www.google.com/recaptcha/api.js';

    /**
     * The public key
     *
     * @var string
     */
    protected $publicKey;

    /**
     * Enable recaptcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Language
     *
     * @var string
     */
    protected $language;

    /**
     * Construct.
     *
     * @param string $publicKey Recaptcha public key
     * @param boolean $enabled Recaptache status
     * @param string $language language or locale code
     */
    public function __construct($publicKey, $enabled, $language)
    {
        $this->publicKey = $publicKey;
        $this->enabled   = $enabled;
        $this->language  = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'no_recaptcha_enabled' => $this->enabled,
        ));

        if (!$this->enabled) {
            return;
        }

        $view->vars = array_replace($view->vars, array(
            'url_api'       => self::RECAPTCHA_API_JS_SERVER,
            'public_key'    => $this->publicKey,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound'      => false,
            'public_key'    => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'no_recaptcha';
    }

    /**
     * Gets the Javascript source URLs.
     *
     * @param string $key The script name
     *
     * @return string The javascript source URL
     */
    public function getScriptURL($key)
    {
        return isset($this->scripts[$key]) ? $this->scripts[$key] : null;
    }

    /**
     * Gets the public key.
     *
     * @return string The javascript source URL
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }
}
