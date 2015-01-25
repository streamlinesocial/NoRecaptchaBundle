<?php

namespace StrSocial\Bundle\NoRecaptchaBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

use GuzzleHttp\Client;
use Nietonfir\Google\ReCaptcha\ReCaptcha;
use Nietonfir\Google\ReCaptcha\Api\RequestData,
    Nietonfir\Google\ReCaptcha\Api\Response;

class TrueValidator extends ConstraintValidator
{
    protected $cache;

    /**
     * Enable recaptcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Recaptcha Private Key
     *
     * @var Boolean
     */
    protected $privateKey;

    /**
     * Request Stack
     *
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_VERIFY_SERVER = 'www.google.com';

    /**
     * Construct.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct($enabled, $privateKey, RequestStack $requestStack)
    {
        $this->enabled = $enabled;
        $this->privateKey = $privateKey;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // if recaptcha is disabled, always valid
        if (!$this->enabled) {
            return true;
        }

        // define variable for recaptcha check answer
        $remoteip   = $this->requestStack->getMasterRequest()->server->get('REMOTE_ADDR');
        $challenge  = $this->requestStack->getMasterRequest()->get('g-recaptcha-response');

        if (
            isset($this->cache[$this->privateKey]) &&
            isset($this->cache[$this->privateKey][$remoteip]) &&
            isset($this->cache[$this->privateKey][$remoteip][$challenge])
        ) {
            $cached = $this->cache[$this->privateKey][$remoteip][$challenge];
        } else {
            $cached = $this->cache[$this->privateKey][$remoteip][$challenge] = $this->checkAnswer($this->privateKey, $remoteip, $challenge);
        }

        if (!$cached) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
      * Calls an HTTP POST function to verify if the user's guess was correct
      *
      * @param string $privateKey
      * @param string $remoteip
      * @param string $challenge
      * @param array $extra_params an array of extra variables to post to the server
      *
      * @throws ValidatorException When missing remote ip
      *
      * @return Boolean
      */
    private function checkAnswer($privatekey, $remoteip, $challenge, $extra_params = array())
    {
        if ($remoteip == null || $remoteip == '') {
            throw new ValidatorException('For security reasons, you must pass the remote ip to reCAPTCHA');
        }

        // discard spam submissions
        if ($challenge == null || strlen($challenge) == 0) {
            return false;
        }

        $response = $this->httpGet(self::RECAPTCHA_VERIFY_SERVER, '/recaptcha/api/verify', array(
            'privatekey' => $privatekey,
            'remoteip'   => $remoteip,
            'challenge'  => $challenge,
        ) + $extra_params);

        if ($response->isValid()) {
            return true;
        }

        // $reCaptcha->getResponse()->getErrors();
        return false;
    }

    /**
     * Submits an HTTP GET to a reCAPTCHA server
     *
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     *
     * @return array response
     */
    private function httpGet($host, $path, $data, $port = 80)
    {
        $requestData = new RequestData(
            $data['privatekey'],        // secret
            $data['challenge'],    // user response
            $data['remoteip']       // end user IP
        );

        $reCaptcha = new ReCaptcha(new Client(), new Response());
        $reCaptcha->processRequest($requestData);

        return $reCaptcha->getResponse();
    }
}
