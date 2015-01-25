<?php

namespace StrSocial\Bundle\NoRecaptchaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class StrSocialNoRecaptchaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config as $key => $value) {
            $container->setParameter('str_social_no_recaptcha.'.$key, $value);
        }

        $this->registerWidget($container);
    }

    /**
     * Registers the form widget.
     */
    protected function registerWidget(ContainerBuilder $container)
    {
        $templatingEngines = $container->getParameter('templating.engines');

        if (in_array('php', $templatingEngines)) {
            $formRessource = 'StrSocialNoRecaptchaBundle:Form';

            $container->setParameter('templating.helper.form.resources', array_merge(
                $container->getParameter('templating.helper.form.resources'),
                array($formRessource)
            ));
        }

        if (in_array('twig', $templatingEngines)) {
            $formRessource = 'StrSocialNoRecaptchaBundle:Form:no_recaptcha_widget.html.twig';

            $container->setParameter('twig.form.resources', array_merge(
                $container->getParameter('twig.form.resources'),
                array($formRessource)
            ));
        }
    }
}
