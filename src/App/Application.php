<?php

namespace App;

use Silex\Application as SilexApplication;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Translation\Loader\YamlFileLoader;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use App\Manager\Provider\ManagerRegistry;
use App\Manager\Provider\ManagerProvider;
use App\Manager\Provider\UserProvider;
use App\Controller\AdminControllerProvider;
use App\Controller\RootControllerProvider;
use App\Controller\TagControllerProvider;
use App\Controller\UserControllerProvider;
use App\Controller\CategoryControllerProvider;
use App\Controller\RecipieControllerProvider;

class Application extends SilexApplication
{
    private $rootDir;
    private $env;

    public function __construct($env)
    {
        $this->rootDir = __DIR__.'/../../';
        $this->env = $env;

        parent::__construct();

        $app = $this;

        // Override these values in resources/config/prod.php file
        $app['var_dir'] = $this->rootDir.'/var';
        $app['upload_dir'] = $this->rootDir.'web/upload/';
        $app['locale'] = 'fr';
        $app['http_cache.cache_dir'] = $app->share(function (Application $app) {
            return $app['var_dir'].'/http';
        });
        $app['monolog.options'] = [
            'monolog.logfile' => $app['var_dir'].'/logs/app.log',
            'monolog.name' => 'app',
            'monolog.level' => 300, // = Logger::WARNING
        ];

        $configFile = sprintf('%s/resources/config/%s.php', $this->rootDir, $env);
        if (!file_exists($configFile)) {
            throw new \RuntimeException(sprintf('The file "%s" does not exist.', $configFile));
        }
        require $configFile;

        $app->register(new HttpCacheServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());

        $app->register(new DoctrineServiceProvider());

        $app->register(new FormServiceProvider(), []);
        $app->extend('form.extensions', function($extensions, $application) {
        	if (isset($application['form.doctrine.bridge.included'])) return $extensions;
        	$application['form.doctrine.bridge.included'] = 1;

        	$mr = new ManagerRegistry(
        			null, array(), array('em'), null, null, '\\Doctrine\\ORM\\Proxy\\Proxy'
        			);
        	$mr->setContainer($application);
        	$extensions[] = new DoctrineOrmExtension($mr);

        	return $extensions;
        });

				// ORM

        $config = Setup::createAnnotationMetadataConfiguration(array(
						$this->rootDir . "/src/App/Domain"
					), $app['debug']);

        $config->addCustomStringFunction('RAND', 'App\Dao\Rand');

				$app['orm.em'] = EntityManager::create($app['db.options'], $config);
				$app['orm.qb'] = $app['orm.em']->createQueryBuilder();

        $app->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'admin' => array(
                    'pattern' => '^/',
                    'form' => array(
                        'login_path' => '/login',
                    		'check_path' => '/login_check',
                    ),
                    'logout' => true,
                		'anonymous' => true,
                		'remember_me' => array(
                				'key' => 'fXfTBXQx47GCVeE',
                				'always_remember_me' => true,
                		),
                		'users' => $app->share(function() use ($app) {
	                		$app['security.encoder.digest'] = $app->share(function ($app) {
	                			return new MessageDigestPasswordEncoder();
	                		});
	                		return new UserProvider($app);
                		}),
                ),
            ),
        ));

        // Note: As of this writing, RememberMeServiceProvider must be registered *after* SecurityServiceProvider or SecurityServiceProvider
        // throws 'InvalidArgumentException' with message 'Identifier "security.remember_me.service.secured_area" is not defined.'
        $app->register(new RememberMeServiceProvider());

        $app['security.utils'] = $app->share(function ($app) {
            return new AuthenticationUtils($app['request_stack']);
        });

        $app->register(new TranslationServiceProvider());
        $app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', $this->rootDir.'/resources/translations/fr.yml', 'fr');
            $translator->addResource('yaml', $this->rootDir.'/resources/translations/en.yml', 'en');
            return $translator;
        }));

        $app->register(new MonologServiceProvider(), $app['monolog.options']);

        $app->register(new TwigServiceProvider(), array(
            'twig.options' => array(
                'cache' => $app['debug'] ? false : $app['var_dir'].'/cache/twig',
            ),
            'twig.form.templates' => array('bootstrap_3_horizontal_layout.html.twig'),
            'twig.path' => array($this->rootDir.'/resources/views'),
        ));
        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
            $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
                $base = $app['request_stack']->getCurrentRequest()->getBasePath();

                return sprintf($base.'/'.$asset, ltrim($asset, '/'));
            }));

            $twig->addGlobal('categories', $app['category.manager']->getAll());

            return $twig;
        }));

       	// ManagerProvider
       	$app->register(new ManagerProvider());

				// Controller
				$app->mount('/', new RootControllerProvider());
				$app->mount('/admin', new AdminControllerProvider());
				$app->mount('/admin/user', new UserControllerProvider());
				$app->mount('/admin/tag', new TagControllerProvider());
				$app->mount('/admin/category', new CategoryControllerProvider());
				$app->mount('/admin/recipie', new RecipieControllerProvider());
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    public function getEnv()
    {
        return $this->env;
    }
}
